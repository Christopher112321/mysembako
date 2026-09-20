<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL for column renaming (MySQL compatible)
        DB::statement('ALTER TABLE products CHANGE COLUMN user_id owner_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE products CHANGE COLUMN nama_produk name VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE products CHANGE COLUMN harga price INTEGER NOT NULL');
        DB::statement('ALTER TABLE products CHANGE COLUMN deskripsi description TEXT NULL');
        DB::statement('ALTER TABLE products CHANGE COLUMN foto image VARCHAR(255) NULL');
        DB::statement('ALTER TABLE products CHANGE COLUMN stok stock INTEGER NOT NULL DEFAULT 0');
        
        // Add status column
        Schema::table('products', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'habis', 'disembunyikan'])->default('aktif')->after('stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        // Reverse column renaming
        DB::statement('ALTER TABLE products CHANGE COLUMN owner_id user_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE products CHANGE COLUMN name nama_produk VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE products CHANGE COLUMN price harga INTEGER NOT NULL');
        DB::statement('ALTER TABLE products CHANGE COLUMN description deskripsi TEXT NULL');
        DB::statement('ALTER TABLE products CHANGE COLUMN image foto VARCHAR(255) NULL');
        DB::statement('ALTER TABLE products CHANGE COLUMN stock stok INTEGER NOT NULL DEFAULT 0');
    }
};
