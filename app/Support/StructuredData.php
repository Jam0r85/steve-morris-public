<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Collection;
use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\Schema;

class StructuredData
{
    /**
     * Build JSON-LD for the Contact page (Org + Branches; optional ContactPage).
     * Returns a full <script type="application/ld+json">…</script> tag.
     */
    public function contactPage(
        string $orgName,
        string $orgUrl,
        string $orgPhone,
        string $orgEmail,
        string $logoUrl,
        Collection $branches,
        string $pageUrl,
        bool $includeContactPage = false
    ): string {
        $orgId = mb_rtrim($orgUrl, '/') . '#org';

        $graph = new Graph();

        // Create the organization node via Graph with an identifier
        $graph->realEstateAgent('org')
            ->id($orgId)                 // sets @id
            ->name($orgName)
            ->url($orgUrl)
            ->telephone($orgPhone)
            ->email($orgEmail)
            ->logo($logoUrl);

        // Create each branch via Graph with its own identifier
        foreach ($branches as $b) {
            $branchKey = (string) $b->getKey(); // your UUID primary key
            $street = mb_trim(implode(' ', array_filter([
                $b->address_building_number,
                $b->address_building_name,
                $b->address_street ?: $b->address_line_1,
            ])));

            $addr = Schema::postalAddress()
                ->streetAddress($street ?: ($b->address_line_1 ?: null))
                ->addressLocality($b->address_town ?: null)
                ->postalCode($b->address_postcode ?: null)
                ->addressCountry($b->address_country ?: 'GB');

            if ($region = ($b->address_line_4 ?: $b->address_line_3)) {
                $addr->addressRegion($region);
            }

            $graph->realEstateAgent("branch-{$branchKey}")
                ->id(mb_rtrim($orgUrl, '/') . "#branch-{$branchKey}")     // unique @id per branch
                ->name($b->display_name)
                // parentOrganization can point to the org by @id:
                ->parentOrganization(Schema::organization()->id($orgId))
                ->address($addr)
                ->telephone($b->phone ?: null)
                ->email($b->email ?: null)
                ->url($pageUrl);
        }

        if ($includeContactPage) {
            $graph->contactPage('contact-page')
                ->id(mb_rtrim($pageUrl, '/') . '#contact-page')
                ->url($pageUrl)
                ->about(Schema::organization()->id($orgId))
                ->name('Contact');
        }

        return $graph->toScript();
    }

    public function tenantsPage(
        string $orgName,
        string $orgUrl,
        string $pageUrl,
        string $pageTitle,
        string $pageDescription,
        array $faqs,                  // Array of ['name' => ..., 'acceptedAnswer' => ['text' => ...]]
        bool $includeBreadcrumbs = false,
    ): string {
        $nowIso = now()->toAtomString();
        $orgId = mb_rtrim($orgUrl, '/') . '#org';
        $pageId = mb_rtrim($pageUrl, '/') . '#page';
        $faqId = mb_rtrim($pageUrl, '/') . '#faq';
        $crumbId = mb_rtrim($pageUrl, '/') . '#breadcrumbs';

        $graph = new Graph();

        // Organization (reused across pages)
        $graph->realEstateAgent('org')
            ->id($orgId)
            ->name($orgName)
            ->url($orgUrl);

        // WebPage
        $graph->webPage('tenants-page')
            ->id($pageId)
            ->name($pageTitle)
            ->description($pageDescription)
            ->url($pageUrl)
            ->inLanguage('en-GB')
            ->dateModified($nowIso)
            ->publisher(Schema::organization()->id($orgId));

        // Optional BreadcrumbList (only if you actually render breadcrumbs in UI)
        if ($includeBreadcrumbs) {
            $graph->breadcrumbList('tenants-breadcrumbs')
                ->id($crumbId)
                ->itemListElement([
                    Schema::listItem()->position(1)->name('Home')->item($orgUrl),
                    Schema::listItem()->position(2)->name('Lettings')->item(route('lettings')),
                    Schema::listItem()->position(3)->name('Tenants')->item($pageUrl),
                ]);
        }

        // FAQPage
        $questionThings = collect($faqs)->map(fn ($q) => Schema::question()
            ->name($q['name'])
            ->acceptedAnswer(
                Schema::answer()->text($q['acceptedAnswer']['text'])
            ))->all();

        $graph->fAQPage('tenants-faq')
            ->id($faqId)
            ->mainEntity($questionThings);

        return $graph->toScript();
    }

    public function propertiesCollectionPage(
        string $orgName,
        string $orgUrl,
        string $pageUrl,
        string $channel,                   // 'sales' | 'lettings'
        ?Collection $items = null,         // optional: a collection of Property models (first page)
        ?string $pageTitle = null,
        ?string $pageDescription = null,
    ): string {
        $orgId = mb_rtrim($orgUrl, '/') . '#org';
        $pageId = mb_rtrim($pageUrl, '/') . '#page';
        $listId = mb_rtrim($pageUrl, '/') . '#itemlist';
        $nowIso = now()->toAtomString();

        $graph = new Graph();

        // Organization
        $graph->realEstateAgent('org')
            ->id($orgId)
            ->name($orgName)
            ->url($orgUrl);

        // CollectionPage + potentialAction (search)
        $title = $pageTitle ?? ('lettings' === $channel ? 'Properties to Let' : 'Properties for Sale');
        $desc = $pageDescription ?? 'Browse available properties and filter by price, bedrooms, and features.';

        $graph->collectionPage('properties-collection')
            ->id($pageId)
            ->name($title)
            ->description($desc)
            ->url($pageUrl)
            ->inLanguage('en-GB')
            ->dateModified($nowIso)
            ->isPartOf(Schema::webSite()->url($orgUrl))
            ->potentialAction(
                Schema::searchAction()
                    // URL template uses your Livewire query param aliases:
                    ->target($pageUrl . '{?bedrooms,min_price,max_price,features,sort,incl_inactive}')
                    ->query(Schema::propertyValueSpecification()->valueName('search_term')) // harmless placeholder
            );

        // Optional ItemList of the first N properties on the page (good for SEO)
        if ($items && $items->isNotEmpty()) {
            $position = 1;
            $elements = [];

            foreach ($items as $prop) {
                // Adjust these accessors to your model
                $propUrl = route('properties.show', [
                    'channel' => $channel,
                    'slug' => $prop->slug ?? $prop->slug_id,
                    'property' => $prop->slug_id,
                ]);
                $elements[] = Schema::listItem()
                    ->position($position++)
                    ->item($propUrl);
            }

            $graph->itemList('properties-list')
                ->id($listId)
                ->itemListElement($elements);
        }

        return $graph->toScript();
    }

    public function propertyDetailPage(
        string $orgName,
        string $orgUrl,
        string $pageUrl,
        \App\Models\Property $property,
        string $channel // 'sales' | 'lettings'
    ): string {
        $orgId = mb_rtrim($orgUrl, '/') . '#org';
        $pageId = mb_rtrim($pageUrl, '/') . '#page';
        $offerId = mb_rtrim($pageUrl, '/') . '#offer';
        $itemId = mb_rtrim($pageUrl, '/') . '#residence';

        // ----- Basic text fields
        $title = $property->headline
            ?: ($property->address_single_line ?: 'Property');

        $desc = $this->firstNonEmpty([
            'lettings' === $channel ? $property->short_description_lettings : null,
            $property->short_description,
            'lettings' === $channel ? $property->full_description_lettings : null,
            $property->full_description,
        ]);
        $desc = $desc ? mb_trim(strip_tags($desc)) : null;

        // ----- Images (use whatever you have; these are common patterns)
        $images = [];
        if (method_exists($property, 'primaryImageUrl') && $property->primaryImageUrl()) {
            $images[] = $property->primaryImageUrl();
        }
        if (property_exists($property, 'primary_image_url') && $property->primary_image_url) {
            $images[] = $property->primary_image_url;
        }
        $images = array_values(array_unique(array_filter($images)));

        // ----- Address
        $street = $property->address_line1 ?: null;
        $addr = Schema::postalAddress()
            ->streetAddress($street)
            ->addressLocality($property->address_town ?: null)
            ->postalCode($property->address_postcode ?: null)
            ->addressCountry('GB');

        // ----- Residence subtype & core facts
        $residence = $this->buildResidenceNode($property)
            ->id($itemId)
            ->name($title)
            ->description($desc)
            ->address($addr);

        if ($property->bedrooms) {
            $residence->numberOfRooms((int) $property->bedrooms);
        }
        if ($property->bathrooms) {
            $residence->numberOfBathroomsTotal((int) $property->bathrooms);
        }
        if ($property->receptions) {
            $residence->numberOfRooms((int) $property->bedrooms + (int) $property->receptions);
        }

        if ( ! empty($images)) {
            $residence->image($images);
        }

        // Amenities (from features JSON array of strings)
        if ( ! empty($property->features)) {
            $features = is_array($property->features) ? $property->features : json_decode((string) $property->features, true);
            if (is_array($features) && ! empty($features)) {
                // Provide as keywords; you could also map to amenitiesFeature if you track structured values
                $residence->keywords(implode(', ', array_unique(array_filter(array_map('strval', $features)))));
            }
        }

        // EPC (rating A–G)
        if ( ! empty($property->epc_rating)) {
            $residence->energyEfficiencyScaleMax('A'); // optional hints
            $residence->energyEfficiencyScaleMin('G');
            $residence->energyEfficiencyRating($property->epc_rating);
        }

        // Council tax (band + amount if present)
        if ( ! empty($property->council_tax_band) || ! empty($property->council_tax_cost)) {
            $ctLabel = mb_trim(implode(' ', array_filter([
                'Council Tax', $property->council_tax_band ? "Band {$property->council_tax_band}" : null,
            ])));
            $val = Schema::propertyValue()->name($ctLabel);
            if ( ! empty($property->council_tax_cost)) {
                $val->value((float) $property->council_tax_cost)->unitText('GBP_PER_YEAR');
            }
            $residence->additionalProperty($val);
        }

        // ----- Offer / pricing
        $offer = Schema::offer()->id($offerId)->itemOffered($residence)->priceCurrency('GBP');

        if ('sales' === $channel) {
            if ( ! empty($property->price_sales)) {
                $offer->price((float) $property->price_sales);
            }
        } else { // lettings
            if ( ! empty($property->price_lettings)) {
                // Prefer UnitPriceSpecification for rent frequency
                $unit = $this->rentUnitText($property->rent_frequency); // PER_MONTH / PER_WEEK / PER_DAY
                $offer->priceSpecification(
                    Schema::unitPriceSpecification()
                        ->price((float) $property->price_lettings)
                        ->priceCurrency('GBP')
                        ->unitText($unit)
                );
                // Also set plain price for broader parsers
                $offer->price((float) $property->price_lettings);
            }
            // Deposit
            if ( ! empty($property->deposit)) {
                $offer->advanceBookingRequirement(
                    Schema::priceSpecification()
                        ->price((float) $property->deposit)
                        ->priceCurrency('GBP')
                        ->name('Tenancy deposit')
                );
            }
            // Furnishing
            if ( ! empty($property->furnished)) {
                $offer->itemOffered(
                    // repeat the same residence but with furnishing flag as additionalProperty
                    $residence->additionalProperty(
                        Schema::propertyValue()->name('Furnished')->value($property->furnished)
                    )
                );
            }
        }

        // Availability
        $availabilityUrl = null;
        if (1 === (int) $property->is_active) {
            $availabilityUrl = 'https://schema.org/InStock';
        } elseif ( ! empty($property->status)) {
            // Map common statuses
            $availabilityUrl = match (mb_strtolower($property->status)) {
                'let agreed', 'under offer', 'sold stc', 'sold subject to contract' => 'https://schema.org/PreOrder',
                'let', 'sold' => 'https://schema.org/OutOfStock',
                default => null,
            };
        }
        if ($availabilityUrl) {
            $offer->availability($availabilityUrl);
        }

        // Geo
        if ( ! empty($property->lat) && ! empty($property->lng)) {
            $residence->geo(
                Schema::geoCoordinates()
                    ->latitude((float) $property->lat)
                    ->longitude((float) $property->lng)
            );
        }

        // ----- Graph assembly
        $graph = new Graph();

        // Org
        $graph->realEstateAgent('org')
            ->id($orgId)
            ->name($orgName)
            ->url($orgUrl);

        // WebPage
        $graph->webPage('property-page')
            ->id($pageId)
            ->name($title)
            ->description($desc)
            ->url($pageUrl)
            ->inLanguage('en-GB')
            ->dateModified(now()->toAtomString())
            ->isPartOf(Schema::webSite()->url($orgUrl));

        // Residence + Offer (explicit add so they appear as separate nodes)
        $graph->add($residence);
        $graph->add($offer);

        return $graph->toScript();
    }

    /**
     * Choose the best Residence subtype based on your property_type/style.
     * Falls back to Residence.
     */
    private function buildResidenceNode(\App\Models\Property $p)
    {
        $type = mb_strtolower((string) ($p->property_type ?? ''));
        $style = mb_strtolower((string) ($p->property_style ?? ''));

        // Very lightweight mapping — extend as needed
        if (str_contains($type, 'apartment') || str_contains($type, 'flat')) {
            return Schema::apartment();
        }
        if (str_contains($type, 'bungalow')) {
            return Schema::singleFamilyResidence();
        }
        if (str_contains($type, 'detached') || str_contains($type, 'semi') || str_contains($type, 'terraced') || str_contains($type, 'house')) {
            return Schema::house();
        }
        if (str_contains($type, 'studio')) {
            return Schema::apartment(); // still an apt subtype
        }

        // Fallback
        return Schema::residence();
    }

    /**
     * Map your rent_frequency to a UnitPriceSpecification::unitText
     */
    private function rentUnitText(?string $freq): string
    {
        $freq = mb_strtolower((string) $freq);

        return match ($freq) {
            'weekly', 'week', 'pw' => 'PER_WEEK',
            'daily', 'day', 'pd' => 'PER_DAY',
            default => 'PER_MONTH', // monthly / pcm / null
        };
    }

    /**
     * First non-empty helper
     */
    private function firstNonEmpty(array $candidates): ?string
    {
        foreach ($candidates as $c) {
            if (is_string($c) && '' !== mb_trim($c)) {
                return $c;
            }
        }

        return null;
    }
}
