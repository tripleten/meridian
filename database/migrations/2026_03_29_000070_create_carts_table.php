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
        Schema::create('carts', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('session_id', 100)->nullable()->index(); // guest carts
            $table->unsignedBigInteger('user_id')->nullable();       // logged-in carts
            $table->char('customer_id', 26)->nullable();
            $table->char('channel_id', 26)->nullable();
            $table->char('currency_code', 3)->default('GBP');
            $table->decimal('exchange_rate', 14, 6)->default(1.0);
            $table->string('coupon_code', 50)->nullable();
            $table->json('applied_rule_ids')->nullable();            // cart rule IDs applied
            $table->string('locale', 5)->default('en_GB');
            $table->boolean('is_guest')->default(true);
            $table->timestamp('abandoned_at')->nullable();
            $table->timestamp('recovered_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->foreign('customer_id')
                ->references('id')->on('customers')
                ->nullOnDelete();

            $table->foreign('channel_id')
                ->references('id')->on('channels')
                ->nullOnDelete();

            $table->index('user_id');
            $table->index('abandoned_at');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
