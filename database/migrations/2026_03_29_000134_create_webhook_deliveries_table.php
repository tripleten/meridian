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
        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('endpoint_id', 26)->index();

            $table->string('event_type', 100);
            $table->json('payload');

            // HTTP response
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->text('response_body')->nullable();

            $table->unsignedTinyInteger('attempt_count')->default(0);
            $table->timestamp('next_retry_at')->nullable();

            // state: 'pending', 'delivered', 'failed'
            $table->string('state', 30)->default('pending');

            $table->timestamps();

            $table->foreign('endpoint_id')->references('id')->on('webhook_endpoints')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
    }
};
