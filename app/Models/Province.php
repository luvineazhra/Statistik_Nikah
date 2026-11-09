<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'island_group',
        'total_marriages',
        'dispensation_ratio',
        'status',
    ];

    /**
     * Scope filter pencarian berdasarkan kode, nama, atau pulau
     */
    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('island_group', 'LIKE', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Scope filter status aktif / nonaktif
     */
    public function scopeFilterStatus($query, $status)
    {
        if (!empty($status)) {
            if (strtolower($status) === 'aktif') {
                $query->whereIn('status', ['Aktif', 'Terverifikasi']);
            } elseif (strtolower($status) === 'nonaktif') {
                $query->where('status', 'Nonaktif');
            } else {
                $query->where('status', $status);
            }
        }
        return $query;
    }

    /**
     * Hitung ringkasan data pernikahan dari tabel rekap_nikah_provinsi
     */
    public function getRekapNikahStats()
    {
        $nikahBase = \Illuminate\Support\Facades\DB::table('rekap_nikah_provinsi')
            ->where(function ($q) {
                $q->where('kode_wilayah', $this->code)
                  ->orWhere('provinsi', $this->name);
            });

        $count = (clone $nikahBase)->count();
        $total = (int) (clone $nikahBase)->sum('jumlah_pernikahan');

        $years = \Illuminate\Support\Facades\DB::table('rekap_nikah_provinsi')
            ->where(function ($q) {
                $q->where('kode_wilayah', $this->code)
                  ->orWhere('provinsi', $this->name);
            })
            ->distinct()
            ->pluck('tahun')
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        return [
            'count' => $count,
            'total' => $total,
            'years' => $years
        ];
    }

    /**
     * Hitung ringkasan data perkara perceraian dari tabel faktor_perceraian
     */
    public function getFaktorPerceraianStats()
    {
        $perceraianBase = \Illuminate\Support\Facades\DB::table('faktor_perceraian')
            ->where('provinsi', $this->name);

        $count = (clone $perceraianBase)->count();
        $total = (int) (clone $perceraianBase)->sum('jumlah_kasus');

        $years = \Illuminate\Support\Facades\DB::table('faktor_perceraian')
            ->where('provinsi', $this->name)
            ->distinct()
            ->pluck('tahun')
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        $topFaktor = \Illuminate\Support\Facades\DB::table('faktor_perceraian')
            ->where('provinsi', $this->name)
            ->orderByDesc('jumlah_kasus')
            ->value('faktor_penyebab') ?: '-';

        return [
            'count' => $count,
            'total' => $total,
            'years' => $years,
            'top_faktor' => $topFaktor
        ];
    }

    /**
     * Cek apakah provinsi memiliki relasi data historis di tabel statistik
     */
    public function hasStatisticalRelations()
    {
        $nikahCount = \Illuminate\Support\Facades\DB::table('rekap_nikah_provinsi')
            ->where(function ($q) {
                $q->where('kode_wilayah', $this->code)
                  ->orWhere('provinsi', $this->name);
            })->count();

        $perceraianCount = \Illuminate\Support\Facades\DB::table('faktor_perceraian')
            ->where('provinsi', $this->name)
            ->count();

        $flatNikahCount = 0;
        if (\Illuminate\Support\Facades\Schema::hasTable('data_pernikahan_2023_2025_flat')) {
            $flatNikahCount = \Illuminate\Support\Facades\DB::table('data_pernikahan_2023_2025_flat')
                ->where(function ($q) {
                    $q->where('kode_wilayah', $this->code)
                      ->orWhere('provinsi', $this->name);
                })->count();
        }

        $marriageRecordsCount = 0;
        if (\Illuminate\Support\Facades\Schema::hasTable('marriage_records')) {
            $marriageRecordsCount = \Illuminate\Support\Facades\DB::table('marriage_records')
                ->where('province_id', $this->id)
                ->count();
        }

        $totalRelasi = $nikahCount + $perceraianCount + $flatNikahCount + $marriageRecordsCount;

        return [
            'has_relations' => $totalRelasi > 0,
            'nikah_count' => max($nikahCount, $flatNikahCount),
            'perceraian_count' => $perceraianCount,
            'marriage_records_count' => $marriageRecordsCount,
            'total_count' => $totalRelasi
        ];
    }
}
