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
        // 1. Hapus/nonaktifkan tabel data_nikah_agama_flat
        Schema::dropIfExists('data_nikah_agama_flat');

        // 2. Buat tabel faktor_perceraian (Data BPS 2023–2025)
        Schema::create('faktor_perceraian', function (Blueprint $table) {
            $table->id();
            $table->string('provinsi', 100);
            $table->integer('tahun');
            $table->string('faktor_penyebab', 150);
            $table->integer('jumlah_kasus')->default(0);
            $table->timestamps();

            // Indexing untuk performa pencarian & filter
            $table->index(['provinsi', 'tahun']);
            $table->index('faktor_penyebab');
            $table->index(['provinsi', 'tahun', 'faktor_penyebab']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faktor_perceraian');
    }
};
