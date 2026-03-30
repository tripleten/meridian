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
        Schema::create('inventory_sources', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('name', 100);
            $table->string('code', 50)->unique();              // main-warehouse, store-london
            $table->enum('type', ['warehouse', 'store', 'dropship'])->default('warehouse');
            $table->string('address_line1', 200)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country_code', 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->smallInteger('priority')->default(0);      // pick order for multi-source
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_sources');
    }
};
