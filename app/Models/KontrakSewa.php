<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KontrakSewa extends Model
{
    use HasFactory;

    protected $table = 'kontrak_sewa';

    protected $fillable = [
        'unit_kontrakan_id',
        'user_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'nominal_sewa',
        'ktp_path',
        'status',
        'catatan',
        'disetujui_oleh',
    ];

    /** Transisi status yang sah: dari => ke[]. */
    public const TRANSITIONS = [
        'diajukan' => ['disetujui', 'ditolak'],
        'disetujui' => ['aktif', 'ditolak'],
        'aktif' => ['selesai'],
        'ditolak' => [],
        'selesai' => [],
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'nominal_sewa' => 'decimal:2',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitKontrakan::class, 'unit_kontrakan_id');
    }

    public function penyewa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function dapatTransitionKe(string $status): bool
    {
        return in_array($status, self::TRANSITIONS[$this->status] ?? [], true);
    }

    public function statusLabel(): string
    {
        return ucfirst($this->status);
    }
}
