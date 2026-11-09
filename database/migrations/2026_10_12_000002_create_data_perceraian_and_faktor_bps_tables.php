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
        // 1. Tabel data_perceraian_flat untuk total perceraian per provinsi & tahun
        if (!Schema::hasTable('data_perceraian_flat')) {
            Schema::create('data_perceraian_flat', function (Blueprint $table) {
                $table->id();
                $table->string('provinsi', 100);
                $table->integer('tahun');
                $table->integer('jumlah_perceraian')->default(0);
                $table->timestamps();

                $table->index(['provinsi', 'tahun']);
            });
        }

        // 2. Tabel faktor_perceraian_bps untuk unpivot data faktor penyebab perceraian BPS
        if (!Schema::hasTable('faktor_perceraian_bps')) {
            Schema::create('faktor_perceraian_bps', function (Blueprint $table) {
                $table->id();
                $table->string('provinsi', 100);
                $table->integer('tahun');
                $table->string('faktor_penyebab', 150);
                $table->integer('jumlah_kasus')->default(0);
                $table->timestamps();

                $table->index(['provinsi', 'tahun']);
                $table->index('faktor_penyebab');
                $table->index(['provinsi', 'tahun', 'faktor_penyebab']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_perceraian_flat');
        Schema::dropIfExists('faktor_perceraian_bps');
    }
};
