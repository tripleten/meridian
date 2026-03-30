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
        Schema::create('shipments', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('order_id', 26)->index();

            // state: 'pending', 'processing', 'shipped', 'delivered', 'failed', 'cancelled'
            $table->string('state', 30)->default('pending');

            $table->string('carrier', 100)->nullable();
            $table->string('tracking_number', 255)->nullable();
            $table->string('tracking_url', 500)->nullable();

            $table->char('inventory_source_id', 26)->nullable();

            $table->json('shipping_address_snapshot');

            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
