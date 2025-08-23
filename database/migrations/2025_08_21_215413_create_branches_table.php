<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table): void {
            $table->uuid('uuid')->primary();

            $table->string('name')->nullable();
            $table->string('public_name')->nullable();
            $table->string('email_address')->nullable();
            $table->string('telephone')->nullable();
            $table->string('website')->nullable();

            // Address fields
            $table->string('address_single_line')->nullable();
            $table->string('address_anon_single_line')->nullable();
            $table->string('address_building_number')->nullable();
            $table->string('address_building_name')->nullable();
            $table->string('address_street')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('address_line_3')->nullable();
            $table->string('address_line_4')->nullable();
            $table->string('address_town')->nullable();
            $table->string('address_country')->nullable();
            $table->string('address_postcode')->nullable();
            $table->string('address_udprn')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
