<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BpsPerceraianCsvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengimpor data CSV BPS resmi multi-tahun (2023, 2024, 2025)
     * ke data_perceraian_flat dan faktor_perceraian_bps.
     */
    public function run(): void
    {
        // 1. Bersihkan tabel tujuan (truncate) sebelum import data baru
        if (Schema::hasTable('data_perceraian_flat')) {
            DB::table('data_perceraian_flat')->truncate();
        }
        if (Schema::hasTable('faktor_perceraian_bps')) {
            DB::table('faktor_perceraian_bps')->truncate();
        }
        if (Schema::hasTable('faktor_perceraian')) {
            DB::table('faktor_perceraian')->truncate();
        }

        // 2. Ambil seluruh file CSV dari folder database/seeders/csv/
        $csvDirectory = database_path('seeders/csv');
        $files = glob($csvDirectory . '/*.csv');

        if (empty($files)) {
            $this->command->warn("Tidak ada file CSV ditemukan di {$csvDirectory}");
            return;
        }

        // Urutkan file berdasarkan nama/tahun
        sort($files);

        $now = now();
        $totalPerceraianRecords = [];
        $faktorPerceraianRecords = [];

        foreach ($files as $filePath) {
            $filename = basename($filePath);
            
            // Ekstrak tahun dari nama file (misal: perceraian_2023.csv -> 2023)
            preg_match('/(\d{4})/', $filename, $yearMatch);
            $tahun = isset($yearMatch[1]) ? (int) $yearMatch[1] : 2025;

            $this->command->info("Memproses file: {$filename} (Tahun {$tahun})...");

            if (!file_exists($filePath) || !is_readable($filePath)) {
                $this->command->error("File tidak dapat dibaca: {$filePath}");
                continue;
            }

            $handle = fopen($filePath, 'r');
            if (!$handle) {
                continue;
            }

            // Baca baris header
            $header = fgetcsv($handle);
            if (!$header) {
                fclose($handle);
                continue;
            }

            // Hapus UTF-8 BOM pada kolom pertama jika ada
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

            // Bersihkan nama kolom dan petakan indeks
            $totalColIndex = null;
            $factorColumns = [];

            foreach ($header as $colIndex => $colName) {
                $cleanColName = trim($colName);
                if ($colIndex === 0) {
                    continue; // Kolom Provinsi
                }

                if (stripos($cleanColName, 'jumlah') !== false) {
                    $totalColIndex = $colIndex;
                } else {
                    $normalizedFactor = $this->normalizeFactorName($cleanColName);
                    $factorColumns[$colIndex] = $normalizedFactor;
                }
            }

            // Baca baris data
            while (($row = fgetcsv($handle)) !== false) {
                if (empty($row) || !isset($row[0])) {
                    continue;
                }

                // Rule a & b: Trim nama provinsi dan validasi baris yang harus dilewati
                $provinsi = trim($row[0]);

                // Jika sudah sampai baris Indonesia, hentikan loop file ini karena baris berikutnya adalah catatan kaki
                if (strtolower($provinsi) === 'indonesia') {
                    break;
                }

                if ($this->shouldSkipRow($provinsi)) {
                    continue;
                }

                // Rule c & d: Ambil total perceraian dan simpan ke data_perceraian_flat
                $totalKasus = 0;
                if ($totalColIndex !== null && isset($row[$totalColIndex])) {
                    $totalKasus = $this->cleanNumber($row[$totalColIndex]);
                } else {
                    // Jika kolom Jumlah tidak ditemukan terpisah, hitung akumulasi faktor
                    $sum = 0;
                    foreach ($factorColumns as $cIdx => $fName) {
                        $sum += $this->cleanNumber($row[$cIdx] ?? 0);
                    }
                    $totalKasus = $sum;
                }

                $totalPerceraianRecords[] = [
                    'provinsi'           => $provinsi,
                    'tahun'              => $tahun,
                    'jumlah_perceraian'  => $totalKasus,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];

                // Rule e: Unpivot kolom faktor ke faktor_perceraian_bps
                foreach ($factorColumns as $colIdx => $factorName) {
                    $kasusFaktor = isset($row[$colIdx]) ? $this->cleanNumber($row[$colIdx]) : 0;

                    $faktorPerceraianRecords[] = [
                        'provinsi'        => $provinsi,
                        'tahun'           => $tahun,
                        'faktor_penyebab' => $factorName,
                        'jumlah_kasus'    => $kasusFaktor,
                        'created_at'      => $now,
                        'updated_at'      => $now,
                    ];
                }
            }

            fclose($handle);
        }

        // 3. Batch insert ke database
        if (!empty($totalPerceraianRecords)) {
            foreach (array_chunk($totalPerceraianRecords, 200) as $chunk) {
                DB::table('data_perceraian_flat')->insert($chunk);
            }
            $this->command->info('Berhasil mengimpor ' . count($totalPerceraianRecords) . ' baris ke data_perceraian_flat.');
        }

        if (!empty($faktorPerceraianRecords)) {
            foreach (array_chunk($faktorPerceraianRecords, 250) as $chunk) {
                DB::table('faktor_perceraian_bps')->insert($chunk);
            }
            $this->command->info('Berhasil mengimpor ' . count($faktorPerceraianRecords) . ' baris ke faktor_perceraian_bps.');

            // Sinkronisasi juga ke tabel faktor_perceraian agar kompatibel penuh
            if (Schema::hasTable('faktor_perceraian')) {
                foreach (array_chunk($faktorPerceraianRecords, 250) as $chunk) {
                    DB::table('faktor_perceraian')->insert($chunk);
                }
            }
        }
    }

    /**
     * Cek apakah baris perlu dilewati (Indonesia, null/kosong, Keterangan, Footnote)
     */
    protected function shouldSkipRow(?string $provinsi): bool
    {
        if ($provinsi === null) {
            return true;
        }

        $trimmed = trim($provinsi);
        if ($trimmed === '' || strlen($trimmed) > 50) {
            return true;
        }

        $lower = strtolower($trimmed);
        if ($lower === 'indonesia') {
            return true;
        }

        if (
            str_starts_with($lower, 'keterangan') || 
            str_contains($lower, 'akta cerai') ||
            str_contains($lower, 'data perceraian') ||
            str_contains($lower, 'data provinsi') ||
            str_contains($lower, 'satu perkara')
        ) {
            return true;
        }

        if (str_starts_with($trimmed, '<') || str_starts_with($trimmed, '"<') || is_numeric($trimmed[0] ?? '')) {
            return true;
        }

        return false;
    }

    /**
     * Bersihkan nilai non-numerik, en-dash, titik-titik menjadi integer 0
     */
    protected function cleanNumber($val): int
    {
        if ($val === null) {
            return 0;
        }

        $str = trim((string) $val);

        // Hapus karakter en-dash, em-dash, spasi non-breaking, dan ellipsis
        $str = str_replace(["\xe2\x80\x93", "–", "—", "-", "...", "…", " "], '', $str);
        // Sisakan hanya digit angka
        $digitsOnly = preg_replace('/[^\d]/', '', $str);

        if ($digitsOnly === '' || !is_numeric($digitsOnly)) {
            return 0;
        }

        return (int) $digitsOnly;
    }

    /**
     * Standarisasi nama faktor perceraian dari header CSV
     */
    protected function normalizeFactorName(string $rawHeader): string
    {
        $clean = trim($rawHeader);
        // Hapus prefix "Fakor Perceraian - " atau "Faktor Perceraian - "
        $clean = preg_replace('/^Fa[k|kt]or\s+Perceraian\s*-\s*/i', '', $clean);
        $clean = trim($clean);

        $lower = strtolower($clean);

        if (str_contains($lower, 'kekerasan dalam rumah tangga') || str_contains($lower, 'kdrt')) {
            return 'KDRT';
        }
        if (str_contains($lower, 'perselisihan')) {
            return 'Perselisihan';
        }
        if (str_contains($lower, 'meninggalkan salah satu')) {
            return 'Meninggalkan Salah Satu Pihak';
        }
        if (str_contains($lower, 'judi')) {
            return 'Judi';
        }
        if (str_contains($lower, 'mabuk')) {
            return 'Mabuk';
        }
        if (str_contains($lower, 'madat')) {
            return 'Madat';
        }
        if (str_contains($lower, 'zina')) {
            return 'Zina';
        }
        if (str_contains($lower, 'poligami')) {
            return 'Poligami';
        }
        if (str_contains($lower, 'penjara')) {
            return 'Dihukum Penjara';
        }
        if (str_contains($lower, 'cacat')) {
            return 'Cacat Badan';
        }
        if (str_contains($lower, 'kawin paksa')) {
            return 'Kawin Paksa';
        }
        if (str_contains($lower, 'murtad')) {
            return 'Murtad';
        }
        if (str_contains($lower, 'ekonomi')) {
            return 'Ekonomi';
        }
        if (str_contains($lower, 'lain')) {
            return 'Lain-lain';
        }

        return $clean;
    }
}
