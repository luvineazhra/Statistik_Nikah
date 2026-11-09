<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPerceraian extends Model
{
    use HasFactory;

    protected $table = 'data_perceraian_flat';

    protected $fillable = [
        'provinsi',
        'tahun',
        'jumlah_perceraian',
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
}
