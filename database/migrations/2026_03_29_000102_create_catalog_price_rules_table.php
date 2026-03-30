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
        Schema::create('catalog_price_rules', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->char('channel_id', 26)->nullable();
            $table->json('customer_group_ids')->nullable();
            $table->json('category_ids')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed', 'to_fixed'])->default('percentage');
            $table->decimal('discount_amount', 10, 4);
            $table->boolean('is_active')->default(true);
            $table->smallInteger('priority')->default(0);
            $table->boolean('stop_further_rules')->default(false);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index('priority');
            $table->index(['valid_from', 'valid_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_price_rules');
    }
};
