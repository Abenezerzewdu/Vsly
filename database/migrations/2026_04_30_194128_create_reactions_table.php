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
        Schema::create('reactions', function (Blueprint $table) {
    $table->id();

    $table->foreignId('take_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->enum('type', ['fire', 'weak', 'clean', 'dodged']);

    $table->timestamps();

    // Prevent spam reactions
    $table->unique(['take_id', 'user_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
