<?php

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Database\Migrations
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('code', 50)->unique();           // e.g. 'color', 'size', 'pack_qty'
            $table->string('label', 100);                    // e.g. 'Colour', 'Size', 'Pack Quantity'
            $table->enum('input_type', ['text', 'select', 'multiselect', 'boolean', 'date', 'price', 'textarea'])
                  ->default('text');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_filterable')->default(false);  // show in layered navigation
            $table->boolean('is_searchable')->default(false);  // include in search index
            $table->boolean('is_variant_axis')->default(false); // used to create product variants
            $table->smallInteger('sort_order')->default(0);
            $table->json('options')->nullable();               // for select/multiselect: [{"value":"red","label":"Red"}]
            $table->timestamps();
            $table->index('is_filterable');
            $table->index('is_variant_axis');
        });

        // Pivot: attribute_set_attribute
        Schema::create('attribute_set_attribute', function (Blueprint $table) {
            $table->char('attribute_set_id', 26);
            $table->char('attribute_id', 26);
            $table->string('group_name', 100)->default('General'); // tab grouping in admin
            $table->smallInteger('sort_order')->default(0);
            $table->primary(['attribute_set_id', 'attribute_id']);
            $table->foreign('attribute_set_id')->references('id')->on('attribute_sets')->cascadeOnDelete();
            $table->foreign('attribute_id')->references('id')->on('attributes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_set_attribute');
        Schema::dropIfExists('attributes');
    }
};
