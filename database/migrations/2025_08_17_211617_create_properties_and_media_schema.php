<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        // ---------------------------
        // PROPERTIES
        // ---------------------------
        Schema::create('properties', function (Blueprint $t): void {
            $t->id();

            // Provider / sync
            $t->uuid('provider_id');                         // Street UUID
            $t->boolean('is_active')->default(true);
            $t->timestampTz('provider_updated_at')->nullable();
            $t->timestampTz('first_seen_at')->nullable();
            $t->timestampTz('last_seen_at')->nullable();

            // Channel / status
            $t->string('listing_category', 20)->nullable();  // 'sales' | 'lettings'
            $t->string('status', 50)->nullable();

            // Sales pricing
            $t->unsignedInteger('price_sales')->nullable();
            $t->string('price_qualifier', 50)->nullable();
            $t->boolean('display_price')->nullable();

            // Lettings pricing
            $t->unsignedInteger('price_lettings')->nullable();
            $t->string('rent_frequency', 20)->nullable();    // pcm/ppw
            $t->unsignedInteger('deposit')->nullable();
            $t->string('furnished', 50)->nullable();

            // EPC
            $t->string('epc_rating', 5)->nullable();

            // Counts / type
            $t->unsignedTinyInteger('bedrooms')->nullable();
            $t->unsignedTinyInteger('bathrooms')->nullable();
            $t->unsignedTinyInteger('receptions')->nullable();
            $t->string('property_type', 100)->nullable();
            $t->string('property_style', 100)->nullable();

            // Address
            $t->string('address_line1')->nullable();
            $t->string('address_town')->nullable();
            $t->string('address_postcode', 16)->nullable();
            $t->string('address_single_line')->nullable();
            $t->decimal('lat', 10, 7)->nullable();
            $t->decimal('lng', 10, 7)->nullable();

            // SEO
            $t->string('slug')->nullable();
            $t->string('slug_id', 16)->nullable();

            // Content
            $t->string('headline')->nullable();
            $t->longText('full_description')->nullable();
            $t->longText('full_description_lettings')->nullable();
            $t->text('short_description')->nullable();
            $t->text('short_description_lettings')->nullable();
            $t->json('features')->nullable();

            // Details / extras
            $t->string('council_tax_band', 10)->nullable();
            $t->unsignedInteger('council_tax_cost')->nullable();
            $t->unsignedInteger('service_charge')->nullable();
            $t->unsignedInteger('ground_rent')->nullable();
            $t->date('lease_expiry_date')->nullable();
            $t->string('heating_system', 191)->nullable();

            $t->timestampsTz();

            // Indexes
            $t->index('provider_id', 'properties_provider_id_index');
            $t->index('listing_category', 'properties_listing_category_index');
            $t->index('is_active', 'properties_is_active_index');
            $t->index('last_seen_at', 'properties_last_seen_at_index');
            $t->index('provider_updated_at', 'properties_provider_updated_at_index');

            $t->index('bedrooms', 'properties_bedrooms_index');
            $t->index('address_town', 'properties_town_index');
            $t->index('address_postcode', 'properties_postcode_index');

            $t->index('epc_rating', 'properties_epc_rating_index');
            $t->index('council_tax_band', 'properties_council_tax_band_index');
            $t->index('lease_expiry_date', 'properties_lease_expiry_date_index');

            $t->index('slug', 'properties_slug_index');
            $t->unique('slug_id', 'properties_slug_id_unique');

            // Composite filters
            $t->index(['listing_category', 'is_active', 'bedrooms'], 'properties_listing_active_bedrooms_idx');
            $t->index(['address_postcode', 'listing_category', 'is_active'], 'properties_postcode_cat_active_idx');
        });

        // ---------------------------
        // PROPERTY_MEDIA
        // ---------------------------
        Schema::create('property_media', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('property_id')->constrained('properties')->cascadeOnDelete();

            $t->string('category', 50)->default('photo');   // photo|floorplan|epc
            $t->string('url');                              // canonical
            $t->integer('sort_order')->nullable();
            $t->unsignedInteger('width')->nullable();
            $t->unsignedInteger('height')->nullable();

            // Metadata / variants
            $t->boolean('is_image')->nullable();
            $t->string('media_type')->nullable();           // image/video/pdf/...
            $t->string('title')->nullable();

            $t->string('url_thumbnail')->nullable();
            $t->string('url_small')->nullable();
            $t->string('url_medium')->nullable();
            $t->string('url_large')->nullable();
            $t->string('url_hero')->nullable();
            $t->string('url_full')->nullable();

            $t->timestampsTz();

            // Indexes (for idempotency and render performance)
            $t->index(['property_id', 'url'], 'property_media_property_url_idx');
            $t->index(['property_id', 'category'], 'property_media_property_category_idx');
            $t->index(['property_id', 'category', 'sort_order'], 'property_media_property_category_sort_idx');
        });

        // ---------------------------
        // PROPERTY_SLUG_REDIRECTS
        // ---------------------------
        Schema::create('property_slug_redirects', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $t->string('old_slug');
            $t->timestampsTz();

            $t->unique('old_slug', 'property_slug_redirects_old_slug_unique');
            $t->index('property_id', 'property_slug_redirects_property_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_slug_redirects');
        Schema::dropIfExists('property_media');
        Schema::dropIfExists('properties');
    }
};
