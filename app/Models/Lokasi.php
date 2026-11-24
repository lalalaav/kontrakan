<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi';

    protected $fillable = [
        'nama',
        'alamat',
        'maps_url',
    ];

    public function unitKontrakan(): HasMany
    {
        return $this->hasMany(UnitKontrakan::class);
    }
}
