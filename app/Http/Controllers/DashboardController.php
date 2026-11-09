<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Analytics & Persebaran Data Pernikahan
     */
    public function index(Request $request)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->route('login');
        }

        // 1. Ambil parameter filter dari request
        $selectedProvinsi = $request->input('provinsi');
        $selectedTahun = $request->input('tahun');

        // 2. Query statistik total pernikahan langsung dari tabel rekapitulasi Simkah Kemenag (rekap_nikah_provinsi)
        $query = DB::table('rekap_nikah_provinsi');
        if ($request->filled('provinsi')) {
            $query->where('provinsi', $request->provinsi);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        $totalPernikahan = (int) $query->sum('jumlah_pernikahan');

        // 3. Ambil daftar provinsi unik untuk isi dropdown filter dari rekap_nikah_provinsi
        $daftarProvinsi = DB::table('rekap_nikah_provinsi')
                            ->select('provinsi')
                            ->distinct()
                            ->orderBy('provinsi')
                            ->pluck('provinsi');

        // Daftar tahun unik untuk filter tahun dari rekap_nikah_provinsi
        $daftarTahun = DB::table('rekap_nikah_provinsi')
                            ->select('tahun')
                            ->distinct()
                            ->orderBy('tahun', 'desc')
                            ->pluck('tahun');

        // Total provinsi tercover (1 jika provinsi dipilih, atau hitung distinct provinsi)
        $provQuery = DB::table('rekap_nikah_provinsi');
        if ($request->filled('provinsi')) {
            $provQuery->where('provinsi', $request->provinsi);
        }
        if ($request->filled('tahun')) {
            $provQuery->where('tahun', $request->tahun);
        }
        $totalProvinsi = $provQuery->distinct('provinsi')->count('provinsi');

        // 4. Data chart/grafik bulanan dan perbandingan antar provinsi dari rekap_nikah_provinsi
        $monthsOrder = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $chartBulananQuery = DB::table('rekap_nikah_provinsi')
            ->select('bulan', DB::raw('SUM(jumlah_pernikahan) as total'));

        if ($request->filled('provinsi')) {
            $chartBulananQuery->where('provinsi', $request->provinsi);
        }
        if ($request->filled('tahun')) {
            $chartBulananQuery->where('tahun', $request->tahun);
        }

        $rawBulanan = $chartBulananQuery->groupBy('bulan')->pluck('total', 'bulan')->toArray();

        $chartBulananData = [];
        $bulanPuncak = '-';
        $maxTotal = -1;

        foreach ($monthsOrder as $bln) {
            $val = (int) ($rawBulanan[$bln] ?? 0);
            $chartBulananData[$bln] = $val;
            if ($val > $maxTotal && $val > 0) {
                $maxTotal = $val;
                $bulanPuncak = $bln;
            }
        }

        // Data perbandingan antar provinsi dari rekap_nikah_provinsi
        $perbandinganProvinsi = [];
        if (!$request->filled('provinsi')) {
            $perbandinganQuery = DB::table('rekap_nikah_provinsi')
                ->select('provinsi', 'kode_wilayah', DB::raw('SUM(jumlah_pernikahan) as total'));
            if ($request->filled('tahun')) {
                $perbandinganQuery->where('tahun', $request->tahun);
            }
            $perbandinganProvinsi = $perbandinganQuery
                ->groupBy('provinsi', 'kode_wilayah')
                ->orderByDesc('total')
                ->get();
        } else {
            // Perbandingan antar tahun untuk provinsi terpilih
            $perbandinganProvinsi = DB::table('rekap_nikah_provinsi')
                ->select('tahun', DB::raw('SUM(jumlah_pernikahan) as total'))
                ->where('provinsi', $request->provinsi)
                ->groupBy('tahun')
                ->orderBy('tahun')
                ->get();
        }

        $periodeDataset = $request->filled('tahun') ? 'Tahun ' . $request->tahun : '2023 – 2025';

        // 5. Data Perceraian & Faktor Penyebab Perceraian BPS (data_perceraian_flat & faktor_perceraian_bps)
        $perceraianQuery = DB::table('data_perceraian_flat');
        if ($request->filled('provinsi')) {
            $perceraianQuery->where('provinsi', $request->provinsi);
        }
        if ($request->filled('tahun')) {
            $perceraianQuery->where('tahun', $request->tahun);
        }
        $totalPerceraianFlat = (int) $perceraianQuery->sum('jumlah_perceraian');

        $faktorPerceraianQuery = DB::table('faktor_perceraian_bps')
            ->select('faktor_penyebab', DB::raw('SUM(jumlah_kasus) as total'));

        if ($request->filled('provinsi')) {
            $faktorPerceraianQuery->where('provinsi', $request->provinsi);
        }
        if ($request->filled('tahun')) {
            $faktorPerceraianQuery->where('tahun', $request->tahun);
        }

        $faktorPerceraianData = $faktorPerceraianQuery->groupBy('faktor_penyebab')
            ->orderByDesc('total')
            ->get();

        $totalKasusPerceraian = $totalPerceraianFlat > 0 ? $totalPerceraianFlat : (int) $faktorPerceraianData->sum('total');
        $topFaktorPerceraian = $faktorPerceraianData->first();

        $viewName = view()->exists('admin.dashboard') ? 'admin.dashboard' : 'index';

        return view($viewName, compact(
            'totalPernikahan',
            'daftarProvinsi',
            'daftarTahun',
            'selectedProvinsi',
            'selectedTahun',
            'totalProvinsi',
            'periodeDataset',
            'bulanPuncak',
            'chartBulananData',
            'perbandinganProvinsi',
            'faktorPerceraianData',
            'totalKasusPerceraian',
            'topFaktorPerceraian'
        ));
    }
}
