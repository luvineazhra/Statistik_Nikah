<?php

namespace App\Http\Controllers;

use App\Models\FaktorPerceraian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FaktorPerceraianController extends Controller
{
    /**
     * Tampilkan data perkara & faktor penyebab perceraian BPS (CRUD & Statistik)
     */
    public function index(Request $request)
    {
        $selectedProvinsi = $request->input('provinsi');
        $selectedTahun = $request->input('tahun');
        $selectedFaktor = $request->input('faktor_penyebab');
        $search = $request->input('search');

        // Query builder untuk filter
        $query = FaktorPerceraian::query()
            ->filterProvinsi($selectedProvinsi)
            ->filterTahun($selectedTahun)
            ->filterFaktor($selectedFaktor);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('provinsi', 'LIKE', "%{$search}%")
                  ->orWhere('faktor_penyebab', 'LIKE', "%{$search}%")
                  ->orWhere('tahun', 'LIKE', "%{$search}%");
            });
        }

        // Summary statistics
        $statsQuery = clone $query;
        $totalKasus = (int) $statsQuery->sum('jumlah_kasus');
        $totalRecords = $statsQuery->count();

        // Rekapitulasi per faktor penyebab
        $rekapPerFaktor = FaktorPerceraian::query()
            ->filterProvinsi($selectedProvinsi)
            ->filterTahun($selectedTahun)
            ->select('faktor_penyebab', DB::raw('SUM(jumlah_kasus) as total'))
            ->groupBy('faktor_penyebab')
            ->orderByDesc('total')
            ->pluck('total', 'faktor_penyebab')
            ->toArray();

        // Faktor terbanyak
        $faktorTerbanyakNama = !empty($rekapPerFaktor) ? array_key_first($rekapPerFaktor) : '-';
        $faktorTerbanyakTotal = !empty($rekapPerFaktor) ? reset($rekapPerFaktor) : 0;

        // Provinsi dengan perkara tertinggi
        $provinsiTertinggiQuery = FaktorPerceraian::query()
            ->filterTahun($selectedTahun)
            ->select('provinsi', DB::raw('SUM(jumlah_kasus) as total'))
            ->groupBy('provinsi')
            ->orderByDesc('total')
            ->first();
        $provinsiTertinggi = $provinsiTertinggiQuery ? $provinsiTertinggiQuery->provinsi : 'Jawa Barat';

        $perPage = (int) $request->input('per_page', 15);
        if ($perPage < 5 || $perPage > 100) {
            $perPage = 15;
        }

        // Paginated results dengan urutan baru:
        // 1. Tahun Terbaru (desc: 2025 -> 2024 -> 2023)
        // 2. Faktor Penyebab Perceraian (asc)
        // 3. Provinsi Alfabetis (asc: Aceh, Bali, Banten, ...)
        $faktorList = $query->orderBy('tahun', 'desc')
            ->orderBy('faktor_penyebab', 'asc')
            ->orderBy('provinsi', 'asc')
            ->paginate($perPage)
            ->appends($request->all());

        // Daftar Provinsi untuk dropdown
        $daftarProvinsiConfig = config('provinces.list', []);
        $listProvinsiNama = array_column($daftarProvinsiConfig, 'name');
        if (empty($listProvinsiNama)) {
            $listProvinsiNama = FaktorPerceraian::distinct()->orderBy('provinsi')->pluck('provinsi')->toArray();
        }

        $daftarTahun = [2025, 2024, 2023];
        $daftarFaktor = FaktorPerceraian::distinct()->orderBy('faktor_penyebab')->pluck('faktor_penyebab')->toArray();
        if (empty($daftarFaktor)) {
            $daftarFaktor = [
                'Perselisihan dan Pertengkaran Terus Menerus',
                'Ekonomi',
                'Meninggalkan Salah Satu Pihak',
                'Kekerasan Dalam Rumah Tangga (KDRT)',
                'Judi',
                'Mabuk',
                'Madat',
                'Zina',
                'Poligami',
                'Dihukum Penjara',
                'Cacat Badan',
                'Kawin Paksa',
                'Murtad',
                'Lain-lain'
            ];
        }

        $data = compact(
            'faktorList',
            'listProvinsiNama',
            'daftarTahun',
            'daftarFaktor',
            'selectedProvinsi',
            'selectedTahun',
            'selectedFaktor',
            'search',
            'totalKasus',
            'totalRecords',
            'rekapPerFaktor',
            'faktorTerbanyakNama',
            'faktorTerbanyakTotal',
            'provinsiTertinggi'
        );

        if (view()->exists('admin.faktor-perceraian.index')) {
            return view('admin.faktor-perceraian.index', $data);
        }

        return view('faktor-perceraian', $data);
    }

    /**
     * Simpan data faktor perceraian baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'provinsi'        => 'required|string|max:100',
            'tahun'           => 'required|integer|min:2000|max:2030',
            'faktor_penyebab' => 'required|string|max:150',
            'jumlah_kasus'    => 'required|integer|min:0',
        ]);

        FaktorPerceraian::updateOrCreate(
            [
                'provinsi'        => $request->provinsi,
                'tahun'           => $request->tahun,
                'faktor_penyebab' => $request->faktor_penyebab,
            ],
            [
                'jumlah_kasus'    => $request->jumlah_kasus,
            ]
        );

        return redirect()->back()->with('success', 'Data faktor penyebab perceraian berhasil ditambahkan.');
    }

    /**
     * Perbarui data faktor perceraian
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'provinsi'        => 'required|string|max:100',
            'tahun'           => 'required|integer|min:2000|max:2030',
            'faktor_penyebab' => 'required|string|max:150',
            'jumlah_kasus'    => 'required|integer|min:0',
        ]);

        $record = FaktorPerceraian::findOrFail($id);
        $record->update([
            'provinsi'        => $request->provinsi,
            'tahun'           => $request->tahun,
            'faktor_penyebab' => $request->faktor_penyebab,
            'jumlah_kasus'    => $request->jumlah_kasus,
        ]);

        return redirect()->back()->with('success', 'Data faktor penyebab perceraian berhasil diperbarui.');
    }

    /**
     * Hapus data faktor perceraian
     */
    public function destroy($id)
    {
        $record = FaktorPerceraian::findOrFail($id);
        $record->delete();

        return redirect()->back()->with('success', 'Data faktor penyebab perceraian berhasil dihapus.');
    }
}
