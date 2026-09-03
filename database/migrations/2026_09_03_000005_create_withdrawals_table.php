<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('goal_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->text('reason');
            $table->integer('viability_score')->nullable();
            $table->enum('decision', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('decision_quality', ['safe', 'bad', 'neutral'])->default('neutral');
            $table->text('ai_summary')->nullable();
            $table->text('user_notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'goal_id', 'decision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
