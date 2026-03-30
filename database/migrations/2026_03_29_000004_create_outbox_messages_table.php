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
        Schema::create('outbox_messages', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('aggregate_type', 100);
            $table->char('aggregate_id', 26);
            $table->string('event_type', 200);
            $table->json('payload');
            $table->timestamp('occurred_at');
            $table->timestamp('dispatched_at')->nullable(); // NULL = not yet relayed
            $table->tinyInteger('attempts')->default(0);
            // Partial index concept: query for undispatched rows efficiently
            $table->index(['dispatched_at', 'occurred_at'], 'idx_undispatched');
            $table->index(['aggregate_type', 'aggregate_id'], 'idx_aggregate');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbox_messages');
    }
};
