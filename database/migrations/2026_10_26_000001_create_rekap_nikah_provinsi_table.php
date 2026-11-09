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
        // 1. Buat tabel rekap_nikah_provinsi
        if (!Schema::hasTable('rekap_nikah_provinsi')) {
            Schema::create('rekap_nikah_provinsi', function (Blueprint $table) {
                $table->id();
                $table->string('kode_wilayah', 50)->nullable()->index();
                $table->string('provinsi', 255)->index();
                $table->integer('tahun')->index();
                $table->string('bulan', 50)->index();
                $table->integer('jumlah_peristiwa')->default(0);
                $table->integer('jumlah_pernikahan')->default(0); // Kompatibilitas dengan fitur controller & view yang sudah ada
                $table->string('sumber_data', 100)->default('Kementerian Agama RI');
                $table->timestamps();

                $table->index(['provinsi', 'tahun']);
            });
        }

        // 2. Buat tabel data_pernikahan_2023_2025_flat
        if (!Schema::hasTable('data_pernikahan_2023_2025_flat')) {
            Schema::create('data_pernikahan_2023_2025_flat', function (Blueprint $table) {
                $table->id();
                $table->string('kode_wilayah', 50)->nullable()->index();
                $table->string('provinsi', 255)->index();
                $table->integer('tahun')->index();
                $table->string('bulan', 50)->index();
                $table->integer('jumlah_peristiwa')->default(0);
                $table->integer('jumlah_pernikahan')->default(0);
                $table->string('sumber_data', 100)->default('Kementerian Agama RI');
                $table->timestamps();

                $table->index(['provinsi', 'tahun']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_pernikahan_2023_2025_flat');
        Schema::dropIfExists('rekap_nikah_provinsi');
    }
};
