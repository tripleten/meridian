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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->char('id', 26)->primary();

            // code: 'stripe', 'paypal', 'bank_transfer'
            $table->string('code', 50)->unique();
            $table->string('name', 255);

            $table->boolean('is_enabled')->default(false);

            // Encrypted gateway credentials / config (JSON)
            $table->text('config')->nullable();

            // Display order in checkout
            $table->unsignedSmallInteger('sort_order')->default(0);

            // Minimum and maximum order total (in base currency pence)
            $table->unsignedBigInteger('min_order_total')->nullable();
            $table->unsignedBigInteger('max_order_total')->nullable();

            // Allowed customer groups (JSON array of group IDs); null = all
            $table->json('allowed_customer_groups')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
