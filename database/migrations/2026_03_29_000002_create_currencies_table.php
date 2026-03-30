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
        Schema::create('currencies', function (Blueprint $table) {
            $table->char('code', 3)->primary();          // ISO 4217
            $table->string('name', 50);
            $table->string('symbol', 5);
            $table->enum('symbol_position', ['before', 'after'])->default('before');
            $table->decimal('exchange_rate', 14, 6);     // relative to base currency
            $table->boolean('is_base')->default(false);
            $table->boolean('is_active')->default(true);
            $table->tinyInteger('decimal_places')->default(2); // JPY=0, KWD=3
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
