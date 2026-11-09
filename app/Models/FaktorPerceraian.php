<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaktorPerceraian extends Model
{
    use HasFactory;

    protected $table = 'faktor_perceraian';

    protected $fillable = [
        'provinsi',
        'tahun',
        'faktor_penyebab',
        'jumlah_kasus',
    ];

    /**
     * Scope filter provinsi
     */
    public function scopeFilterProvinsi($query, $provinsi)
    {
        if (!empty($provinsi)) {
            return $query->where('provinsi', $provinsi);
        }
        return $query;
    }

    /**
     * Scope filter tahun
     */
    public function scopeFilterTahun($query, $tahun)
    {
        if (!empty($tahun)) {
            return $query->where('tahun', $tahun);
        }
        return $query;
    }

    /**
     * Scope filter faktor penyebab
     */
    public function scopeFilterFaktor($query, $faktor)
    {
        if (!empty($faktor)) {
            return $query->where('faktor_penyebab', $faktor);
        }
        return $query;
    }
}
