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
        Schema::create('customers', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->unsignedBigInteger('user_id')->unique();   // FK to users.id
            $table->char('customer_group_id', 26)->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 30)->nullable();
            $table->string('company', 150)->nullable();
            $table->string('vat_number', 30)->nullable();
            $table->boolean('vat_validated')->nullable();       // null=unchecked, 0=invalid, 1=valid
            $table->timestamp('vat_validated_at')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'prefer_not_to_say'])->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_subscribed_to_newsletter')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->nullOnDelete();
            $table->index('is_active');
            $table->index('customer_group_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
