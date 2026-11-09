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
        Schema::create('marriage_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained('provinces')->onDelete('cascade');
            $table->integer('year');
            $table->string('month');
            $table->integer('count');
            $table->string('registry_source')->default('SIMKAH & BPS');
            $table->string('status')->default('Terverifikasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marriage_records');
    }
};
