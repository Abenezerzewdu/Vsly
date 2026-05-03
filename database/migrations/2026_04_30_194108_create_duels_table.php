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
        Schema::create('duels', function (Blueprint $table) {
    $table->id();

    $table->foreignId('take_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete();

    $table->foreignId('challenger_id')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->foreignId('opponent_id')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->enum('status', ['pending', 'active', 'finished'])
        ->default('pending');

    $table->foreignId('winner_id')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

    $table->unsignedTinyInteger('current_round')->default(1);
    $table->unsignedTinyInteger('total_rounds')->default(3);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duels');
    }
};
