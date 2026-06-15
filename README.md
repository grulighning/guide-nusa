# Guide Nusa — Laravel 12 + Blade + Breeze

Platform pemandu wisata berbasis web untuk Sumatera Barat.

---

## 🚀 Setup Instalasi

### 1. Buat project Laravel 12 baru
```bash
composer create-project laravel/laravel guide-nusa
cd guide-nusa
```

### 2. Install Laravel Breeze
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
```

### 3. Copy semua file dari repo ini ke dalam project Laravel
Salin folder-folder berikut ke project Laravel kamu sesuai strukturnya.

### 4. Konfigurasi database (.env)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=guide_nusa
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan migrasi & seeder
```bash
php artisan migrate
php artisan db:seed
```

### 6. Jalankan server
```bash
php artisan serve
```

---

## 📁 Struktur Project

```
guide-nusa/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php
│   │   │   ├── DestinasiController.php
│   │   │   ├── PaketController.php
│   │   │   └── PemanduController.php
│   │   └── Middleware/
│   └── Models/
│       ├── User.php
│       ├── Destinasi.php
│       ├── PaketTour.php
│       └── Pemandu.php
├── database/
│   ├── migrations/
│   │   ├── create_destinasis_table.php
│   │   ├── create_pemandus_table.php
│   │   └── create_paket_tours_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php       ← Layout utama (navbar)
│       ├── components/
│       │   ├── dest-card.blade.php
│       │   └── pemandu-card.blade.php
│       ├── dashboard/
│       │   └── index.blade.php
│       ├── destinasi/
│       │   └── index.blade.php
│       ├── paket/
│       │   ├── index.blade.php
│       │   └── create.blade.php
│       ├── pemandu/
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       └── auth/ (dihandle Breeze)
└── routes/
    └── web.php
```

---

## 🔐 Role User

| Role       | Akses                              |
|------------|------------------------------------|
| `pemandu`  | Dashboard, buat/kelola paket tour  |
| `wisatawan`| Lihat & booking paket tour         |

Role disimpan di kolom `role` pada tabel `users`.

---

## 📦 Halaman yang Ada

- `/` → Dashboard (hero + destinasi populer + tour aktif + daftar pemandu)
- `/destinasi` → Daftar destinasi + filter + search
- `/paket` → Daftar paket tour + filter
- `/paket/create` → Form buat paket tour (khusus pemandu)
- `/pemandu` → Daftar pemandu aktif
- `/pemandu/{id}` → Detail profil pemandu
- `/register` → Registrasi (Breeze, dengan role selector)
- `/login` → Login (Breeze)
