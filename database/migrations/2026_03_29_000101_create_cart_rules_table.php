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
        Schema::create('cart_rules', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->char('channel_id', 26)->nullable();
            $table->json('customer_group_ids')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed_cart', 'fixed_product', 'buy_x_get_y', 'free_shipping']);
            $table->decimal('discount_amount', 10, 4)->default(0);
            $table->unsignedSmallInteger('discount_qty')->nullable();
            $table->boolean('apply_to_shipping')->default(false);
            $table->boolean('stop_rules_processing')->default(false);
            $table->json('conditions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->unsignedInteger('uses_per_coupon')->nullable();
            $table->unsignedInteger('uses_per_customer')->nullable();
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('is_active');
            $table->index('channel_id');
            $table->index(['valid_from', 'valid_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_rules');
    }
};
