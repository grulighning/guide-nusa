<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemandu_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemandu_id')->constrained('pemandus')->cascadeOnDelete();
            $table->foreignId('wisatawan_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('paket_tour_id')->nullable()->constrained('paket_tours')->nullOnDelete();
            $table->date('tanggal_booking')->nullable();
            $table->unsignedInteger('jumlah_peserta')->default(1);
            $table->text('catatan')->nullable();
            $table->string('status')->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemandu_bookings');
    }
};
