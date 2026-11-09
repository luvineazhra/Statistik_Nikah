<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProvinsiController extends Controller
{
    /**
     * Tampilkan Halaman Utama Kelola Provinsi
     */
    public function index(Request $request)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->route('login');
        }

        $search = $request->input('search');
        $filterStatus = $request->input('status');
        $filterPemetaan = $request->input('pemetaan');
        $sortBy = $request->input('sort_by', 'code');
        $sortOrder = strtolower($request->input('sort_order', 'asc')) === 'desc' ? 'desc' : 'asc';

        // 1. Dapatkan peta fitur GeoJSON batas wilayah Indonesia
        $geoFeaturesMap = $this->getGeoJsonFeaturesMap();

        // 2. Ambil seluruh data provinsi dari database untuk menghitung KPI akurat
        $allProvinces = Province::all();
        $totalProvinsi = $allProvinces->count();
        $totalAktif = $allProvinces->filter(function ($p) {
            return in_array($p->status, ['Aktif', 'Terverifikasi']);
        })->count();

        // Hitung pemetaan GeoJSON yang valid dari seluruh provinsi
        $mappedCodes = [];
        $unmappedCodes = [];

        foreach ($allProvinces as $p) {
            $mapping = $this->checkProvinceGeoJsonMapping($p, $geoFeaturesMap);
            if ($mapping['is_mapped']) {
                $mappedCodes[] = $p->code;
            } else {
                $unmappedCodes[] = $p->code;
            }
        }

        $totalTerhubungPeta = count($mappedCodes);
        $totalPerluPeriksa = count($unmappedCodes);

        // 3. Bangun query untuk tabel dengan pencarian, filter, dan pengurutan
        $query = Province::query();

        // Filter pencarian
        if (!empty($search)) {
            $query->search($search);
        }

        // Filter status
        if (!empty($filterStatus)) {
            $query->filterStatus($filterStatus);
        }

        // Filter status pemetaan
        if (!empty($filterPemetaan)) {
            if ($filterPemetaan === 'terhubung') {
                $query->whereIn('code', $mappedCodes);
            } elseif ($filterPemetaan === 'belum_terhubung') {
                $query->whereIn('code', $unmappedCodes);
            }
        }

        // Pengurutan
        $allowedSorts = ['code', 'name', 'status', 'updated_at', 'island_group'];
        if (in_array($sortBy, $allowedSorts)) {
            if ($sortBy === 'code') {
                // Urutkan kode numerik jika angka
                $query->orderByRaw('CAST(code AS UNSIGNED) ' . $sortOrder)->orderBy('code', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        } else {
            $query->orderByRaw('CAST(code AS UNSIGNED) ASC');
        }

        // 4. Paginate hasil (15 baris per halaman)
        $provinces = $query->paginate(15)->appends($request->all());

        // Lampirkan informasi pemetaan GeoJSON dan relasi statistik pada item paginasi
        foreach ($provinces as $item) {
            $item->geo_mapping = $this->checkProvinceGeoJsonMapping($item, $geoFeaturesMap);
            $item->statistical_relations = $item->hasStatisticalRelations();
            $item->nikah_stats = $item->getRekapNikahStats();
            $item->perceraian_stats = $item->getFaktorPerceraianStats();
        }

        // Daftar kelompok pulau untuk opsi dropdown form
        $islandGroups = [
            'Sumatera',
            'Jawa',
            'Bali & Nusa Tenggara',
            'Kalimantan',
            'Sulawesi',
            'Maluku',
            'Papua',
        ];

        return view('provinsi', compact(
            'provinces',
            'totalProvinsi',
            'totalAktif',
            'totalTerhubungPeta',
            'totalPerluPeriksa',
            'search',
            'filterStatus',
            'filterPemetaan',
            'sortBy',
            'sortOrder',
            'islandGroups'
        ));
    }

    /**
     * Tambah Provinsi Baru
     */
    public function store(Request $request)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->route('login');
        }

        $request->validate([
            'code'         => 'required|string|max:10|unique:provinces,code',
            'name'         => 'required|string|max:100',
            'island_group' => 'nullable|string|max:100',
            'status'       => 'required|in:Aktif,Nonaktif',
        ], [
            'code.required' => 'Kode provinsi wajib diisi.',
            'code.unique'   => 'Kode provinsi sudah digunakan oleh wilayah lain. Harap gunakan kode unik.',
            'name.required' => 'Nama provinsi wajib diisi.',
            'status.required' => 'Status provinsi wajib dipilih.',
            'status.in'     => 'Status provinsi harus Aktif atau Nonaktif.',
        ]);

        try {
            $province = Province::create([
                'code'               => trim($request->code),
                'name'               => trim($request->name),
                'island_group'       => $request->island_group ?: null,
                'status'             => $request->status,
                'total_marriages'    => 0,
                'dispensation_ratio' => 1.00,
            ]);

            return redirect()->route('provinsi')->with('success', "Provinsi \"{$province->name}\" (Kode {$province->code}) berhasil ditambahkan ke database.");
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'Gagal menyimpan data provinsi. Silakan coba lagi.'])->withInput();
        }
    }

    /**
     * Perbarui Data Provinsi
     */
    public function update(Request $request, $id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->route('login');
        }

        $province = Province::findOrFail($id);
        $relations = $province->hasStatisticalRelations();

        // Validasi kode: jika sudah memiliki relasi statistik, kode tidak boleh diubah
        if ($relations['has_relations']) {
            if ($request->filled('code') && trim($request->code) !== (string) $province->code) {
                return back()->withErrors([
                    'code' => "Kode provinsi ({$province->code}) tidak dapat diubah karena telah terhubung dengan {$relations['nikah_count']} catatan pernikahan dan {$relations['perceraian_count']} catatan perceraian historis (2023–2025)."
                ])->withInput();
            }
            $targetCode = $province->code;
        } else {
            $request->validate([
                'code' => ['required', 'string', 'max:10', Rule::unique('provinces', 'code')->ignore($province->id)],
            ], [
                'code.required' => 'Kode provinsi wajib diisi.',
                'code.unique'   => 'Kode provinsi sudah digunakan oleh wilayah lain.',
            ]);
            $targetCode = trim($request->code);
        }

        $request->validate([
            'name'         => 'required|string|max:100',
            'island_group' => 'nullable|string|max:100',
            'status'       => 'required|in:Aktif,Nonaktif',
        ], [
            'name.required' => 'Nama provinsi wajib diisi.',
            'status.required' => 'Status provinsi wajib dipilih.',
            'status.in'     => 'Status provinsi harus Aktif atau Nonaktif.',
        ]);

        try {
            $province->update([
                'code'         => $targetCode,
                'name'         => trim($request->name),
                'island_group' => $request->island_group ?: null,
                'status'       => $request->status,
            ]);

            return redirect()->route('provinsi')->with('success', "Data provinsi \"{$province->name}\" berhasil diperbarui.");
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'Gagal memperbarui data provinsi. Silakan coba lagi.'])->withInput();
        }
    }

    /**
     * Beralih status Aktif / Nonaktif
     */
    public function toggleStatus(Request $request, $id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->route('login');
        }

        $province = Province::findOrFail($id);
        $currentStatus = in_array($province->status, ['Aktif', 'Terverifikasi']) ? 'Aktif' : 'Nonaktif';
        $newStatus = ($currentStatus === 'Aktif') ? 'Nonaktif' : 'Aktif';

        $province->update(['status' => $newStatus]);

        return redirect()->route('provinsi')->with('success', "Status provinsi \"{$province->name}\" berhasil diubah menjadi {$newStatus}. Data historis tetap utuh.");
    }

    /**
     * Hapus Provinsi Secara Aman
     */
    public function destroy($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->route('login');
        }

        $province = Province::findOrFail($id);
        $relations = $province->hasStatisticalRelations();

        // Tolak jika memiliki data relasi pernikahan / perceraian historis
        if ($relations['has_relations']) {
            return redirect()->route('provinsi')->with('error', "Provinsi \"{$province->name}\" (Kode {$province->code}) tidak dapat dihapus permanen karena masih terhubung dengan {$relations['nikah_count']} catatan pernikahan dan {$relations['perceraian_count']} catatan perceraian historis (2023–2025). Silakan gunakan opsi \"Nonaktifkan\" agar integritas data tetap terjaga.");
        }

        try {
            $name = $province->name;
            $province->delete();

            return redirect()->route('provinsi')->with('success', "Provinsi \"{$name}\" berhasil dihapus secara permanen.");
        } catch (\Exception $e) {
            return redirect()->route('provinsi')->with('error', 'Gagal menghapus provinsi. Terjadi kendala pada database.');
        }
    }

    /**
     * Mengambil data detail lengkap provinsi via JSON (untuk Modal Detail)
     */
    public function detailJson($id)
    {
        if (!session()->get('is_logged_in')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $province = Province::findOrFail($id);
        $geoFeaturesMap = $this->getGeoJsonFeaturesMap();
        $mapping = $this->checkProvinceGeoJsonMapping($province, $geoFeaturesMap);
        $nikahStats = $province->getRekapNikahStats();
        $perceraianStats = $province->getFaktorPerceraianStats();
        $relations = $province->hasStatisticalRelations();

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $province->id,
                'code' => $province->code,
                'name' => $province->name,
                'island_group' => $province->island_group ?: '-',
                'status' => $province->status,
                'created_at' => $province->created_at ? $province->created_at->format('d/m/Y H:i') : '-',
                'updated_at' => $province->updated_at ? $province->updated_at->format('d/m/Y H:i') : '-',
                'geo_mapping' => $mapping,
                'nikah' => [
                    'count' => $nikahStats['count'],
                    'total' => $nikahStats['total'],
                    'total_formatted' => number_format($nikahStats['total'], 0, ',', '.'),
                    'years' => $nikahStats['years']
                ],
                'perceraian' => [
                    'count' => $perceraianStats['count'],
                    'total' => $perceraianStats['total'],
                    'total_formatted' => number_format($perceraianStats['total'], 0, ',', '.'),
                    'years' => $perceraianStats['years'],
                    'top_faktor' => $perceraianStats['top_faktor']
                ],
                'has_relations' => $relations['has_relations'],
                'nikah_count' => $relations['nikah_count'],
                'perceraian_count' => $relations['perceraian_count'],
            ]
        ]);
    }

    /**
     * Membaca dan memetakan struktur file GeoJSON Indonesia
     */
    protected function getGeoJsonFeaturesMap(): array
    {
        $geoJsonPath = public_path('geojson/indonesia-provinces.json');
        if (!file_exists($geoJsonPath)) {
            return [
                'by_code' => [],
                'by_name' => [],
                'by_slug' => [],
            ];
        }

        $content = file_get_contents($geoJsonPath);
        $raw = json_decode($content, true);

        if (!isset($raw['features']) || !is_array($raw['features'])) {
            return [
                'by_code' => [],
                'by_name' => [],
                'by_slug' => [],
            ];
        }

        $map = [
            'by_code' => [],
            'by_name' => [],
            'by_slug' => [],
        ];

        foreach ($raw['features'] as $feature) {
            $props = $feature['properties'] ?? [];
            $code = (string) ($props['kode_wilayah'] ?? ($props['kode'] ?? ''));
            $name = (string) ($props['name'] ?? ($props['NAME_1'] ?? ''));
            $altName = (string) ($props['Propinsi'] ?? '');

            $info = [
                'code' => $code,
                'name' => $name,
                'alt_name' => $altName,
                'feature_id' => $feature['id'] ?? null,
            ];

            if ($code !== '') {
                $map['by_code'][$code] = $info;
            }
            if ($name !== '') {
                $map['by_name'][strtolower(trim($name))] = $info;
                $map['by_slug'][Str::slug($name)] = $info;
            }
            if ($altName !== '') {
                $map['by_name'][strtolower(trim($altName))] = $info;
                $map['by_slug'][Str::slug($altName)] = $info;
            }
        }

        return $map;
    }

    /**
     * Periksa keterhubungan provinsi database dengan poligon/geometri GeoJSON
     */
    protected function checkProvinceGeoJsonMapping($province, array $geoMap): array
    {
        $code = (string) $province->code;
        $nameLower = strtolower(trim($province->name));
        $nameSlug = Str::slug($province->name);

        // 1. Pencocokan utama menggunakan Kode Provinsi (paling konsisten dan handal)
        if (isset($geoMap['by_code'][$code])) {
            $matched = $geoMap['by_code'][$code];
            return [
                'is_mapped'    => true,
                'method'       => 'Kode Wilayah (' . $code . ')',
                'geojson_code' => $matched['code'],
                'geojson_name' => $matched['name'],
                'note'         => 'Geometri batas wilayah terhubung sempurna berdasarkan kode wilayah BPS.',
            ];
        }

        // 2. Pencocokan alternatif menggunakan Nama Provinsi
        if (isset($geoMap['by_name'][$nameLower])) {
            $matched = $geoMap['by_name'][$nameLower];
            return [
                'is_mapped'    => true,
                'method'       => 'Nama Wilayah (' . $matched['name'] . ')',
                'geojson_code' => $matched['code'],
                'geojson_name' => $matched['name'],
                'note'         => 'Geometri batas wilayah terhubung berdasarkan kesesuaian nama provinsi.',
            ];
        }

        // 3. Pencocokan alternatif menggunakan Slug Nama
        if (isset($geoMap['by_slug'][$nameSlug])) {
            $matched = $geoMap['by_slug'][$nameSlug];
            return [
                'is_mapped'    => true,
                'method'       => 'Slug Nama (' . $matched['name'] . ')',
                'geojson_code' => $matched['code'],
                'geojson_name' => $matched['name'],
                'note'         => 'Geometri batas wilayah terhubung berdasarkan normalisasi teks.',
            ];
        }

        // Belum terhubung
        return [
            'is_mapped'    => false,
            'method'       => null,
            'geojson_code' => null,
            'geojson_name' => null,
            'note'         => 'Poligon/geometri batas wilayah untuk kode ' . $code . ' belum ditemukan pada file GeoJSON.',
        ];
    }

    /**
     * Halaman Publik/Statistik Detail Provinsi berdasarkan Slug (Mempertahankan fitur yang ada)
     */
    public function show($slug)
    {
        // Public route - tidak memerlukan autentikasi
        // Match province name by slug dari tabel rekapitulasi Simkah
        $provinces = DB::table('rekap_nikah_provinsi')
            ->select('provinsi', 'kode_wilayah')
            ->distinct()
            ->get();

        $matchedProvinsi = null;
        $matchedKode = null;

        foreach ($provinces as $p) {
            if (Str::slug($p->provinsi) === strtolower($slug) || strtolower($p->provinsi) === strtolower(str_replace('-', ' ', $slug))) {
                $matchedProvinsi = $p->provinsi;
                $matchedKode = $p->kode_wilayah;
                break;
            }
        }

        if (!$matchedProvinsi) {
            $matchedProvinsi = 'Jawa Barat';
            $matchedKode = '32';
        }

        // Totals per year dari tabel rekapitulasi Simkah
        $total2023 = (int) DB::table('rekap_nikah_provinsi')
            ->where('provinsi', $matchedProvinsi)
            ->where('tahun', 2023)
            ->sum('jumlah_pernikahan');

        $total2024 = (int) DB::table('rekap_nikah_provinsi')
            ->where('provinsi', $matchedProvinsi)
            ->where('tahun', 2024)
            ->whereIn('bulan', ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September'])
            ->sum('jumlah_pernikahan');

        $total2025 = (int) DB::table('rekap_nikah_provinsi')
            ->where('provinsi', $matchedProvinsi)
            ->where('tahun', 2025)
            ->sum('jumlah_pernikahan');

        $totalPernikahan = $total2023 + $total2024 + $total2025;

        // Monthly trend for 2025
        $monthsOrder = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $raw2025 = DB::table('rekap_nikah_provinsi')
            ->where('provinsi', $matchedProvinsi)
            ->where('tahun', 2025)
            ->pluck('jumlah_pernikahan', 'bulan')
            ->toArray();

        $raw2024 = DB::table('rekap_nikah_provinsi')
            ->where('provinsi', $matchedProvinsi)
            ->where('tahun', 2024)
            ->pluck('jumlah_pernikahan', 'bulan')
            ->toArray();

        $monthlyTrend = [];
        $maxMonthVal = -1;
        $minMonthVal = PHP_INT_MAX;
        $bulanTertinggi = '-';
        $bulanTerendah = '-';

        foreach ($monthsOrder as $bln) {
            $val2025 = (int)($raw2025[$bln] ?? 0);
            $monthlyTrend[$bln] = $val2025;

            if ($val2025 > $maxMonthVal) {
                $maxMonthVal = $val2025;
                $bulanTertinggi = $bln;
            }
            if ($val2025 < $minMonthVal && $val2025 > 0) {
                $minMonthVal = $val2025;
                $bulanTerendah = $bln;
            }
        }

        $growthYoY = $total2023 > 0 ? round((($total2025 - $total2023) / $total2023) * 100, 1) : 0;

        // Data Faktor Perceraian BPS untuk Provinsi ini
        $faktorPerceraianProvinsi = DB::table('faktor_perceraian')
            ->where('provinsi', $matchedProvinsi)
            ->where('tahun', 2025)
            ->orderByDesc('jumlah_kasus')
            ->get();
        $totalPerceraianProvinsi = (int) $faktorPerceraianProvinsi->sum('jumlah_kasus');
        $topFaktorProvinsi = $faktorPerceraianProvinsi->first();

        return view('provinsi-detail', compact(
            'matchedProvinsi',
            'matchedKode',
            'totalPernikahan',
            'total2023',
            'total2024',
            'total2025',
            'monthlyTrend',
            'raw2024',
            'bulanTertinggi',
            'bulanTerendah',
            'growthYoY',
            'faktorPerceraianProvinsi',
            'totalPerceraianProvinsi',
            'topFaktorProvinsi'
        ));
    }
}
