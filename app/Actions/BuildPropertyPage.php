<?php

declare(strict_types=1);

// app/Actions/BuildPropertyPage.php

namespace App\Actions;

use App\Models\Property;

class BuildPropertyPage
{
    public function __invoke(Property $prop, string $channel): array
    {
        $isSales = ('sales' === $channel);
        $typeLabel = $prop->typeLabel();
        $priceText = $prop->priceTextForChannel($channel);
        $badgeText = $prop->badgeTextForChannel($channel);
        $badgeColor = 'bg-red-600';

        $galleryImages = $prop->galleryImagesLarge();
        $imageHosts = Property::hostsFromImages($galleryImages);

        // SEO
        $brand = config('app.name');
        $town = $prop->address_town ?? null;
        $beds = (int) ($prop->bedrooms ?? 0);
        $pieces = array_filter([
            'POA' !== $priceText ? $priceText : null,
            $beds ? "{$beds}-bed" : null,
            $typeLabel,
            $town,
        ]);
        $metaTitle = (count($pieces) ? implode(' ', $pieces) . ' | ' : '') . $brand;
        $metaDescription = mb_trim(
            ($prop->strapline ?? '') !== ''
            ? $prop->strapline
            : implode(' · ', array_filter([
                $beds ? "{$beds} bed" : null,
                $prop->bathrooms ? "{$prop->bathrooms} bath" : null,
                $typeLabel,
                $town,
                'POA' !== $priceText ? $priceText : null,
            ]))
        );
        $canonicalUrl = route('properties.show', [
            'channel' => $channel,
            'slug' => $prop->slug,
            'property' => $prop->slug_id,
        ]);
        $ogImage = $galleryImages[0]['url'] ?? ($prop->primaryImageUrl('large') ?: null);

        // JSON-LD
        $availability = $prop->isActive() ? 'https://schema.org/InStock' : 'https://schema.org/SoldOut';
        $priceValue = $prop->priceForChannel($channel);
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'RealEstateListing',
            'name' => $prop->address_single_line,
            'url' => $canonicalUrl,
            'image' => array_values(array_map(fn ($i) => $i['url'], $galleryImages)),
            'category' => $typeLabel,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $prop->address_single_line,
                'addressLocality' => $prop->address_town ?? null,
                'postalCode' => $prop->address_postcode ?? null,
                'addressCountry' => 'GB',
            ],
            'numberOfRooms' => (int) ($prop->bedrooms ?? 0),
            'offers' => array_filter([
                '@type' => 'Offer',
                'price' => is_numeric($priceValue) ? (float) $priceValue : null,
                'priceCurrency' => 'GBP',
                'availability' => $availability,
                ! $isSales && is_numeric($priceValue) ? 'priceSpecification' : null => ( ! $isSales && is_numeric($priceValue)) ? [
                    '@type' => 'UnitPriceSpecification',
                    'price' => (float) $priceValue,
                    'priceCurrency' => 'GBP',
                    'unitText' => mb_strtoupper($prop->rent_frequency ?? 'pcm'),
                ] : null,
            ]),
        ];

        return compact(
            'typeLabel',
            'priceText',
            'badgeText',
            'badgeColor',
            'galleryImages',
            'imageHosts',
            'metaTitle',
            'metaDescription',
            'canonicalUrl',
            'ogImage',
            'schema'
        );
    }
}
