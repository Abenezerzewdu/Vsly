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
        Schema::create('rounds', function (Blueprint $table) {
    $table->id();

    $table->foreignId('duel_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->unsignedTinyInteger('round_number'); // 1, 2, 3

    $table->text('challenger_response')->nullable();
    $table->text('opponent_response')->nullable();

    $table->boolean('completed')->default(false);

    $table->timestamps();

    // Prevent duplicate rounds
    $table->unique(['duel_id', 'round_number']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rounds');
    }
};
