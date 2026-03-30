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
        Schema::create('transactions', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('order_id', 26)->index();
            $table->char('payment_method_id', 26)->nullable();

            // type: 'authorise', 'capture', 'refund', 'void'
            $table->string('type', 30);

            // state: 'pending', 'success', 'failed', 'cancelled'
            $table->string('state', 30)->default('pending');

            // Amount in base currency integer pence/cents
            $table->unsignedBigInteger('amount');
            $table->char('currency_code', 3);

            // Gateway identifiers
            $table->string('gateway_transaction_id', 255)->nullable()->index();
            $table->string('gateway_payment_intent_id', 255)->nullable(); // Stripe PaymentIntent
            $table->json('gateway_response')->nullable(); // raw response payload

            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
