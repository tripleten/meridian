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
        Schema::create('daily_sales_aggregates', function (Blueprint $table) {
            $table->id();
            $table->char('channel_id', 26)->nullable()->index();
            $table->date('date')->index();

            $table->unsignedInteger('order_count')->default(0);
            $table->unsignedBigInteger('gross_revenue')->default(0); // integer pence
            $table->unsignedBigInteger('net_revenue')->default(0);   // after refunds
            $table->unsignedBigInteger('tax_collected')->default(0);
            $table->unsignedBigInteger('shipping_revenue')->default(0);
            $table->unsignedBigInteger('discount_total')->default(0);
            $table->unsignedBigInteger('refund_total')->default(0);

            $table->unsignedInteger('new_customers')->default(0);
            $table->unsignedInteger('returning_customers')->default(0);

            $table->timestamp('aggregated_at')->nullable();
            $table->timestamps();

            $table->unique(['channel_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_sales_aggregates');
    }
};
