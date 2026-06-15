<?php
// ============================================================
// FILE: database/migrations/2024_01_01_000001_add_role_to_users_table.php
// Jalankan SETELAH migration users bawaan Breeze
// ============================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('wisatawan')->after('email'); // 'pemandu' | 'wisatawan'
            $table->string('phone')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone']);
        });
    }
};
