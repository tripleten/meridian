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
        Schema::create('cms_blocks', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('channel_id', 26)->nullable()->index();

            // identifier is used in templates/widgets: e.g. 'header-promo-banner'
            $table->string('identifier', 100);
            $table->string('title', 500);
            $table->longText('content')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['channel_id', 'identifier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_blocks');
    }
};
