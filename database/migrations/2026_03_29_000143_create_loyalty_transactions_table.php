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
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('account_id', 26)->index();

            // type: 'earn', 'redeem', 'adjust', 'expire'
            $table->string('type', 20);

            // Positive = earned, negative = redeemed/expired
            $table->integer('points');

            $table->unsignedInteger('balance_after');

            // Polymorphic source: order, admin_adjustment, etc.
            $table->string('source_type', 100)->nullable();
            $table->char('source_id', 26)->nullable();
            $table->index(['source_type', 'source_id']);

            $table->string('description', 500)->nullable();

            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('loyalty_accounts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
    }
};
