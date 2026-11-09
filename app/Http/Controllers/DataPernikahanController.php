<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\RekapNikahProvinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataPernikahanController extends Controller
{
    /**
     * Urutan standar 12 bulan kalender masehi
     */
    protected array $monthsOrder = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    /**
     * Halaman Utama Kelola Data Pernikahan
     */
    public function index(Request $request)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->route('login');
        }

        $search = $request->input('search');
        $selectedTahun = $request->input('tahun');
        $selectedProvinsi = $request->input('provinsi');
        $selectedBulan = $request->input('bulan');
        $sortBy = $request->input('sort_by', 'tahun');
        $sortOrder = strtolower($request->input('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';

        // 1. Query builder utama
        $query = RekapNikahProvinsi::query()
            ->search($search)
            ->filterTahun($selectedTahun)
            ->filterProvinsi($selectedProvinsi)
            ->filterBulan($selectedBulan);

        // 2. Hitung 4 Kartu Ringkasan Statistik (Dinamis dari Database sesuai filter)
        $kpiQuery = clone $query;
        $totalData = $kpiQuery->count();
        $totalPernikahan = (int) $kpiQuery->sum('jumlah_pernikahan');
        $provinsiDenganData = (clone $query)->distinct('provinsi')->count('provinsi');

        // Master total provinsi dari tabel master provinces
        $totalMasterProvinsi = Province::count();

        // Label Tahun Tersedia
        $tahunTersediaLabel = '2023 – 2025';
        $keteranganKelengkapan = null;

        if (!empty($selectedTahun) && $selectedTahun !== 'Semua Tahun') {
            $tahunTersediaLabel = "Tahun {$selectedTahun}";
            if ((string) $selectedTahun === '2024') {
                $keteranganKelengkapan = 'Data 2024 tersedia s.d. September (Oktober–Desember belum dirilis resmi).';
            } else {
                $keteranganKelengkapan = "Data lengkap 12 bulan (Januari–Desember {$selectedTahun}).";
            }
        } else {
            $keteranganKelengkapan = 'Data observasi agregat 3 tahun (2023–2025). Tahun 2024 tersedia s.d. September.';
        }

        // 3. Daftar filter untuk dropdown
        $daftarTahun = RekapNikahProvinsi::distinct()->orderByDesc('tahun')->pluck('tahun')->toArray();
        if (empty($daftarTahun)) {
            $daftarTahun = [2025, 2024, 2023];
        }

        // Master provinsi dari tabel provinces
        $masterProvinces = Province::orderBy('name')->get();
        $daftarBulan = $this->monthsOrder;

        // 4. Pengurutan Data Sesuai Aturan:
        // - Tahun Terbaru (desc: 2025 -> 2024 -> 2023)
        // - Bulan Pertama (Januari dulu s.d. Desember)
        // - Provinsi Alfabetis (asc: Aceh, Bali, Banten, ...)
        $monthCase = "CASE bulan 
            WHEN 'Januari' THEN 1 
            WHEN 'Februari' THEN 2 
            WHEN 'Maret' THEN 3 
            WHEN 'April' THEN 4 
            WHEN 'Mei' THEN 5 
            WHEN 'Juni' THEN 6 
            WHEN 'Juli' THEN 7 
            WHEN 'Agustus' THEN 8 
            WHEN 'September' THEN 9 
            WHEN 'Oktober' THEN 10 
            WHEN 'November' THEN 11 
            WHEN 'Desember' THEN 12 
            ELSE 99 END";

        $allowedSorts = ['tahun', 'provinsi', 'bulan', 'jumlah_pernikahan', 'kode_wilayah', 'id'];
        if ($request->filled('sort_by') && in_array($sortBy, $allowedSorts)) {
            if ($sortBy === 'bulan') {
                $query->orderByRaw("{$monthCase} {$sortOrder}")
                      ->orderBy('provinsi', 'asc');
            } elseif ($sortBy === 'kode_wilayah') {
                $query->orderByRaw("CAST(kode_wilayah AS UNSIGNED) {$sortOrder}")
                      ->orderBy('kode_wilayah', $sortOrder)
                      ->orderBy('tahun', 'desc');
            } elseif ($sortBy === 'provinsi') {
                $query->orderBy('provinsi', $sortOrder)
                      ->orderBy('tahun', 'desc')
                      ->orderByRaw("{$monthCase} ASC");
            } elseif ($sortBy === 'jumlah_pernikahan') {
                $query->orderBy('jumlah_pernikahan', $sortOrder)
                      ->orderBy('tahun', 'desc')
                      ->orderBy('provinsi', 'asc');
            } else {
                $query->orderBy($sortBy, $sortOrder)
                      ->orderByRaw("{$monthCase} ASC")
                      ->orderBy('provinsi', 'asc');
            }
        } else {
            // Default Sorting:
            // 1. Tahun Terbaru (desc)
            // 2. Bulan Pertama (Januari s.d. Desember)
            // 3. Provinsi Alfabetis (asc: Aceh, Bali, Banten, ...)
            $query->orderBy('tahun', 'desc')
                  ->orderByRaw("{$monthCase} ASC")
                  ->orderBy('provinsi', 'asc');
        }

        // Secondary fallback ordering untuk konsistensi baris
        $query->orderBy('id', 'asc');

        // 5. Pagination hasil (default 38 baris agar 1 halaman memuat seluruh provinsi untuk 1 bulan secara merata)
        $perPage = (int) $request->input('per_page', 38);
        if ($perPage < 5 || $perPage > 100) {
            $perPage = 38;
        }

        $pernikahanList = $query->paginate($perPage)->appends($request->all());

        return view('data-pernikahan', compact(
            'pernikahanList',
            'totalData',
            'totalPernikahan',
            'tahunTersediaLabel',
            'provinsiDenganData',
            'totalMasterProvinsi',
            'keteranganKelengkapan',
            'daftarTahun',
            'masterProvinces',
            'daftarBulan',
            'search',
            'selectedTahun',
            'selectedProvinsi',
            'selectedBulan',
            'sortBy',
            'sortOrder'
        ));
    }

    /**
     * Tambah Data Pernikahan Baru
     */
    public function store(Request $request)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->route('login');
        }

        $request->validate([
            'tahun'             => 'required|integer|min:2000|max:2030',
            'provinsi'          => 'required|string|max:100',
            'bulan'             => 'required|string|in:' . implode(',', $this->monthsOrder),
            'jumlah_pernikahan' => 'required|integer|min:0',
        ], [
            'tahun.required'             => 'Tahun observasi wajib diisi.',
            'tahun.integer'              => 'Tahun harus berupa bilangan angka.',
            'provinsi.required'          => 'Provinsi wajib dipilih.',
            'bulan.required'             => 'Bulan wajib dipilih.',
            'bulan.in'                   => 'Nama bulan yang dipilih tidak valid.',
            'jumlah_pernikahan.required' => 'Jumlah pernikahan wajib diisi.',
            'jumlah_pernikahan.min'      => 'Jumlah pernikahan tidak boleh negatif.',
        ]);

        // Cek kombinasi identitas unik (Provinsi, Tahun, Bulan) agar tidak ada data ganda
        $exists = RekapNikahProvinsi::where('provinsi', $request->provinsi)
            ->where('tahun', $request->tahun)
            ->where('bulan', $request->bulan)
            ->first();

        if ($exists) {
            return back()->withErrors([
                'duplicate' => "Data pernikahan untuk {$request->provinsi} periode {$request->bulan} {$request->tahun} sudah tersimpan di database (" . number_format($exists->jumlah_pernikahan, 0, ',', '.') . " akta). Silakan gunakan tombol Edit untuk memperbarui angka."
            ])->withInput();
        }

        // Ambil kode wilayah dari master province
        $kodeWilayah = Province::where('name', $request->provinsi)->value('code') ?: $request->input('kode_wilayah', '');

        try {
            DB::transaction(function () use ($request, $kodeWilayah) {
                RekapNikahProvinsi::create([
                    'kode_wilayah'      => $kodeWilayah,
                    'provinsi'          => $request->provinsi,
                    'tahun'             => $request->tahun,
                    'bulan'             => $request->bulan,
                    'jumlah_pernikahan' => $request->jumlah_pernikahan,
                ]);

                // Sinkronisasi tabel flat jika tersedia
                if (Schema::hasTable('data_pernikahan_2023_2025_flat')) {
                    DB::table('data_pernikahan_2023_2025_flat')->insert([
                        'kode_wilayah'      => $kodeWilayah,
                        'provinsi'          => $request->provinsi,
                        'tahun'             => $request->tahun,
                        'bulan'             => $request->bulan,
                        'jumlah_pernikahan' => $request->jumlah_pernikahan,
                    ]);
                }
            });

            return redirect()->route('admin.data-pernikahan.index')->with(
                'success',
                "Data pernikahan {$request->provinsi} ({$request->bulan} {$request->tahun}) berhasil ditambahkan sebanyak " . number_format($request->jumlah_pernikahan, 0, ',', '.') . " akta."
            );
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'Gagal menyimpan data pernikahan. Terjadi kesalahan pada database.'])->withInput();
        }
    }

    /**
     * Perbarui Data Pernikahan
     */
    public function update(Request $request, $id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->route('login');
        }

        $record = RekapNikahProvinsi::findOrFail($id);

        $request->validate([
            'tahun'             => 'required|integer|min:2000|max:2030',
            'provinsi'          => 'required|string|max:100',
            'bulan'             => 'required|string|in:' . implode(',', $this->monthsOrder),
            'jumlah_pernikahan' => 'required|integer|min:0',
        ], [
            'tahun.required'             => 'Tahun observasi wajib diisi.',
            'provinsi.required'          => 'Provinsi wajib dipilih.',
            'bulan.required'             => 'Bulan wajib dipilih.',
            'jumlah_pernikahan.required' => 'Jumlah pernikahan wajib diisi.',
            'jumlah_pernikahan.min'      => 'Jumlah pernikahan tidak boleh negatif.',
        ]);

        // Cegah duplikasi data jika diubah ke kombinasi yang sudah ada pada baris lain
        $duplicate = RekapNikahProvinsi::where('provinsi', $request->provinsi)
            ->where('tahun', $request->tahun)
            ->where('bulan', $request->bulan)
            ->where('id', '!=', $record->id)
            ->first();

        if ($duplicate) {
            return back()->withErrors([
                'duplicate' => "Data pernikahan untuk {$request->provinsi} periode {$request->bulan} {$request->tahun} sudah tersimpan pada catatan lain."
            ])->withInput();
        }

        $kodeWilayah = Province::where('name', $request->provinsi)->value('code') ?: $record->kode_wilayah;

        try {
            DB::transaction(function () use ($record, $request, $kodeWilayah) {
                $oldProv = $record->provinsi;
                $oldTahun = $record->tahun;
                $oldBulan = $record->bulan;

                $record->update([
                    'kode_wilayah'      => $kodeWilayah,
                    'provinsi'          => $request->provinsi,
                    'tahun'             => $request->tahun,
                    'bulan'             => $request->bulan,
                    'jumlah_pernikahan' => $request->jumlah_pernikahan,
                ]);

                // Sinkronisasi perubahan ke tabel flat
                if (Schema::hasTable('data_pernikahan_2023_2025_flat')) {
                    DB::table('data_pernikahan_2023_2025_flat')
                        ->where('provinsi', $oldProv)
                        ->where('tahun', $oldTahun)
                        ->where('bulan', $oldBulan)
                        ->update([
                            'kode_wilayah'      => $kodeWilayah,
                            'provinsi'          => $request->provinsi,
                            'tahun'             => $request->tahun,
                            'bulan'             => $request->bulan,
                            'jumlah_pernikahan' => $request->jumlah_pernikahan,
                        ]);
                }
            });

            return redirect()->route('admin.data-pernikahan.index')->with(
                'success',
                "Data pernikahan {$record->provinsi} ({$record->bulan} {$record->tahun}) berhasil diperbarui menjadi " . number_format($request->jumlah_pernikahan, 0, ',', '.') . " akta."
            );
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'Gagal memperbarui data pernikahan. Terjadi kendala teknis.'])->withInput();
        }
    }

    /**
     * Hapus Data Pernikahan
     */
    public function destroy($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->route('login');
        }

        $record = RekapNikahProvinsi::findOrFail($id);

        try {
            $provName = $record->provinsi;
            $bulanName = $record->bulan;
            $tahunVal = $record->tahun;
            $jumlahVal = number_format($record->jumlah_pernikahan, 0, ',', '.');

            DB::transaction(function () use ($record, $provName, $tahunVal, $bulanName) {
                // Hapus dari tabel flat jika ada
                if (Schema::hasTable('data_pernikahan_2023_2025_flat')) {
                    DB::table('data_pernikahan_2023_2025_flat')
                        ->where('provinsi', $provName)
                        ->where('tahun', $tahunVal)
                        ->where('bulan', $bulanName)
                        ->delete();
                }

                $record->delete();
            });

            return redirect()->route('admin.data-pernikahan.index')->with(
                'success',
                "Data pernikahan {$provName} periode {$bulanName} {$tahunVal} ({$jumlahVal} akta) berhasil dihapus."
            );
        } catch (\Exception $e) {
            return redirect()->route('admin.data-pernikahan.index')->with('error', 'Gagal menghapus data pernikahan.');
        }
    }

    /**
     * Endpoint API JSON Rincian Detail Data Pernikahan
     */
    public function detailJson($id)
    {
        if (!session()->get('is_logged_in')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $record = RekapNikahProvinsi::findOrFail($id);

        // Ambil rincian bulanan provinsi tersebut pada tahun terkait
        $rawMonthly = RekapNikahProvinsi::where('provinsi', $record->provinsi)
            ->where('tahun', $record->tahun)
            ->pluck('jumlah_pernikahan', 'bulan')
            ->toArray();

        $monthlyBreakdown = [];
        $totalTahunan = 0;
        $maxBulan = '-';
        $maxCount = -1;

        foreach ($this->monthsOrder as $bln) {
            if (array_key_exists($bln, $rawMonthly)) {
                $val = (int) $rawMonthly[$bln];
                $totalTahunan += $val;
                $monthlyBreakdown[$bln] = [
                    'status' => 'tersedia',
                    'jumlah' => $val,
                    'jumlah_formatted' => number_format($val, 0, ',', '.')
                ];
                if ($val > $maxCount) {
                    $maxCount = $val;
                    $maxBulan = $bln;
                }
            } else {
                $monthlyBreakdown[$bln] = [
                    'status' => 'tidak_tersedia',
                    'jumlah' => null,
                    'jumlah_formatted' => 'Belum tersedia'
                ];
            }
        }

        $bulanTersediaCount = count($rawMonthly);
        $rataRataBulanan = $bulanTersediaCount > 0 ? round($totalTahunan / $bulanTersediaCount) : 0;

        $catatanKelengkapan = 'Data observasi resmi terverifikasi.';
        if ((string) $record->tahun === '2024') {
            $catatanKelengkapan = 'Data pernikahan tahun 2024 dari Kementerian Agama tersedia sampai periode September. Bulan Oktober, November, dan Desember belum dirilis.';
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id'                   => $record->id,
                'kode_wilayah'         => $record->kode_wilayah ?: '-',
                'provinsi'             => $record->provinsi,
                'tahun'                => $record->tahun,
                'bulan'                => $record->bulan,
                'jumlah_pernikahan'    => $record->jumlah_pernikahan,
                'jumlah_formatted'     => number_format($record->jumlah_pernikahan, 0, ',', '.') . ' Akta',
                'sumber_data'          => 'Kementerian Agama RI & BPS RI',
                'catatan_kelengkapan'  => $catatanKelengkapan,
                'total_tahunan'        => $totalTahunan,
                'total_tahunan_format' => number_format($totalTahunan, 0, ',', '.') . ' Akta',
                'bulan_tersedia_count' => $bulanTersediaCount,
                'rata_rata_bulanan'    => number_format($rataRataBulanan, 0, ',', '.') . ' Akta/bulan',
                'bulan_puncak'         => $maxBulan . ($maxCount > 0 ? ' (' . number_format($maxCount, 0, ',', '.') . ' Akta)' : ''),
                'monthly_breakdown'    => $monthlyBreakdown,
            ]
        ]);
    }

    /**
     * Ekspor Data Pernikahan ke CSV (Kompatibel dengan Microsoft Excel via UTF-8 BOM)
     */
    public function export(Request $request): StreamedResponse
    {
        if (!session()->get('is_logged_in')) {
            abort(403, 'Akses ditolak.');
        }

        $search = $request->input('search');
        $selectedTahun = $request->input('tahun');
        $selectedProvinsi = $request->input('provinsi');
        $selectedBulan = $request->input('bulan');

        $monthCase = "CASE bulan 
            WHEN 'Januari' THEN 1 
            WHEN 'Februari' THEN 2 
            WHEN 'Maret' THEN 3 
            WHEN 'April' THEN 4 
            WHEN 'Mei' THEN 5 
            WHEN 'Juni' THEN 6 
            WHEN 'Juli' THEN 7 
            WHEN 'Agustus' THEN 8 
            WHEN 'September' THEN 9 
            WHEN 'Oktober' THEN 10 
            WHEN 'November' THEN 11 
            WHEN 'Desember' THEN 12 
            ELSE 99 END";

        $query = RekapNikahProvinsi::query()
            ->search($search)
            ->filterTahun($selectedTahun)
            ->filterProvinsi($selectedProvinsi)
            ->filterBulan($selectedBulan)
            ->orderBy('tahun', 'desc')
            ->orderByRaw("{$monthCase} ASC")
            ->orderBy('provinsi', 'asc');

        // Bentuk nama file yang informatif
        $parts = ['data_pernikahan'];
        if ($selectedTahun && $selectedTahun !== 'Semua Tahun') {
            $parts[] = $selectedTahun;
        } else {
            $parts[] = '2023-2025';
        }
        if ($selectedProvinsi && $selectedProvinsi !== 'Semua Provinsi') {
            $parts[] = Str::slug($selectedProvinsi);
        }
        if ($selectedBulan && $selectedBulan !== 'Semua Bulan') {
            $parts[] = Str::slug($selectedBulan);
        }

        $fileName = implode('_', $parts) . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Tambahkan UTF-8 BOM agar Microsoft Excel langsung membaca aksen/karakter dengan benar
            fputs($handle, "\xEF\xBB\xBF");

            // Header kolom CSV
            fputcsv($handle, [
                'No',
                'Tahun',
                'Kode Wilayah',
                'Provinsi',
                'Bulan',
                'Jumlah Pernikahan (Akta)',
                'Sumber Data',
            ]);

            $no = 1;
            $query->chunk(200, function ($records) use ($handle, &$no) {
                foreach ($records as $row) {
                    fputcsv($handle, [
                        $no++,
                        $row->tahun,
                        $row->kode_wilayah ?: '-',
                        $row->provinsi,
                        $row->bulan,
                        $row->jumlah_pernikahan,
                        'Kementerian Agama RI & BPS RI',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
