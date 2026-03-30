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
        Schema::create('settings', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('group', 50);                 // general, seo, scripts, social, gdpr
            $table->string('key', 100);
            $table->longText('value')->nullable();
            $table->string('type', 20)->default('string'); // string|boolean|integer|json|encrypted
            $table->boolean('is_public')->default(false);  // expose to Inertia shared props
            $table->unsignedBigInteger('updated_by')->nullable(); // FK users.id
            $table->timestamps();
            $table->unique(['group', 'key']);
            $table->index('group');
            $table->index('is_public');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
