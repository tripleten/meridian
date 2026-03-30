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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('product_id', 26);
            $table->string('name', 250)->nullable(); // NULL = inherit parent name
            $table->string('sku', 100)->unique();
            // Price override: NULL = inherit parent base_price
            $table->unsignedBigInteger('price')->nullable();
            $table->unsignedBigInteger('compare_price')->nullable();
            $table->unsignedBigInteger('cost_price')->nullable();
            $table->decimal('weight', 8, 3)->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('visibility', ['hidden'])->default('hidden'); // variants NEVER show independently
            $table->smallInteger('sort_order')->default(0);
            // schemalessAttributes for variant-specific extras
            $table->schemalessAttributes('extra_attributes');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->index(['product_id', 'is_active']);
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
