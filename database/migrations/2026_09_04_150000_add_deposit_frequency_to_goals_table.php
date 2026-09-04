<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->enum('deposit_frequency', ['none', 'daily', 'weekly', 'monthly', 'one_time'])->default('none');
            $table->string('phone_number')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropColumn(['deposit_frequency', 'phone_number']);
        });
    }
};
