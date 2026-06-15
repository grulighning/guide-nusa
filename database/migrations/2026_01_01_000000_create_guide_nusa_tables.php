<?php
// ============================================================
// FILE: database/migrations/2024_01_01_000002_create_guide_nusa_tables.php
// ============================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Destinasi ───────────────────────────────────────
        Schema::create('destinasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('lokasi');
            $table->string('kabupaten')->nullable();
            $table->string('kategori'); // Alam, Budaya, Sejarah, Petualangan, Pantai
            $table->string('emoji')->default('🏔️');
            $table->string('warna_bg')->default('linear-gradient(135deg,#1b5e20,#388e3c)');
            $table->decimal('rating', 3, 1)->default(0);
            $table->integer('jumlah_pemandu')->default(0);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // ── Pemandu ─────────────────────────────────────────
        Schema::create('pemandus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('spesialisasi')->default('Alam');
            $table->integer('pengalaman_tahun')->default(1);
            $table->decimal('rating', 3, 1)->default(0);
            $table->integer('jumlah_tour')->default(0);
            $table->string('warna_avatar')->default('#0d5c45');
            $table->string('inisial', 3)->default('GN');
            $table->text('bio')->nullable();
            $table->json('ketersediaan')->nullable(); // [0,1,2,...] index hari
            $table->timestamps();
        });

        // ── Paket Tour ──────────────────────────────────────
        Schema::create('paket_tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pemandu_id')->nullable()->constrained('pemandus')->nullOnDelete();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->integer('harga')->default(0);
            $table->string('durasi')->default('1 Hari');
            $table->string('jam_mulai', 5)->default('07:00');
            $table->string('jam_selesai', 5)->default('18:00');
            $table->integer('max_peserta')->default(15);
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->text('catatan')->nullable();
            $table->string('status')->default('aktif'); // aktif | nonaktif | selesai
            $table->boolean('is_featured')->default(false);
            $table->string('badge')->nullable(); // '⭐ Terpopuler'
            $table->timestamps();
        });

        // ── Pivot: Paket <-> Destinasi ──────────────────────
        Schema::create('paket_destinasi', function (Blueprint $table) {
            $table->foreignId('paket_tour_id')->constrained('paket_tours')->cascadeOnDelete();
            $table->foreignId('destinasi_id')->constrained('destinasis')->cascadeOnDelete();
            $table->primary(['paket_tour_id', 'destinasi_id']);
        });

        // ── Ulasan ──────────────────────────────────────────
        Schema::create('ulasans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemandu_id')->constrained('pemandus')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nama_wisatawan');
            $table->integer('rating')->default(5);
            $table->text('komentar')->nullable();
            $table->string('destinasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ulasans');
        Schema::dropIfExists('paket_destinasi');
        Schema::dropIfExists('paket_tours');
        Schema::dropIfExists('pemandus');
        Schema::dropIfExists('destinasis');
    }
};