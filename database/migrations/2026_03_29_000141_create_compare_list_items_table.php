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
        Schema::create('compare_list_items', function (Blueprint $table) {
            $table->char('id', 26)->primary();

            // Identified by either customer or anonymous session
            $table->char('customer_id', 26)->nullable()->index();
            $table->string('session_id', 100)->nullable()->index();

            $table->char('product_id', 26);

            $table->timestamps();

            $table->unique(['customer_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compare_list_items');
    }
};
