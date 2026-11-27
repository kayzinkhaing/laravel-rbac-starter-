<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_data_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_data_category_id')
                ->constrained('master_data_categories')
                ->restrictOnDelete();

            $table->string('key', 100);
            $table->string('value', 255);
            $table->string('label', 255)->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(
                ['master_data_category_id', 'key'],
                'mdi_category_key_unique'
            );
            $table->index(
                ['master_data_category_id', 'is_active', 'sort_order'],
                'mdi_category_active_sort_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_data_items');
    }
};
