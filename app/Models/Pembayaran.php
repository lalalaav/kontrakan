<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'kontrak_sewa_id',
        'periode',
        'jumlah',
        'tanggal_jatuh_tempo',
        'tanggal_bayar',
        'bukti_path',
        'status',
        'catatan',
        'diverifikasi_oleh',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'tanggal_jatuh_tempo' => 'date',
            'tanggal_bayar' => 'date',
        ];
    }

    public function kontrak(): BelongsTo
    {
        return $this->belongsTo(KontrakSewa::class, 'kontrak_sewa_id');
    }

    public function diverifikasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function statusLabel(): string
    {
        return ucfirst($this->status);
    }
}
