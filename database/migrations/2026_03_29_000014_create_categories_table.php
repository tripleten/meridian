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
        Schema::create('categories', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('parent_id', 26)->nullable();
            // Nested set columns for kalnoy/nestedset (one global tree)
            $table->unsignedInteger('_lft')->default(0);
            $table->unsignedInteger('_rgt')->default(0);
            $table->unsignedInteger('depth')->default(0);

            $table->string('name', 200);
            $table->string('slug', 220)->unique();
            $table->string('url_key', 220)->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('sort_mode', ['position', 'name_asc', 'price_asc', 'price_desc', 'newest'])
                  ->default('position');
            $table->smallInteger('position')->default(0);

            // SEO columns (SeoMeta value object embedded)
            $table->string('seo_title', 160)->nullable();
            $table->string('seo_description', 320)->nullable();
            $table->string('seo_robots', 50)->default('index,follow');
            $table->string('canonical_url', 500)->nullable();
            $table->string('og_title', 160)->nullable();
            $table->string('og_description', 320)->nullable();
            $table->string('og_image_url', 500)->nullable();

            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('categories')->nullOnDelete();
            $table->index(['_lft', '_rgt']);
            $table->index('is_active');
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
