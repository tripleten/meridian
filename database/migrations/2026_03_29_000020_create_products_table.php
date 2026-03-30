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
        Schema::create('products', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('brand_id', 26)->nullable();
            $table->char('attribute_set_id', 26)->nullable();
            $table->char('tax_class_id', 26)->nullable();

            $table->enum('type', ['simple', 'configurable', 'bundle', 'virtual', 'downloadable'])->default('simple');
            $table->string('name', 250);
            $table->string('slug', 270)->unique();
            $table->string('url_key', 270)->unique();
            $table->string('sku', 100)->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();

            // Pricing (base price only — full waterfall in Pricing module)
            $table->unsignedBigInteger('base_price')->default(0);       // pence/cents
            $table->unsignedBigInteger('compare_price')->nullable();     // crossed-out RRP
            $table->unsignedBigInteger('cost_price')->nullable();        // for margin reporting

            // Dimensions & shipping
            $table->decimal('weight', 8, 3)->nullable();
            $table->string('weight_unit', 5)->default('kg');
            $table->decimal('length', 8, 3)->nullable();
            $table->decimal('width', 8, 3)->nullable();
            $table->decimal('height', 8, 3)->nullable();

            // Status & visibility
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->boolean('is_purchasable')->default(true); // false for configurable parents
            $table->enum('visibility', ['hidden', 'catalog', 'search', 'catalog_search'])->default('catalog_search');

            // Product flags
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_new')->default(false);
            $table->date('new_from_date')->nullable();
            $table->date('new_to_date')->nullable();

            // SEO (SeoMeta value object embedded)
            $table->string('seo_title', 160)->nullable();
            $table->string('seo_description', 320)->nullable();
            $table->string('seo_robots', 50)->default('index,follow');
            $table->string('canonical_url', 500)->nullable();
            $table->string('og_title', 160)->nullable();
            $table->string('og_description', 320)->nullable();
            $table->string('og_image_url', 500)->nullable();

            // Flexible extra attributes (spatie/laravel-schemaless-attributes)
            $table->schemalessAttributes('extra_attributes');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('brand_id')->references('id')->on('brands')->nullOnDelete();
            $table->foreign('attribute_set_id')->references('id')->on('attribute_sets')->nullOnDelete();
            $table->foreign('tax_class_id')->references('id')->on('tax_classes')->nullOnDelete();

            $table->index('status');
            $table->index('visibility');
            $table->index('type');
            $table->index('is_featured');
            $table->index(['status', 'visibility']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
