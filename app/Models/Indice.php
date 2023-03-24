<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Indice extends Model
{
    use HasFactory;

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }

    public function subindices(): HasMany
    {
        return $this->hasMany(Indice::class, 'indice_pai_id');
    }

    public function indicePai(): BelongsTo
    {
        return $this->belongsTo(Indice::class, 'indice_pai_id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('titulo', 'like', "%$search%");
    }
}
