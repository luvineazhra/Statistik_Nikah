<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapNikahProvinsi extends Model
{
    use HasFactory;

    protected $table = 'rekap_nikah_provinsi';

    public $timestamps = true;

    protected $fillable = [
        'kode_wilayah',
        'provinsi',
        'tahun',
        'bulan',
        'jumlah_peristiwa',
        'jumlah_pernikahan',
        'sumber_data',
    ];

    /**
     * Accessor untuk kompatibilitas alias jumlah_nikah
     */
    public function getJumlahNikahAttribute()
    {
        return (int) ($this->attributes['jumlah_peristiwa'] ?? $this->attributes['jumlah_pernikahan'] ?? 0);
    }

    /**
     * Accessor untuk jumlah_peristiwa dengan fallback ke jumlah_pernikahan
     */
    public function getJumlahPeristiwaAttribute($value)
    {
        return $value !== null ? (int) $value : (int) ($this->attributes['jumlah_pernikahan'] ?? 0);
    }

    /**
     * Accessor untuk jumlah_pernikahan dengan fallback ke jumlah_peristiwa
     */
    public function getJumlahPernikahanAttribute($value)
    {
        return $value !== null ? (int) $value : (int) ($this->attributes['jumlah_peristiwa'] ?? 0);
    }

    /**
     * Scope pencarian nama provinsi atau kode wilayah
     */
    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('provinsi', 'LIKE', "%{$search}%")
                  ->orWhere('kode_wilayah', 'LIKE', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Scope filter tahun
     */
    public function scopeFilterTahun($query, $tahun)
    {
        if (!empty($tahun) && $tahun !== 'Semua Tahun') {
            $query->where('tahun', $tahun);
        }
        return $query;
    }

    /**
     * Scope filter provinsi
     */
    public function scopeFilterProvinsi($query, $provinsi)
    {
        if (!empty($provinsi) && $provinsi !== 'Semua Provinsi') {
            $query->where('provinsi', $provinsi);
        }
        return $query;
    }

    /**
     * Scope filter bulan
     */
    public function scopeFilterBulan($query, $bulan)
    {
        if (!empty($bulan) && $bulan !== 'Semua Bulan') {
            $query->where('bulan', $bulan);
        }
        return $query;
    }
}
