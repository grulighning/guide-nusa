<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('destination_guide') || ! Schema::hasTable('destinasis') || ! Schema::hasTable('pemandus')) {
            return;
        }

        Schema::create('destination_guide', function (Blueprint $table) {
            $table->foreignId('destination_id')->constrained('destinasis')->cascadeOnDelete();
            $table->foreignId('guide_id')->constrained('pemandus')->cascadeOnDelete();
            $table->primary(['destination_id', 'guide_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destination_guide');
    }
};
