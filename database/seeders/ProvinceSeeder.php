<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ProvinsiSeeder::class);
    }
}
