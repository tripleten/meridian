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
        Schema::create('orders', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('number', 30)->unique();              // ORD-2026-00001
            $table->char('channel_id', 26)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->char('customer_id', 26)->nullable();
            $table->string('customer_email', 200);
            $table->string('status', 30)->default('pending_payment');
            $table->string('payment_status', 30)->default('pending');

            // Currency
            $table->char('base_currency', 3)->default('GBP');
            $table->char('order_currency', 3)->default('GBP');
            $table->decimal('exchange_rate_snapshot', 14, 6)->default(1.0);

            // Totals in ORDER currency (smallest unit)
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('shipping_amount')->default(0);
            $table->unsignedBigInteger('tax_amount')->default(0);
            $table->unsignedBigInteger('grand_total');

            // Totals in BASE currency (for accounting)
            $table->unsignedBigInteger('base_subtotal');
            $table->unsignedBigInteger('base_grand_total');
            $table->unsignedBigInteger('base_tax_amount')->default(0);

            // Amounts already refunded
            $table->unsignedBigInteger('total_refunded')->default(0);

            // Coupon
            $table->string('coupon_code', 50)->nullable();
            $table->json('applied_rule_ids')->nullable();

            // Payment
            $table->string('payment_method', 50)->nullable();    // stripe, paypal, bank_transfer
            $table->char('payment_method_id', 26)->nullable();

            // Shipping
            $table->string('shipping_method', 100)->nullable();
            $table->string('shipping_carrier', 50)->nullable();

            // UTM attribution
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('utm_content', 100)->nullable();
            $table->string('utm_term', 100)->nullable();

            // Snapshots (immutable at order time)
            $table->json('shipping_address_snapshot');
            $table->json('billing_address_snapshot');
            $table->json('pricing_snapshot');                    // line item prices, discounts, totals
            $table->json('tax_snapshot');                        // full TaxBreakdown at placement
            $table->json('customer_snapshot');                   // name, email, group at placement

            // Invoice
            $table->string('invoice_number', 50)->nullable();    // INV-2026-00001
            $table->timestamp('invoiced_at')->nullable();

            // VAT
            $table->string('customer_vat_number', 30)->nullable();
            $table->boolean('vat_number_valid')->nullable();

            // Notes
            $table->text('customer_note')->nullable();

            $table->timestamp('placed_at')->nullable();
            $table->timestamps();

            $table->foreign('channel_id')
                ->references('id')->on('channels')
                ->nullOnDelete();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->foreign('customer_id')
                ->references('id')->on('customers')
                ->nullOnDelete();

            // payment_method_id is a soft reference only — payment data is snapshotted in
            // the payment_method (string) column; no hard FK to avoid dependency ordering.
            $table->index('payment_method_id');

            $table->index('status');
            $table->index('payment_status');
            $table->index('customer_id');
            $table->index(['status', 'placed_at']);
            $table->index('channel_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
