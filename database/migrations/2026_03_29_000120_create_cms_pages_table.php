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
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('channel_id', 26)->nullable()->index();

            $table->string('title', 500);
            $table->string('url_key', 255)->index();
            $table->longText('content')->nullable(); // HTML/Markdown

            // state: 'draft', 'published', 'archived'
            $table->string('state', 30)->default('draft');

            // SEO meta (embedded value object)
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('meta_keywords', 500)->nullable();
            $table->boolean('meta_robots_noindex')->default(false);

            $table->json('layout_config')->nullable(); // page builder / layout settings

            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['channel_id', 'url_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_pages');
    }
};
