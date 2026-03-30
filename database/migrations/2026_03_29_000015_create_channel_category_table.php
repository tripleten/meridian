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
        // Per-channel VISIBILITY pivot — NOT a separate tree (one global tree in categories table)
        Schema::create('channel_category', function (Blueprint $table) {
            $table->char('channel_id', 26);
            $table->char('category_id', 26);
            $table->boolean('is_visible')->default(true);
            $table->smallInteger('sort_order')->default(0);
            $table->primary(['channel_id', 'category_id']);
            $table->foreign('channel_id')->references('id')->on('channels')->cascadeOnDelete();
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('channel_category');
    }
};
