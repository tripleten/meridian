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
        Schema::create('cookie_consents', function (Blueprint $table) {
            $table->char('id', 26)->primary();

            // Nullable — guest consents are linked by fingerprint only
            $table->char('customer_id', 26)->nullable()->index();
            $table->string('session_id', 100)->nullable()->index();

            // Consent choices (GDPR categories)
            $table->boolean('necessary')->default(true);   // always true
            $table->boolean('analytics')->default(false);
            $table->boolean('marketing')->default(false);
            $table->boolean('preferences')->default(false);

            // Consent metadata for audit trail
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('consent_version', 20)->nullable(); // version of cookie policy
            $table->timestamp('consented_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cookie_consents');
    }
};
