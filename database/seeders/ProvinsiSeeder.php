<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Province;

class ProvinsiSeeder extends Seeder
{
    /**
     * Run the database seeds for all 34 provinces in Indonesia.
     */
    public function run(): void
    {
        $provinces = config('provinces.list', []);

        foreach ($provinces as $index => $item) {
            DB::table('provinces')->updateOrInsert(
                ['code' => $item['code']],
                [
                    'name'               => $item['name'],
                    'island_group'       => $item['island_group'],
                    'total_marriages'    => $item['total_marriages'] ?? 10000,
                    'dispensation_ratio' => $item['dispensation_ratio'] ?? 1.00,
                    'status'             => 'Aktif',
                    'updated_at'         => now(),
                    'created_at'         => now(),
                ]
            );
        }
    }
}
