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
        Schema::create('return_requests', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('order_id', 26)->index();
            $table->char('customer_id', 26)->nullable()->index();

            // state: 'pending', 'approved', 'rejected', 'received', 'closed'
            $table->string('state', 30)->default('pending');

            // reason: 'wrong_item', 'damaged', 'not_as_described', 'changed_mind', 'other'
            $table->string('reason', 50);
            $table->text('customer_notes')->nullable();
            $table->text('staff_notes')->nullable();

            $table->json('items'); // [{order_item_id, quantity, reason}]

            $table->char('linked_refund_id', 26)->nullable(); // order_refunds.id

            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_requests');
    }
};
