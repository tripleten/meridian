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
        Schema::create('customer_tax_exemptions', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('customer_id', 26);
            $table->string('exemption_certificate', 200)->nullable();
            $table->string('reason', 200)->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->index(['customer_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_tax_exemptions');
    }
};
