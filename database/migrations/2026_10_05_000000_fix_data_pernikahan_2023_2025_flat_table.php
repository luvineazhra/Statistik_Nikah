<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('data_pernikahan_2023_2025_flat')) {
            if (Schema::hasColumn('data_pernikahan_2023_2025_flat', 'COL 1')) {
                DB::statement("DELETE FROM `data_pernikahan_2023_2025_flat` WHERE `COL 1` = 'kode_wilayah'");
                DB::statement("ALTER TABLE `data_pernikahan_2023_2025_flat` CHANGE `COL 1` `kode_wilayah` VARCHAR(50)");
                DB::statement("ALTER TABLE `data_pernikahan_2023_2025_flat` CHANGE `COL 2` `provinsi` VARCHAR(255)");
                DB::statement("ALTER TABLE `data_pernikahan_2023_2025_flat` CHANGE `COL 3` `tahun` INT");
                DB::statement("ALTER TABLE `data_pernikahan_2023_2025_flat` CHANGE `COL 4` `bulan` VARCHAR(50)");
                DB::statement("ALTER TABLE `data_pernikahan_2023_2025_flat` CHANGE `COL 5` `jumlah_pernikahan` INT");
                if (!Schema::hasColumn('data_pernikahan_2023_2025_flat', 'id')) {
                    DB::statement("ALTER TABLE `data_pernikahan_2023_2025_flat` ADD `id` BIGINT AUTO_INCREMENT PRIMARY KEY FIRST");
                }
            }
        }
    }

    public function down(): void
    {
    }
};
