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
        Schema::create('brands', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->string('website_url', 250)->nullable();
            $table->boolean('is_active')->default(true);
            // SEO columns (SeoMeta value object)
            $table->string('seo_title', 160)->nullable();
            $table->string('seo_description', 320)->nullable();
            $table->string('seo_robots', 50)->default('index,follow');
            $table->string('canonical_url', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
