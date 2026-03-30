<?php

declare(strict_types=1);

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
        Schema::create('order_items', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('order_id', 26)->index();
            $table->char('product_id', 26)->nullable();
            $table->char('variant_id', 26)->nullable();

            // Snapshot of product at time of order
            $table->string('sku', 100);
            $table->string('name', 500);
            $table->json('product_snapshot'); // full product snapshot

            $table->unsignedSmallInteger('quantity');

            // Pricing (all in base currency, integer pence/cents)
            $table->unsignedBigInteger('unit_price');          // before discounts/tax
            $table->unsignedBigInteger('unit_price_incl_tax'); // after tax
            $table->unsignedBigInteger('row_total');           // quantity * unit_price
            $table->unsignedBigInteger('row_total_incl_tax');
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('tax_amount');
            $table->decimal('tax_rate', 5, 4)->default(0)->unsigned(); // e.g. 0.2000 = 20%

            $table->unsignedBigInteger('quantity_refunded')->default(0);
            $table->unsignedBigInteger('refunded_amount')->default(0);

            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
