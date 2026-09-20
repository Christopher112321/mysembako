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
        // Foreign key constraint akan tetap ada setelah rename column
        // Migration ini untuk memastikan constraint sudah benar
        // Jika constraint sudah ada, tidak perlu melakukan apa-apa
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
