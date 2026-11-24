<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanKerusakan extends Model
{
    use HasFactory;

    protected $table = 'laporan_kerusakan';

    protected $fillable = [
        'unit_kontrakan_id',
        'user_id',
        'judul',
        'deskripsi',
        'foto',
        'status',
        'biaya_material',
        'biaya_upah',
    ];

    protected function casts(): array
    {
        return [
            'biaya_material' => 'decimal:2',
            'biaya_upah' => 'decimal:2',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitKontrakan::class, 'unit_kontrakan_id');
    }

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function totalBiaya(): float
    {
        return (float) $this->biaya_material + (float) $this->biaya_upah;
    }
}
