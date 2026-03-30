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
        Schema::create('price_lists', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->char('channel_id', 26)->nullable();
            $table->char('customer_group_id', 26)->nullable();
            $table->char('currency_code', 3)->default('GBP');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('channel_id')
                ->references('id')->on('channels')
                ->nullOnDelete();

            $table->foreign('customer_group_id')
                ->references('id')->on('customer_groups')
                ->nullOnDelete();

            $table->foreign('currency_code')
                ->references('code')->on('currencies')
                ->restrictOnDelete();

            $table->index('channel_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_lists');
    }
};
