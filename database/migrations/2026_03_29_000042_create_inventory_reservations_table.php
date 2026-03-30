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
        Schema::create('inventory_reservations', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('product_variant_id', 26);
            $table->char('source_id', 26);
            $table->enum('reference_type', ['cart', 'order']);
            $table->char('reference_id', 26);                   // cart.id or order.id
            $table->unsignedInteger('qty');
            $table->enum('status', ['reserved', 'committed', 'released'])->default('reserved');
            $table->timestamp('expires_at')->nullable();         // cart reservations expire
            $table->timestamps();

            $table->foreign('product_variant_id')->references('id')->on('product_variants')->cascadeOnDelete();
            $table->foreign('source_id')->references('id')->on('inventory_sources')->cascadeOnDelete();
            $table->index(['reference_type', 'reference_id']);
            $table->index(['product_variant_id', 'status']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_reservations');
    }
};
