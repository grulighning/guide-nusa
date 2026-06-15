<?php

namespace Database\Seeders;

use App\Models\Destinasi;
use App\Models\PaketTour;
use App\Models\Pemandu;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ───────────────────────────────────────────
        $adminPemandu = User::create([
            'name'     => 'Bima Wijaya',
            'email'    => 'bima@guidenusa.id',
            'phone'    => '+6281234567890',
            'role'     => 'pemandu',
            'password' => Hash::make('password'),
        ]);
        $pemandu2 = User::create([
            'name'     => 'Arif Rahman',
            'email'    => 'arif@guidenusa.id',
            'phone'    => '+6285678901234',
            'role'     => 'pemandu',
            'password' => Hash::make('password'),
        ]);
        $pemandu3 = User::create([
            'name'     => 'Yoki Kusuma',
            'email'    => 'yoki@guidenusa.id',
            'phone'    => '+6287890123456',
            'role'     => 'pemandu',
            'password' => Hash::make('password'),
        ]);
        User::create([
            'name'     => 'Wisatawan Demo',
            'email'    => 'wisatawan@demo.id',
            'phone'    => '+6281122334455',
            'role'     => 'wisatawan',
            'password' => Hash::make('password'),
        ]);

        // ── Pemandu Profiles ─────────────────────────────────
        $p1 = Pemandu::create([
            'user_id'          => $adminPemandu->id,
            'spesialisasi'     => 'Fotografi, Alam',
            'pengalaman_tahun' => 3,
            'rating'           => 0,
            'jumlah_tour'      => 0,
            'warna_avatar'     => '#1a5276',
            'inisial'          => 'BW',
            'bio'              => 'Pemandu berpengalaman spesialis fotografi alam Sumatera Barat.',
            'ketersediaan'     => [0, 1, 2, 3, 5], // Sen-Kam + Sab
        ]);
        $p2 = Pemandu::create([
            'user_id'          => $pemandu2->id,
            'spesialisasi'     => 'Alam, Petualangan',
            'pengalaman_tahun' => 5,
            'rating'           => 4.6,
            'jumlah_tour'      => 120,
            'warna_avatar'     => '#1e8449',
            'inisial'          => 'AR',
            'bio'              => 'Spesialis trekking dan petualangan alam terbuka.',
            'ketersediaan'     => [1, 2, 3, 4, 6], // Sel-Jum + Min
        ]);
        $p3 = Pemandu::create([
            'user_id'          => $pemandu3->id,
            'spesialisasi'     => 'Kuliner, Budaya',
            'pengalaman_tahun' => 2,
            'rating'           => 4.4,
            'jumlah_tour'      => 45,
            'warna_avatar'     => '#7d3c98',
            'inisial'          => 'YK',
            'bio'              => 'Ahli kuliner lokal dan budaya Minangkabau.',
            'ketersediaan'     => [0, 2, 4, 5], // Sen, Rab, Jum, Sab
        ]);

        // ── Destinasi ────────────────────────────────────────
        $destinasis = [
            ['nama' => 'Gunung Marapi',     'lokasi' => 'Agam, Sumatera Barat',   'kabupaten' => 'Agam',          'kategori' => 'Alam',        'emoji' => '🏔️', 'warna_bg' => 'linear-gradient(135deg,#5d4037,#8d6e63)', 'rating' => 4.3, 'jumlah_pemandu' => 20],
            ['nama' => 'Lembah Harau',      'lokasi' => 'Lima Puluh Kota',         'kabupaten' => 'Lima Puluh Kota','kategori' => 'Alam',        'emoji' => '🌿', 'warna_bg' => 'linear-gradient(135deg,#1b5e20,#388e3c)', 'rating' => 4.9, 'jumlah_pemandu' => 15],
            ['nama' => 'Istana Pagaruyuang','lokasi' => 'Tanah Datar',             'kabupaten' => 'Tanah Datar',   'kategori' => 'Budaya',      'emoji' => '🏛️', 'warna_bg' => 'linear-gradient(135deg,#4e342e,#795548)', 'rating' => 4.5, 'jumlah_pemandu' => 50],
            ['nama' => 'Danau Kembar',      'lokasi' => 'Solok, Sumatera Barat',   'kabupaten' => 'Solok',         'kategori' => 'Alam',        'emoji' => '🏞️', 'warna_bg' => 'linear-gradient(135deg,#0d47a1,#1976d2)', 'rating' => 5.0, 'jumlah_pemandu' => 60],
            ['nama' => 'Pantai Air Manis',  'lokasi' => 'Padang',                  'kabupaten' => 'Padang',        'kategori' => 'Pantai',      'emoji' => '🌊', 'warna_bg' => 'linear-gradient(135deg,#4a148c,#7b1fa2)', 'rating' => 4.7, 'jumlah_pemandu' => 30],
            ['nama' => 'Ngarai Sianok',     'lokasi' => 'Bukittinggi',             'kabupaten' => 'Agam',          'kategori' => 'Alam',        'emoji' => '⛰️', 'warna_bg' => 'linear-gradient(135deg,#37474f,#546e7a)', 'rating' => 4.8, 'jumlah_pemandu' => 25],
            ['nama' => 'Jam Gadang',        'lokasi' => 'Bukittinggi',             'kabupaten' => 'Agam',          'kategori' => 'Sejarah',     'emoji' => '🕰️', 'warna_bg' => 'linear-gradient(135deg,#bf360c,#e64a19)', 'rating' => 4.6, 'jumlah_pemandu' => 40],
            ['nama' => 'Danau Maninjau',    'lokasi' => 'Agam, Sumatera Barat',   'kabupaten' => 'Agam',          'kategori' => 'Alam',        'emoji' => '🌅', 'warna_bg' => 'linear-gradient(135deg,#006064,#00838f)', 'rating' => 4.8, 'jumlah_pemandu' => 18],
        ];

        $created = [];
        foreach ($destinasis as $d) {
            $created[] = Destinasi::create($d);
        }

        // ── Paket Tour ───────────────────────────────────────
        $paket1 = PaketTour::create([
            'user_id'         => $adminPemandu->id,
            'pemandu_id'      => $p1->id,
            'nama'            => 'Jejak Budaya Minang',
            'deskripsi'       => 'Jelajahi kekayaan budaya Minangkabau dari Istana Pagaruyuang hingga pasar tradisional Bukittinggi dalam satu hari penuh bersama pemandu berpengalaman.',
            'harga'           => 350000,
            'durasi'          => '1 Hari',
            'jam_mulai'       => '08:00',
            'jam_selesai'     => '18:00',
            'max_peserta'     => 15,
            'tanggal_mulai'   => now()->addDays(3),
            'tanggal_selesai' => now()->addDays(3),
            'status'          => 'aktif',
            'is_featured'     => true,
            'badge'           => '⭐ Terpopuler',
        ]);
        $paket1->destinasis()->sync([$created[2]->id, $created[6]->id, $created[5]->id]);

        $paket2 = PaketTour::create([
            'user_id'         => $pemandu2->id,
            'pemandu_id'      => $p2->id,
            'nama'            => 'Petualangan Alam Harau',
            'deskripsi'       => 'Trek ringan menyusuri lembah hijau Harau, air terjun tersembunyi, dan pemandangan tebing granit yang dramatis bersama fotografer profesional.',
            'harga'           => 200000,
            'durasi'          => '1 Hari',
            'jam_mulai'       => '07:00',
            'jam_selesai'     => '18:00',
            'max_peserta'     => 20,
            'tanggal_mulai'   => now()->addDays(5),
            'tanggal_selesai' => now()->addDays(5),
            'status'          => 'aktif',
            'is_featured'     => false,
        ]);
        $paket2->destinasis()->sync([$created[1]->id, $created[0]->id]);

        $paket3 = PaketTour::create([
            'user_id'         => $pemandu3->id,
            'pemandu_id'      => $p3->id,
            'nama'            => 'Wisata Danau Kembar & Maninjau',
            'deskripsi'       => 'Menikmati keindahan dua danau vulkanik paling menakjubkan di Sumatera Barat dengan pemandangan pegunungan yang memukau.',
            'harga'           => 280000,
            'durasi'          => '2 Hari',
            'jam_mulai'       => '06:00',
            'jam_selesai'     => '20:00',
            'max_peserta'     => 12,
            'tanggal_mulai'   => now()->addDays(7),
            'tanggal_selesai' => now()->addDays(8),
            'status'          => 'aktif',
            'is_featured'     => false,
        ]);
        $paket3->destinasis()->sync([$created[3]->id, $created[7]->id]);
    }
}
