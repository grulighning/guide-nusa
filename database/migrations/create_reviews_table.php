<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('destination_id')->constrained('destinasis')->cascadeOnDelete();
            $table->foreignId('guide_id')->constrained('pemandus')->cascadeOnDelete();
            $table->tinyInteger('rating'); // 1–5
            $table->text('comment')->nullable();
            $table->timestamps();

            // A user can only review once per destination+guide pair
            $table->unique(['user_id', 'destination_id', 'guide_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
