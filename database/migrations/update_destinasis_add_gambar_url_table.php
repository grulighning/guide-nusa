<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinasis', function (Blueprint $table) {
            if (! Schema::hasColumn('destinasis', 'gambar_url')) {
                $table->string('gambar_url')->nullable()->after('warna_bg');
            }
        });
    }

    public function down(): void
    {
        Schema::table('destinasis', function (Blueprint $table) {
            if (Schema::hasColumn('destinasis', 'gambar_url')) {
                $table->dropColumn('gambar_url');
            }
        });
    }
};
