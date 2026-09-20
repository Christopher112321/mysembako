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
        // Roles table sudah ada, hanya perlu update seeder
        // Tidak perlu perubahan struktur tabel
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
