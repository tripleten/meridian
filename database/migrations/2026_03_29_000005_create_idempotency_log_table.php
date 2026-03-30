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
        Schema::create('idempotency_log', function (Blueprint $table) {
            $table->string('idempotency_key', 200)->primary();
            $table->timestamp('processed_at');
            $table->timestamp('expires_at');
            $table->index('expires_at');  // for cleanup job
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idempotency_log');
    }
};
