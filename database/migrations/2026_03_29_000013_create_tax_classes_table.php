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
        Schema::create('tax_classes', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('name', 100);                   // Standard, Reduced, Zero Rate, Digital Services
            $table->string('code', 50)->unique();           // standard, reduced, zero, digital
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_classes');
    }
};
