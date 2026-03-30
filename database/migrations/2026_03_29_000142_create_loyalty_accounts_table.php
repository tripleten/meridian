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
        Schema::create('loyalty_accounts', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('customer_id', 26)->unique();

            $table->unsignedInteger('points_balance')->default(0);
            $table->unsignedInteger('points_lifetime')->default(0); // never decremented

            // tier: 'bronze', 'silver', 'gold', 'platinum'
            $table->string('tier', 20)->default('bronze');

            $table->timestamp('tier_reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_accounts');
    }
};
