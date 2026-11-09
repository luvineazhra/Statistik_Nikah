<?php

namespace App\Http\Controllers;

use App\Models\DataPerceraian;
use App\Models\FaktorPerceraian;
use App\Models\Province;
use App\Models\RekapNikahProvinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicPortalController extends Controller
{
    /**
     * Urutan standar 12 bulan kalender
     */
    protected array $monthsOrder = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    /**
     * Halaman Utama Website Publik
     */
    public function index(Request $request)
    {
        $selectedTahun = $request->input('tahun', '2025');
        $selectedProvinsi = $request->input('provinsi', '');
        $selectedJenis = $request->input('jenis_statistik', 'pernikahan');
        $selectedFaktor = $request->input('faktor', '');

        // 1. Hitung 4 Kartu Ringkasan Statistik
        $kpi = $this->calculateKpi($selectedTahun, $selectedProvinsi);

        // 2. Data Tren Tahunan Multi-Tahun (2023, 2024, 2025)
        $trenPernikahan = $this->getAnnualTrendNikah($selectedProvinsi);
        $trenPerceraian = $this->getAnnualTrendCerai($selectedProvinsi);

        // 3. Peringkat Provinsi (Top 10)
        $rankingNikah = $this->getRankingNikah($selectedTahun);
        $rankingCerai = $this->getRankingCerai($selectedTahun);

        // 4. Breakdown Faktor Penyebab Perceraian
        $faktorBreakdown = $this->getFaktorBreakdown($selectedTahun, $selectedProvinsi);

        // 5. Data Rincian Bulanan Pernikahan untuk Tahun Terpilih
        $bulananNikah = $this->getMonthlyNikahBreakdown($selectedTahun, $selectedProvinsi);

        // 6. Master Data untuk Dropdown
        $masterProvinces = Province::orderBy('name')->get();
        $daftarTahun = [2025, 2024, 2023];
        $daftarFaktor = FaktorPerceraian::distinct()->orderBy('faktor_penyebab')->pluck('faktor_penyebab')->toArray();

        // 7. Tabel Data Pernikahan Publik (Terkini)
        $monthCase = "CASE bulan 
            WHEN 'Januari' THEN 1 WHEN 'Februari' THEN 2 WHEN 'Maret' THEN 3 WHEN 'April' THEN 4 
            WHEN 'Mei' THEN 5 WHEN 'Juni' THEN 6 WHEN 'Juli' THEN 7 WHEN 'Agustus' THEN 8 
            WHEN 'September' THEN 9 WHEN 'Oktober' THEN 10 WHEN 'November' THEN 11 WHEN 'Desember' THEN 12 
            ELSE 99 END";

        $pernikahanQuery = RekapNikahProvinsi::query();
        if (!empty($selectedTahun) && $selectedTahun !== 'Semua Tahun') {
            $pernikahanQuery->where('tahun', $selectedTahun);
        }
        if (!empty($selectedProvinsi)) {
            $pernikahanQuery->where('provinsi', $selectedProvinsi);
        }
        $pernikahanTable = $pernikahanQuery
            ->orderBy('tahun', 'desc')
            ->orderByRaw("{$monthCase} ASC")
            ->orderBy('provinsi', 'asc')
            ->paginate(15, ['*'], 'page_nikah')
            ->appends($request->all());

        // 8. Tabel Data Perceraian Publik (Terkini)
        $perceraianQuery = DataPerceraian::query();
        if (!empty($selectedTahun) && $selectedTahun !== 'Semua Tahun') {
            $perceraianQuery->where('tahun', $selectedTahun);
        }
        if (!empty($selectedProvinsi)) {
            $perceraianQuery->where('provinsi', $selectedProvinsi);
        }
        $perceraianTable = $perceraianQuery
            ->orderBy('tahun', 'desc')
            ->orderBy('provinsi', 'asc')
            ->paginate(15, ['*'], 'page_cerai')
            ->appends($request->all());

        // 9. Tabel Faktor Perceraian Publik (Terkini)
        $faktorTableQuery = FaktorPerceraian::query();
        if (!empty($selectedTahun) && $selectedTahun !== 'Semua Tahun') {
            $faktorTableQuery->where('tahun', $selectedTahun);
        }
        if (!empty($selectedProvinsi)) {
            $faktorTableQuery->where('provinsi', $selectedProvinsi);
        }
        if (!empty($selectedFaktor)) {
            $faktorTableQuery->where('faktor_penyebab', $selectedFaktor);
        }
        $faktorTable = $faktorTableQuery
            ->orderBy('tahun', 'desc')
            ->orderBy('faktor_penyebab', 'asc')
            ->orderBy('provinsi', 'asc')
            ->paginate(15, ['*'], 'page_faktor')
            ->appends($request->all());

        return view('pengguna', compact(
            'kpi',
            'trenPernikahan',
            'trenPerceraian',
            'rankingNikah',
            'rankingCerai',
            'faktorBreakdown',
            'bulananNikah',
            'masterProvinces',
            'daftarTahun',
            'daftarFaktor',
            'pernikahanTable',
            'perceraianTable',
            'faktorTable',
            'selectedTahun',
            'selectedProvinsi',
            'selectedJenis',
            'selectedFaktor'
        ));
    }

    /**
     * Endpoint API JSON Statistik Interaktif Publik (AJAX)
     */
    public function getStatsJson(Request $request)
    {
        $selectedTahun = $request->input('tahun', '2025');
        $selectedProvinsi = $request->input('provinsi', '');
        $selectedJenis = $request->input('jenis_statistik', 'pernikahan');

        $kpi = $this->calculateKpi($selectedTahun, $selectedProvinsi);
        $trenPernikahan = $this->getAnnualTrendNikah($selectedProvinsi);
        $trenPerceraian = $this->getAnnualTrendCerai($selectedProvinsi);
        $rankingNikah = $this->getRankingNikah($selectedTahun);
        $rankingCerai = $this->getRankingCerai($selectedTahun);
        $faktorBreakdown = $this->getFaktorBreakdown($selectedTahun, $selectedProvinsi);
        $bulananNikah = $this->getMonthlyNikahBreakdown($selectedTahun, $selectedProvinsi);

        return response()->json([
            'success' => true,
            'status' => 'success',
            'tahun' => $selectedTahun,
            'provinsi' => $selectedProvinsi,
            'jenis_statistik' => $selectedJenis,
            'kpi' => $kpi,
            'trenPernikahan' => $trenPernikahan,
            'tren_pernikahan' => $trenPernikahan,
            'trenPerceraian' => $trenPerceraian,
            'tren_perceraian' => $trenPerceraian,
            'rankingNikah' => $rankingNikah,
            'ranking_nikah' => $rankingNikah,
            'rankingCerai' => $rankingCerai,
            'ranking_cerai' => $rankingCerai,
            'faktorBreakdown' => $faktorBreakdown,
            'faktor_breakdown' => $faktorBreakdown,
            'bulananNikah' => $bulananNikah,
            'bulanan_nikah' => $bulananNikah,
        ]);
    }

    /**
     * Endpoint API Peta Choropleth Persebaran Indonesia (Leaflet)
     */
    public function getMapData(Request $request)
    {
        $tahun = $request->input('tahun', '2025');
        $jenis = $request->input('jenis_statistik', 'pernikahan'); // 'pernikahan' atau 'perceraian'
        $bulan = $request->input('bulan', 'Semua Bulan');

        $is2024 = ((string) $tahun === '2024');

        // Ambil data pernikahan per provinsi
        $nikahQuery = DB::table('rekap_nikah_provinsi');
        if ($tahun && $tahun !== 'Semua Tahun') {
            $nikahQuery->where('tahun', $tahun);
        }
        if ($is2024 && $bulan === 'Semua Bulan') {
            $nikahQuery->whereIn('bulan', ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September']);
        } elseif ($bulan !== 'Semua Bulan') {
            $nikahQuery->where('bulan', $bulan);
        }
        $nikahList = $nikahQuery->select('kode_wilayah', 'provinsi', DB::raw('SUM(jumlah_pernikahan) as total_nikah'))
            ->groupBy('kode_wilayah', 'provinsi')
            ->pluck('total_nikah', 'provinsi')
            ->toArray();

        // Ambil data perceraian per provinsi
        $ceraiQuery = DB::table('data_perceraian_flat');
        if ($tahun && $tahun !== 'Semua Tahun') {
            $ceraiQuery->where('tahun', $tahun);
        }
        $ceraiList = $ceraiQuery->select('provinsi', DB::raw('SUM(jumlah_perceraian) as total_cerai'))
            ->groupBy('provinsi')
            ->pluck('total_cerai', 'provinsi')
            ->toArray();

        // Ambil top faktor per provinsi
        $faktorGrouped = DB::table('faktor_perceraian');
        if ($tahun && $tahun !== 'Semua Tahun') {
            $faktorGrouped->where('tahun', $tahun);
        }
        $topFaktorList = $faktorGrouped->select('provinsi', 'faktor_penyebab', DB::raw('SUM(jumlah_kasus) as total_kasus'))
            ->groupBy('provinsi', 'faktor_penyebab')
            ->orderByDesc('total_kasus')
            ->get()
            ->groupBy('provinsi')
            ->map(function ($items) {
                $top = $items->first();
                return $top ? [
                    'nama' => $top->faktor_penyebab,
                    'kasus' => (int) $top->total_kasus,
                    'kasus_format' => number_format((int) $top->total_kasus, 0, ',', '.')
                ] : null;
            })
            ->toArray();

        // Tentukan data berdasarkan mode aktif (pernikahan vs perceraian)
        $masterProvinces = Province::all();
        $provincesData = [];
        $values = [];

        foreach ($masterProvinces as $prov) {
            $pName = $prov->name;
            $code = $prov->code ?: '';

            $valNikah = $nikahList[$pName] ?? null;
            $valCerai = $ceraiList[$pName] ?? null;
            $topFaktor = $topFaktorList[$pName] ?? null;

            $activeVal = ($jenis === 'perceraian') ? $valCerai : $valNikah;
            $hasData = (!is_null($activeVal) && $activeVal !== false);

            if ($hasData) {
                $values[] = (int) $activeVal;
            }

            $info = [
                'kode_wilayah'       => $code,
                'provinsi'           => $pName,
                'has_data'           => $hasData,
                'nilai'              => $hasData ? (int) $activeVal : null,
                'nilai_formatted'    => $hasData ? number_format((int) $activeVal, 0, ',', '.') : 'Data tidak tersedia',
                'nikah'              => !is_null($valNikah) ? (int) $valNikah : null,
                'nikah_formatted'    => !is_null($valNikah) ? number_format((int) $valNikah, 0, ',', '.') . ' Akta' : 'Data tidak tersedia',
                'cerai'              => !is_null($valCerai) ? (int) $valCerai : null,
                'cerai_formatted'    => !is_null($valCerai) ? number_format((int) $valCerai, 0, ',', '.') . ' Perkara' : 'Data tidak tersedia',
                'top_faktor'         => $topFaktor,
                'sumber'             => ($jenis === 'perceraian') ? 'BPS RI (Badan Peradilan Agama MA)' : 'Ditjen Bimas Islam Kementerian Agama RI',
            ];

            $provincesData[$pName] = $info;
            if ($code) {
                $provincesData[$code] = $info;
            }
        }

        $minVal = !empty($values) ? min($values) : 0;
        $maxVal = !empty($values) ? max($values) : 0;
        $totalAktif = !empty($values) ? array_sum($values) : 0;

        return response()->json([
            'success'           => true,
            'status'            => 'success',
            'tahun'             => $tahun,
            'jenis_statistik'   => $jenis,
            'total_nasional'    => $totalAktif,
            'total_formatted'   => number_format($totalAktif, 0, ',', '.'),
            'min_val'           => $minVal,
            'max_val'           => $maxVal,
            'data'              => $provincesData,
            'note'              => $is2024 ? 'Data pernikahan 2024 tersedia s.d. September.' : null
        ]);
    }

    /**
     * Hitung Kartu Ringkasan KPI Dinamis
     */
    protected function calculateKpi($tahun = '', $provinsi = ''): array
    {
        // 1. Total Pernikahan
        $nikahQ = DB::table('rekap_nikah_provinsi');
        if (!empty($tahun) && $tahun !== 'Semua Tahun') {
            $nikahQ->where('tahun', $tahun);
        }
        if (!empty($provinsi)) {
            $nikahQ->where('provinsi', $provinsi);
        }
        $totalNikah = (int) $nikahQ->sum('jumlah_pernikahan');

        // 2. Total Perceraian
        $ceraiQ = DB::table('data_perceraian_flat');
        if (!empty($tahun) && $tahun !== 'Semua Tahun') {
            $ceraiQ->where('tahun', $tahun);
        }
        if (!empty($provinsi)) {
            $ceraiQ->where('provinsi', $provinsi);
        }
        $totalCerai = (int) $ceraiQ->sum('jumlah_perceraian');

        // 3. Faktor Penyebab Perceraian Tertinggi
        $faktorQ = DB::table('faktor_perceraian')
            ->select('faktor_penyebab', DB::raw('SUM(jumlah_kasus) as total_kasus'));
        if (!empty($tahun) && $tahun !== 'Semua Tahun') {
            $faktorQ->where('tahun', $tahun);
        }
        if (!empty($provinsi)) {
            $faktorQ->where('provinsi', $provinsi);
        }
        $topFaktorRecord = $faktorQ->groupBy('faktor_penyebab')
            ->orderByDesc('total_kasus')
            ->first();

        // 4. Jumlah Provinsi yang Memiliki Data
        $provQ = DB::table('rekap_nikah_provinsi');
        if (!empty($tahun) && $tahun !== 'Semua Tahun') {
            $provQ->where('tahun', $tahun);
        }
        if (!empty($provinsi)) {
            $provQ->where('provinsi', $provinsi);
        }
        $provCount = $provQ->distinct('provinsi')->count('provinsi');
        $totalMasterProv = Province::count();

        // Label dan Catatan Dinamis
        $tahunLabel = (!empty($tahun) && $tahun !== 'Semua Tahun') ? "Tahun {$tahun}" : "Semua Tahun (2023–2025)";
        $provinsiLabel = !empty($provinsi) ? $provinsi : "Seluruh Indonesia";

        $catatanNikah = ((string) $tahun === '2024') ? 'Tercatat s.d. September 2024' : 'Data resmi Kementerian Agama RI';
        $catatanCerai = 'Sumber: BPS RI / Statistik Peradilan Agama';

        $catatanKelengkapan = 'Data observasi resmi konsolidasi 2023–2025.';
        if ((string) $tahun === '2024') {
            $catatanKelengkapan = 'Catatan: Data pernikahan 2024 resmi tersedia sampai September. Bulan Oktober–Desember belum dirilis.';
        }

        return [
            'total_nikah'             => $totalNikah,
            'total_pernikahan'        => $totalNikah,
            'total_pernikahan_format' => number_format($totalNikah, 0, ',', '.') . ' Akta',
            'total_cerai'             => $totalCerai,
            'total_perceraian'        => $totalCerai,
            'total_perceraian_format' => number_format($totalCerai, 0, ',', '.') . ' Perkara',
            'top_faktor_nama'         => $topFaktorRecord ? $topFaktorRecord->faktor_penyebab : '-',
            'top_faktor_kasus'        => $topFaktorRecord ? (int) $topFaktorRecord->total_kasus : 0,
            'top_faktor_format'       => $topFaktorRecord ? number_format($topFaktorRecord->total_kasus, 0, ',', '.') . ' Kasus' : '-',
            'provinsi_tercover'       => $provCount,
            'total_master_provinsi'   => $totalMasterProv,
            'tahun_label'             => $tahunLabel,
            'provinsi_label'          => $provinsiLabel,
            'catatan_nikah'           => $catatanNikah,
            'catatan_cerai'           => $catatanCerai,
            'catatan_kelengkapan'     => $catatanKelengkapan,
        ];
    }

    /**
     * Ambil Tren Tahunan Pernikahan (2023, 2024, 2025)
     */
    protected function getAnnualTrendNikah($provinsi = ''): array
    {
        $years = [2023, 2024, 2025];
        $result = [];

        foreach ($years as $yr) {
            $q = DB::table('rekap_nikah_provinsi')->where('tahun', $yr);
            if (!empty($provinsi)) {
                $q->where('provinsi', $provinsi);
            }
            $val = (int) $q->sum('jumlah_pernikahan');
            $result[$yr] = [
                'total' => $val,
                'formatted' => number_format($val, 0, ',', '.'),
                'is_incomplete' => ($yr === 2024),
                'keterangan' => ($yr === 2024) ? 'Tersedia s.d. September' : 'Lengkap 12 Bulan'
            ];
        }

        return [
            'labels'   => ['2023', '2024', '2025'],
            'data'     => [$result[2023]['total'], $result[2024]['total'], $result[2025]['total']],
            'details'  => $result,
            'provinsi' => $provinsi ?: 'Seluruh Indonesia',
        ];
    }

    /**
     * Ambil Tren Tahunan Perceraian (2023, 2024, 2025)
     */
    protected function getAnnualTrendCerai($provinsi = ''): array
    {
        $years = [2023, 2024, 2025];
        $result = [];

        foreach ($years as $yr) {
            $q = DB::table('data_perceraian_flat')->where('tahun', $yr);
            if (!empty($provinsi)) {
                $q->where('provinsi', $provinsi);
            }
            $val = (int) $q->sum('jumlah_perceraian');
            $result[$yr] = [
                'total' => $val,
                'formatted' => number_format($val, 0, ',', '.'),
                'keterangan' => 'Publikasi BPS RI'
            ];
        }

        return [
            'labels'   => ['2023', '2024', '2025'],
            'data'     => [$result[2023]['total'], $result[2024]['total'], $result[2025]['total']],
            'details'  => $result,
            'provinsi' => $provinsi ?: 'Seluruh Indonesia',
        ];
    }

    /**
     * Peringkat 10 Provinsi Pernikahan Tertinggi
     */
    protected function getRankingNikah($tahun = ''): array
    {
        $q = DB::table('rekap_nikah_provinsi')
            ->select('provinsi', DB::raw('SUM(jumlah_pernikahan) as total'));
        if (!empty($tahun) && $tahun !== 'Semua Tahun') {
            $q->where('tahun', $tahun);
        }
        return $q->groupBy('provinsi')
            ->orderByDesc('total')
            ->take(10)
            ->get()
            ->map(function ($row, $idx) {
                return [
                    'rank' => $idx + 1,
                    'provinsi' => $row->provinsi,
                    'total' => (int) $row->total,
                    'total_format' => number_format((int) $row->total, 0, ',', '.') . ' Akta'
                ];
            })
            ->toArray();
    }

    /**
     * Peringkat 10 Provinsi Perceraian Tertinggi
     */
    protected function getRankingCerai($tahun = ''): array
    {
        $q = DB::table('data_perceraian_flat')
            ->select('provinsi', DB::raw('SUM(jumlah_perceraian) as total'));
        if (!empty($tahun) && $tahun !== 'Semua Tahun') {
            $q->where('tahun', $tahun);
        }
        return $q->groupBy('provinsi')
            ->orderByDesc('total')
            ->take(10)
            ->get()
            ->map(function ($row, $idx) {
                return [
                    'rank' => $idx + 1,
                    'provinsi' => $row->provinsi,
                    'total' => (int) $row->total,
                    'total_format' => number_format((int) $row->total, 0, ',', '.') . ' Perkara'
                ];
            })
            ->toArray();
    }

    /**
     * Rekapitulasi Faktor Perceraian Berdasarkan Jumlah Kasus
     */
    protected function getFaktorBreakdown($tahun = '', $provinsi = ''): array
    {
        $q = DB::table('faktor_perceraian')
            ->select('faktor_penyebab', DB::raw('SUM(jumlah_kasus) as total'));
        if (!empty($tahun) && $tahun !== 'Semua Tahun') {
            $q->where('tahun', $tahun);
        }
        if (!empty($provinsi)) {
            $q->where('provinsi', $provinsi);
        }
        $list = $q->groupBy('faktor_penyebab')
            ->orderByDesc('total')
            ->get();

        $grandTotal = $list->sum('total');

        $items = $list->map(function ($row, $idx) use ($grandTotal) {
            $val = (int) $row->total;
            $pct = $grandTotal > 0 ? round(($val / $grandTotal) * 100, 1) : 0;
            return [
                'rank' => $idx + 1,
                'faktor' => $row->faktor_penyebab,
                'total' => $val,
                'total_format' => number_format($val, 0, ',', '.'),
                'persen' => $pct,
                'persentase' => $pct,
                'is_top' => ($idx === 0)
            ];
        })->toArray();

        return [
            'labels'     => array_column($items, 'faktor'),
            'data'       => array_column($items, 'total'),
            'items'      => $items,
            'top_faktor' => !empty($items) ? $items[0] : null
        ];
    }

    /**
     * Distribusi Bulanan Pernikahan
     */
    protected function getMonthlyNikahBreakdown($tahun = '2025', $provinsi = ''): array
    {
        $q = DB::table('rekap_nikah_provinsi');
        if (!empty($tahun) && $tahun !== 'Semua Tahun') {
            $q->where('tahun', $tahun);
        } else {
            $q->where('tahun', 2025);
        }
        if (!empty($provinsi)) {
            $q->where('provinsi', $provinsi);
        }

        $raw = $q->select('bulan', DB::raw('SUM(jumlah_pernikahan) as total'))
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $grandTotal = array_sum($raw);
        $result = [];
        foreach ($this->monthsOrder as $bln) {
            if (array_key_exists($bln, $raw)) {
                $val = (int) $raw[$bln];
                $pct = $grandTotal > 0 ? round(($val / $grandTotal) * 100, 1) : 0;
                $result[] = [
                    'bulan'        => $bln,
                    'status'       => 'tersedia',
                    'total'        => $val,
                    'total_format' => number_format($val, 0, ',', '.'),
                    'persen'       => $pct
                ];
            }
        }

        return $result;
    }

    /**
     * Ekspor CSV Data Pernikahan Publik
     */
    public function exportPernikahan(Request $request): StreamedResponse
    {
        $selectedTahun = $request->input('tahun');
        $selectedProvinsi = $request->input('provinsi');

        $query = RekapNikahProvinsi::query();
        if (!empty($selectedTahun) && $selectedTahun !== 'Semua Tahun') {
            $query->where('tahun', $selectedTahun);
        }
        if (!empty($selectedProvinsi)) {
            $query->where('provinsi', $selectedProvinsi);
        }

        $fileName = 'data_pernikahan_publik_' . ($selectedTahun ?: '2023-2025') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

            fputcsv($handle, ['No', 'Tahun', 'Kode Wilayah', 'Provinsi', 'Bulan', 'Jumlah Pernikahan (Akta)', 'Sumber Data']);

            $no = 1;
            $query->orderBy('tahun', 'desc')->orderBy('provinsi', 'asc')->chunk(200, function ($rows) use ($handle, &$no) {
                foreach ($rows as $r) {
                    fputcsv($handle, [
                        $no++,
                        $r->tahun,
                        $r->kode_wilayah ?: '-',
                        $r->provinsi,
                        $r->bulan,
                        $r->jumlah_pernikahan,
                        'Ditjen Bimas Islam Kementerian Agama RI'
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Ekspor CSV Data Perceraian Publik
     */
    public function exportPerceraian(Request $request): StreamedResponse
    {
        $selectedTahun = $request->input('tahun');
        $selectedProvinsi = $request->input('provinsi');

        $query = DataPerceraian::query();
        if (!empty($selectedTahun) && $selectedTahun !== 'Semua Tahun') {
            $query->where('tahun', $selectedTahun);
        }
        if (!empty($selectedProvinsi)) {
            $query->where('provinsi', $selectedProvinsi);
        }

        $fileName = 'data_perceraian_publik_' . ($selectedTahun ?: '2023-2025') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

            fputcsv($handle, ['No', 'Tahun', 'Provinsi', 'Jumlah Perceraian (Perkara)', 'Sumber Data']);

            $no = 1;
            $query->orderBy('tahun', 'desc')->orderBy('provinsi', 'asc')->chunk(200, function ($rows) use ($handle, &$no) {
                foreach ($rows as $r) {
                    fputcsv($handle, [
                        $no++,
                        $r->tahun,
                        $r->provinsi,
                        $r->jumlah_perceraian,
                        'Badan Pusat Statistik (BPS RI) & Badilag MA'
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
