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
        Schema::create('coupons', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('code', 50)->unique();
            $table->string('description', 200)->nullable();
            $table->enum('type', ['single_use', 'multi_use', 'per_customer']);
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_limit_per_customer')->nullable();
            $table->unsignedInteger('times_used')->default(0);
            $table->char('cart_rule_id', 26)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index('is_active');
            $table->index('cart_rule_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
