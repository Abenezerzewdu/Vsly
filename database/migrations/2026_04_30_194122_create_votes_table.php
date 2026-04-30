<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('votes', function (Blueprint $table) {
    $table->id();

    $table->foreignId('duel_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('voted_for')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->timestamps();

    // One vote per user per duel
    $table->unique(['duel_id', 'user_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
