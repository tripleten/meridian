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
        Schema::create('credit_memo_sequences', function (Blueprint $table) {
            $table->smallInteger('year')->primary()->unsigned();
            $table->unsignedInteger('next_number')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_memo_sequences');
    }
};
