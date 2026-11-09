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
        Schema::create('data_nikah_agama_flat', function (Blueprint $table) {
            $table->id();
            $table->string('provinsi');
            $table->integer('tahun');
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']);
            $table->integer('jumlah_pernikahan')->default(0);
            $table->timestamps();

            // Indexing for performance
            $table->index(['provinsi', 'tahun', 'agama']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_nikah_agama_flat');
    }
};
