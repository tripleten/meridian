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
        Schema::create('order_refunds', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('order_id', 26)->index();

            // Gapless credit memo number (SELECT FOR UPDATE from credit_memo_sequences)
            $table->string('credit_memo_number', 30)->unique()->nullable();

            // state: pending, approved, processed, rejected
            $table->string('state', 30)->default('pending');

            // All amounts in base currency integer pence/cents
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('tax_amount');
            $table->unsignedBigInteger('shipping_amount')->default(0);
            $table->unsignedBigInteger('total');

            $table->json('items_snapshot'); // [{order_item_id, qty, amount}]

            $table->string('reason', 500)->nullable();

            // Payment gateway refund reference
            $table->string('gateway_refund_id', 255)->nullable();

            $table->unsignedBigInteger('processed_by')->nullable(); // users.id
            $table->timestamp('processed_at')->nullable();

            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_refunds');
    }
};
