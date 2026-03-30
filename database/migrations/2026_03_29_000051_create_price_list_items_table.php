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
        Schema::create('price_list_items', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('price_list_id', 26);
            $table->char('product_id', 26)->nullable();
            $table->char('product_variant_id', 26)->nullable(); // more specific overrides product
            $table->unsignedBigInteger('price');                // in pence/cents
            $table->unsignedBigInteger('compare_price')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();

            $table->foreign('price_list_id')
                ->references('id')->on('price_lists')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->cascadeOnDelete();

            $table->foreign('product_variant_id')
                ->references('id')->on('product_variants')
                ->cascadeOnDelete();

            $table->index(['price_list_id', 'product_id', 'product_variant_id'], 'pli_list_product_variant_idx');
            $table->index(['price_list_id', 'valid_from', 'valid_until'], 'pli_list_dates_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_list_items');
    }
};
