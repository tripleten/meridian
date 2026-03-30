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
        Schema::create('product_performance', function (Blueprint $table) {
            $table->id();
            $table->char('product_id', 26)->index();
            $table->date('date')->index();

            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('add_to_cart_count')->default(0);
            $table->unsignedInteger('purchase_count')->default(0);
            $table->unsignedBigInteger('revenue')->default(0); // integer pence

            $table->timestamp('aggregated_at')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_performance');
    }
};
