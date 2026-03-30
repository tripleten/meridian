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
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->char('id', 26)->primary();

            $table->string('code', 50)->unique();

            // state: 'active', 'redeemed', 'expired', 'cancelled'
            $table->string('state', 30)->default('active');

            // Amounts in base currency integer pence/cents
            $table->unsignedBigInteger('initial_balance');
            $table->unsignedBigInteger('remaining_balance');

            $table->char('currency_code', 3)->default('GBP');

            $table->char('customer_id', 26)->nullable()->index();
            $table->char('order_id', 26)->nullable(); // order that generated this gift card

            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};
