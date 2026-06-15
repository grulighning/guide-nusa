<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinasis', function (Blueprint $table) {
            if (! Schema::hasColumn('destinasis', 'fotos')) {
                $table->json('fotos')->nullable()->after('gambar_url');
            }
            if (! Schema::hasColumn('destinasis', 'pemandu_id')) {
                $table->foreignId('pemandu_id')->nullable()->constrained('pemandus')->nullOnDelete()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('destinasis', function (Blueprint $table) {
            $table->dropColumn(['fotos', 'pemandu_id']);
        });
    }
};
