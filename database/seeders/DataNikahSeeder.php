<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DataNikahSeeder extends Seeder
{
    /**
     * Daftar nama standar 12 bulan dalam kalender masehi
     */
    protected array $standardMonths = [
        'januari', 'februari', 'maret', 'april', 'mei', 'juni',
        'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
    ];

    /**
     * Run the database seeds.
     * Mengimpor data pernikahan dari file CSV data_nikah_2023.csv, data_nikah_2024.csv, data_nikah_2025.csv
     * ke dalam tabel rekap_nikah_provinsi dan data_pernikahan_2023_2025_flat.
     */
    public function run(): void
    {
        // 1. Bersihkan tabel tujuan sebelum import
        if (Schema::hasTable('rekap_nikah_provinsi')) {
            DB::table('rekap_nikah_provinsi')->truncate();
        }
        if (Schema::hasTable('data_pernikahan_2023_2025_flat')) {
            DB::table('data_pernikahan_2023_2025_flat')->truncate();
        }

        // Master kode wilayah provinsi dari database jika tersedia
        $provinceCodeMap = [];
        if (Schema::hasTable('provinces')) {
            $provinceCodeMap = DB::table('provinces')->pluck('code', 'name')->toArray();
        }

        $csvDirectory = database_path('seeders/csv');
        $targetFiles = [
            'data_nikah_2023.csv',
            'data_nikah_2024.csv',
            'data_nikah_2025.csv',
        ];

        $allRecords = [];
        $now = now();

        foreach ($targetFiles as $targetFileName) {
            $filePath = $csvDirectory . DIRECTORY_SEPARATOR . $targetFileName;

            if (!file_exists($filePath)) {
                $this->command->warn("File CSV tidak ditemukan: {$filePath}");
                continue;
            }

            preg_match('/(\d{4})/', $targetFileName, $yearMatch);
            $fileYear = isset($yearMatch[1]) ? (int) $yearMatch[1] : 2025;

            $this->command->info("--> Memproses file: {$targetFileName} (Tahun {$fileYear})...");

            $handle = fopen($filePath, 'r');
            if (!$handle) {
                $this->command->error("Gagal membuka file: {$filePath}");
                continue;
            }

            // Baca baris pertama untuk deteksi delimiter dan header
            $firstLine = fgets($handle);
            if ($firstLine === false) {
                fclose($handle);
                continue;
            }

            // Hapus UTF-8 BOM jika ada
            $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);

            // Deteksi delimiter (koma atau titik koma)
            $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

            // Parse baris header
            $header = str_getcsv($firstLine, $delimiter);
            $headerLower = array_map(function ($col) {
                return strtolower(trim($col));
            }, $header);

            // Periksa apakah format CSV adalah flat (memiliki kolom 'bulan' dan 'jumlah_peristiwa')
            $bulanColIndex = array_search('bulan', $headerLower);
            $isFlatFormat = ($bulanColIndex !== false);

            if ($isFlatFormat) {
                // FORMAT A: FLAT CSV
                $provinsiColIndex = array_search('provinsi', $headerLower);
                $tahunColIndex = array_search('tahun', $headerLower);
                $jumlahColIndex = array_search('jumlah_peristiwa', $headerLower);
                if ($jumlahColIndex === false) {
                    $jumlahColIndex = array_search('jumlah_pernikahan', $headerLower);
                }
                if ($jumlahColIndex === false) {
                    $jumlahColIndex = array_search('jumlah', $headerLower);
                }
                $kodeColIndex = array_search('kode_wilayah', $headerLower);
                $sumberColIndex = array_search('sumber_data', $headerLower);

                while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                    if (empty($row) || !isset($row[$provinsiColIndex])) {
                        continue;
                    }

                    $provinsi = trim($row[$provinsiColIndex] ?? '');
                    if ($this->shouldSkipRow($provinsi)) {
                        continue;
                    }

                    $tahun = ($tahunColIndex !== false && !empty($row[$tahunColIndex]))
                        ? (int) $row[$tahunColIndex]
                        : $fileYear;

                    $bulan = trim($row[$bulanColIndex] ?? '');
                    $jumlahPeristiwa = ($jumlahColIndex !== false)
                        ? $this->cleanNumber($row[$jumlahColIndex] ?? 0)
                        : 0;

                    $sumberData = ($sumberColIndex !== false && !empty($row[$sumberColIndex]))
                        ? trim($row[$sumberColIndex])
                        : 'Kementerian Agama RI';

                    $kodeWilayah = null;
                    if ($kodeColIndex !== false && !empty($row[$kodeColIndex])) {
                        $kodeWilayah = trim($row[$kodeColIndex]);
                    } elseif (isset($provinceCodeMap[$provinsi])) {
                        $kodeWilayah = $provinceCodeMap[$provinsi];
                    }

                    $allRecords[] = [
                        'kode_wilayah'      => $kodeWilayah,
                        'provinsi'          => $provinsi,
                        'tahun'             => $tahun,
                        'bulan'             => $bulan,
                        'jumlah_peristiwa'  => $jumlahPeristiwa,
                        'jumlah_pernikahan' => $jumlahPeristiwa,
                        'sumber_data'       => $sumberData,
                        'created_at'        => $now,
                        'updated_at'        => $now,
                    ];
                }
            } else {
                // FORMAT B: WIDE CSV (Bulan tersebar di beberapa kolom)
                $provinsiColIndex = 0;
                $kodeColIndex = null;
                $monthCols = [];

                foreach ($headerLower as $idx => $colName) {
                    if (str_contains($colName, 'provinsi')) {
                        $provinsiColIndex = $idx;
                    } elseif (str_contains($colName, 'kode')) {
                        $kodeColIndex = $idx;
                    } elseif (in_array($colName, $this->standardMonths)) {
                        $monthCols[$idx] = ucfirst($colName);
                    }
                }

                while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                    if (empty($row) || !isset($row[$provinsiColIndex])) {
                        continue;
                    }

                    $provinsi = trim($row[$provinsiColIndex] ?? '');
                    if ($this->shouldSkipRow($provinsi)) {
                        continue;
                    }

                    $kodeWilayah = ($kodeColIndex !== null && !empty($row[$kodeColIndex]))
                        ? trim($row[$kodeColIndex])
                        : ($provinceCodeMap[$provinsi] ?? null);

                    foreach ($monthCols as $cIdx => $monthName) {
                        $rawVal = $row[$cIdx] ?? 0;
                        $jumlahPeristiwa = $this->cleanNumber($rawVal);

                        $allRecords[] = [
                            'kode_wilayah'      => $kodeWilayah,
                            'provinsi'          => $provinsi,
                            'tahun'             => $fileYear,
                            'bulan'             => $monthName,
                            'jumlah_peristiwa'  => $jumlahPeristiwa,
                            'jumlah_pernikahan' => $jumlahPeristiwa,
                            'sumber_data'       => 'Kementerian Agama RI',
                            'created_at'        => $now,
                            'updated_at'        => $now,
                        ];
                    }
                }
            }

            fclose($handle);
        }

        // 2. Insert data dalam chunk agar performa cepat dan hemat memori
        $totalRows = count($allRecords);
        if ($totalRows === 0) {
            $this->command->warn("Tidak ada data pernikahan yang ditemukan untuk di-insert.");
            return;
        }

        $this->command->info("Menyimpan {$totalRows} baris data ke database...");

        $chunks = array_chunk($allRecords, 250);

        foreach ($chunks as $chunk) {
            if (Schema::hasTable('rekap_nikah_provinsi')) {
                DB::table('rekap_nikah_provinsi')->insert($chunk);
            }
            if (Schema::hasTable('data_pernikahan_2023_2025_flat')) {
                DB::table('data_pernikahan_2023_2025_flat')->insert($chunk);
            }
        }

        $this->command->info("SUKSES: Sebanyak {$totalRows} baris data pernikahan berhasil dimasukkan!");
    }

    /**
     * Konversi nilai sel CSV menjadi integer murni.
     * Membersihkan pemisah ribuan (titik/koma), spasi, tanda strip/dash, dsb.
     * sehingga angka seperti "3.145" terbaca tepat sebagai integer 3145 (bukan 0 atau 3).
     */
    protected function cleanNumber($val): int
    {
        if ($val === null) {
            return 0;
        }

        $str = trim((string) $val);

        // Hapus karakter en-dash, em-dash, spasi, dan ellipsis
        $str = str_replace(["\xe2\x80\x93", "–", "—", "-", "...", "…", " "], '', $str);

        // Sisakan hanya digit angka (0-9)
        $digitsOnly = preg_replace('/[^0-9]/', '', $str);

        if ($digitsOnly === '' || !is_numeric($digitsOnly)) {
            return 0;
        }

        return (int) $digitsOnly;
    }

    /**
     * Filter validasi baris yang bukan nama provinsi sah (misal: 'Indonesia', 'Jumlah', baris kosong, dsb.)
     */
    protected function shouldSkipRow(string $provinsi): bool
    {
        $trimmed = trim($provinsi);
        if ($trimmed === '' || strlen($trimmed) > 100) {
            return true;
        }

        $lower = strtolower($trimmed);
        if ($lower === 'indonesia' || $lower === 'jumlah' || $lower === 'total') {
            return true;
        }

        if (
            str_starts_with($lower, 'keterangan') ||
            str_starts_with($lower, 'sumber') ||
            str_starts_with($lower, 'catatan') ||
            str_starts_with($lower, '<')
        ) {
            return true;
        }

        return false;
    }
}
