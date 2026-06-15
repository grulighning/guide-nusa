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
        Schema::table('pemandus', function (Blueprint $table) {
            $table->json('tanggal_tersedia')->nullable()->after('ketersediaan');
        });
    }

    public function down(): void
    {
        Schema::table('pemandus', function (Blueprint $table) {
            $table->dropColumn('tanggal_tersedia');
        });
    }
};
