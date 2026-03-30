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
        Schema::create('channels', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->string('domain', 200)->nullable();
            $table->char('default_locale', 5)->default('en_GB');
            $table->json('supported_locales');          // ["en_GB","fr_FR"]
            $table->char('default_currency', 3)->default('GBP');
            $table->json('supported_currencies');        // ["GBP","EUR","USD"]
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
