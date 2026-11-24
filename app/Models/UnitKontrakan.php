<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitKontrakan extends Model
{
    use HasFactory;

    protected $table = 'unit_kontrakan';

    protected $fillable = [
        'lokasi_id',
        'nomor_unit',
        'tarif_sewa',
        'jumlah_kamar',
        'luas_bangunan',
        'fasilitas',
        'status',
        'izin_hewan',
        'foto',
    ];

    protected function casts(): array
    {
        return [
            'tarif_sewa' => 'decimal:2',
            'luas_bangunan' => 'decimal:2',
            'izin_hewan' => 'boolean',
        ];
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function kontrak(): HasMany
    {
        return $this->hasMany(KontrakSewa::class);
    }

    public function laporanKerusakan(): HasMany
    {
        return $this->hasMany(LaporanKerusakan::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'terisi' => 'Terisi',
            'perbaikan' => 'Perbaikan',
            default => 'Kosong',
        };
    }
}
