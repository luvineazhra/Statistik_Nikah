<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MapApiController extends Controller
{
    public function getData(Request $request)
    {
        $tahun = $request->input('tahun', '2025');
        $bulan = $request->input('bulan', 'Semua Bulan');

        $is2024 = ((string)$tahun === '2024');
        $unavailableMonths2024 = ['Oktober', 'November', 'Desember'];

        // Special check: 2024 Oktober - Desember data unavailable
        if ($is2024 && in_array($bulan, $unavailableMonths2024)) {
            return response()->json([
                'status' => 'success',
                'tahun' => $tahun,
                'bulan' => $bulan,
                'periode_label' => "$bulan $tahun",
                'is_data_available' => false,
                'note' => 'Data belum tersedia untuk periode ini.',
                'total_nasional' => 0,
                'total_nasional_formatted' => 'Data tidak tersedia',
                'provinsi_tertinggi' => ['name' => '-', 'jumlah' => 0],
                'provinsi_terendah' => ['name' => '-', 'jumlah' => 0],
                'min_val' => 0,
                'max_val' => 0,
                'data' => [],
                'top_10' => []
            ]);
        }

        // Query setup dari tabel rekapitulasi Simkah Kemenag
        $query = DB::table('rekap_nikah_provinsi')
            ->where('tahun', $tahun);

        $note = null;
        if ($is2024 && $bulan === 'Semua Bulan') {
            $note = 'Data 2024 tersedia sampai September.';
            $query->whereIn('bulan', ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September']);
        } elseif ($bulan !== 'Semua Bulan') {
            $query->where('bulan', $bulan);
        }

        $totalNasional = (int) $query->sum('jumlah_pernikahan');

        // Aggregation per province dari tabel rekapitulasi Simkah Kemenag
        $provQuery = DB::table('rekap_nikah_provinsi')
            ->select('kode_wilayah', 'provinsi', DB::raw('SUM(jumlah_pernikahan) as jumlah'))
            ->where('tahun', $tahun);

        if ($is2024 && $bulan === 'Semua Bulan') {
            $provQuery->whereIn('bulan', ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September']);
        } elseif ($bulan !== 'Semua Bulan') {
            $provQuery->where('bulan', $bulan);
        }

        $list = $provQuery->groupBy('kode_wilayah', 'provinsi')
            ->orderByDesc('jumlah')
            ->get();

        $data = [];
        $top10 = [];
        $minVal = null;
        $maxVal = 0;
        $tertinggi = ['name' => '-', 'jumlah' => 0];
        $terendah = ['name' => '-', 'jumlah' => 0];

        if ($list->count() > 0) {
            $maxVal = (int) $list->first()->jumlah;
            $tertinggi = [
                'name' => $list->first()->provinsi,
                'jumlah' => (int) $list->first()->jumlah,
                'jumlah_formatted' => number_format((int) $list->first()->jumlah, 0, ',', '.')
            ];

            $lastWithData = $list->where('jumlah', '>', 0)->last() ?? $list->last();
            $minVal = (int) $lastWithData->jumlah;
            $terendah = [
                'name' => $lastWithData->provinsi,
                'jumlah' => (int) $lastWithData->jumlah,
                'jumlah_formatted' => number_format((int) $lastWithData->jumlah, 0, ',', '.')
            ];
        }

        // Ambil data faktor perceraian per provinsi untuk tahun ini dari data BPS
        $faktorGrouped = DB::table('faktor_perceraian_bps')
            ->where('tahun', $tahun)
            ->select('provinsi', 'faktor_penyebab', 'jumlah_kasus')
            ->orderByDesc('jumlah_kasus')
            ->get()
            ->groupBy('provinsi');

        foreach ($list as $item) {
            $j = (int) $item->jumlah;
            $pct = $totalNasional > 0 ? round(($j / $totalNasional) * 100, 1) : 0;
            $slug = Str::slug($item->provinsi);

            $provFaktorRecords = $faktorGrouped[$item->provinsi] ?? collect();
            $totalPerceraianProv = (int) $provFaktorRecords->sum('jumlah_kasus');
            $topFaktorProv = $provFaktorRecords->first();

            $faktorBreakdown = [];
            foreach ($provFaktorRecords as $fRec) {
                $fPct = $totalPerceraianProv > 0 ? round(($fRec->jumlah_kasus / $totalPerceraianProv) * 100, 1) : 0;
                $faktorBreakdown[] = [
                    'faktor' => $fRec->faktor_penyebab,
                    'kasus' => (int) $fRec->jumlah_kasus,
                    'kasus_formatted' => number_format((int) $fRec->jumlah_kasus, 0, ',', '.'),
                    'persentase' => $fPct
                ];
            }

            $provData = [
                'kode_wilayah' => $item->kode_wilayah,
                'provinsi' => $item->provinsi,
                'slug' => $slug,
                'jumlah' => $j,
                'jumlah_formatted' => number_format($j, 0, ',', '.'),
                'persentase' => $pct,
                'has_data' => true,
                'url' => route('provinsi.detail', ['slug' => $slug]),
                'faktor_perceraian' => [
                    'total_kasus' => $totalPerceraianProv,
                    'total_kasus_formatted' => number_format($totalPerceraianProv, 0, ',', '.'),
                    'top_faktor' => $topFaktorProv ? [
                        'nama' => $topFaktorProv->faktor_penyebab,
                        'kasus' => (int) $topFaktorProv->jumlah_kasus,
                        'kasus_formatted' => number_format((int) $topFaktorProv->jumlah_kasus, 0, ',', '.'),
                        'persentase' => $totalPerceraianProv > 0 ? round(($topFaktorProv->jumlah_kasus / $totalPerceraianProv) * 100, 1) : 0
                    ] : null,
                    'breakdown' => $faktorBreakdown
                ]
            ];

            $data[$item->provinsi] = $provData;
            $data[$item->kode_wilayah] = $provData;
        }

        foreach ($list->take(10) as $idx => $item) {
            $j = (int) $item->jumlah;
            $slug = Str::slug($item->provinsi);
            $top10[] = [
                'rank' => sprintf('%02d', $idx + 1),
                'provinsi' => $item->provinsi,
                'slug' => $slug,
                'jumlah' => $j,
                'jumlah_formatted' => number_format($j, 0, ',', '.'),
                'url' => route('provinsi.detail', ['slug' => $slug])
            ];
        }

        $periodeLabel = ($bulan === 'Semua Bulan') ? "Januari–Desember $tahun" : "$bulan $tahun";
        if ($is2024 && $bulan === 'Semua Bulan') {
            $periodeLabel = "Januari–September 2024";
        }

        return response()->json([
            'status' => 'success',
            'tahun' => $tahun,
            'bulan' => $bulan,
            'periode_label' => $periodeLabel,
            'is_data_available' => true,
            'note' => $note,
            'total_nasional' => $totalNasional,
            'total_nasional_formatted' => number_format($totalNasional, 0, ',', '.'),
            'provinsi_tertinggi' => $tertinggi,
            'provinsi_terendah' => $terendah,
            'min_val' => $minVal ?: 0,
            'max_val' => $maxVal ?: 0,
            'data' => $data,
            'top_10' => $top10
        ]);
    }
}
