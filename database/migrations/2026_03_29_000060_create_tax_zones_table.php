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
        Schema::create('tax_zones', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('name', 100);
            $table->string('code', 50)->unique();            // uk, eu, us, row
            $table->json('countries');                       // ["GB"] or ["AT","BE","BG","CY"...]
            $table->json('regions')->nullable();             // state/province codes or null for all
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_zones');
    }
};
