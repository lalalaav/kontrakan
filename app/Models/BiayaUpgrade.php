<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiayaUpgrade extends Model
{
    use HasFactory;

    protected $table = 'biaya_upgrade';

    protected $fillable = [
        'unit_kontrakan_id',
        'judul',
        'keterangan',
        'jumlah',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'tanggal' => 'date',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitKontrakan::class, 'unit_kontrakan_id');
    }
}
