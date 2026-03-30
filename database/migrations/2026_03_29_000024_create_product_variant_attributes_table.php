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
        Schema::create('product_variant_attributes', function (Blueprint $table) {
            $table->char('variant_id', 26);
            $table->char('attribute_id', 26);
            $table->string('value', 500);
            $table->primary(['variant_id', 'attribute_id']);
            $table->foreign('variant_id')->references('id')->on('product_variants')->cascadeOnDelete();
            $table->foreign('attribute_id')->references('id')->on('attributes')->cascadeOnDelete();
            $table->index('variant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_attributes');
    }
};
