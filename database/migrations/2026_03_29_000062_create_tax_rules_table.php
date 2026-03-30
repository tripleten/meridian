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
        Schema::create('tax_rules', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('name', 100);
            $table->smallInteger('priority')->default(0);
            $table->json('tax_class_ids');                       // ["char26id1","char26id2"]
            $table->json('tax_zone_ids');                        // ["char26id1"]
            $table->json('tax_rate_ids');                        // ["char26id1"]
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_rules');
    }
};
