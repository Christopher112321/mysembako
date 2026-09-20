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
        Schema::table('orders', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['nama_produk', 'jumlah', 'total_harga', 'tanggal_pemesanan']);
        });
        
        // Rename user_id to buyer_id
        DB::statement('ALTER TABLE orders CHANGE COLUMN user_id buyer_id BIGINT UNSIGNED NOT NULL');
        
        Schema::table('orders', function (Blueprint $table) {
            // Add new columns
            $table->decimal('total_price', 15, 2)->after('buyer_id');
            $table->enum('status', ['pending', 'paid', 'shipped', 'done', 'cancel'])->default('pending')->after('total_price');
            $table->string('payment_method')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['total_price', 'status', 'payment_method']);
            $table->string('nama_produk');
            $table->integer('jumlah');
            $table->integer('total_harga');
            $table->dateTime('tanggal_pemesanan')->useCurrent();
        });
        
        // Reverse rename
        DB::statement('ALTER TABLE orders CHANGE COLUMN buyer_id user_id BIGINT UNSIGNED NOT NULL');
    }
};
