<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('iso2', 2)->unique();
            $table->string('iso3', 3)->unique();
            $table->string('name');
            $table->string('name_local')->nullable();
            $table->timestamps();
        });

        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('source_id', 40)->unique();
            $table->string('name');
            $table->string('name_local')->nullable();
            $table->string('slug')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
            $table->index(['country_id', 'name']);
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')->constrained()->cascadeOnDelete();
            $table->string('source_id', 40)->unique();
            $table->string('name');
            $table->string('name_local')->nullable();
            $table->string('slug')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
            $table->index(['state_id', 'name']);
        });

        Schema::create('townships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->string('source_id', 40)->unique();
            $table->string('name');
            $table->string('name_local')->nullable();
            $table->string('slug')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
            $table->index(['district_id', 'name']);
        });

        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('township_id')->constrained()->cascadeOnDelete();
            $table->string('source_id', 48)->unique();
            $table->string('name');
            $table->string('name_local')->nullable();
            $table->string('slug')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
            $table->index(['township_id', 'name']);
        });

        // Street names are intentionally user-maintained: the cited source does not provide a street gazetteer.
        Schema::create('streets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('township_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ward_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('name_local')->nullable();
            $table->timestamps();
            $table->index(['township_id', 'name']);
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->morphs('addressable');
            $table->foreignId('country_id')->constrained()->restrictOnDelete();
            $table->foreignId('state_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('township_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('ward_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('street_id')->nullable()->constrained()->nullOnDelete();
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('building_name')->nullable();
            $table->string('unit_number')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('landmark')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->index(['township_id', 'ward_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('streets');
        Schema::dropIfExists('wards');
        Schema::dropIfExists('townships');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('states');
        Schema::dropIfExists('countries');
    }
};
