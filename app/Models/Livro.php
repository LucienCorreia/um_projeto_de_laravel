<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Livro extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
    ];

    public function setUsuarioPublicadorIdAttribute($value): void
    {
        $this->attributes['usuario_publicador_id'] = $value;
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_publicador_id');
    }

    public function indices(): HasMany
    {
        return $this->hasMany(Indice::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('titulo', 'like', "%$search%");
    }

    public function scopeSearchByIndice($query, $search)
    {
        return $query->whereHas('indices', function ($query) use ($search) {
            $query->search($search);
        })->orWhereHas('indices.subindices', function ($query) use ($search) {
            $query->search($search);
        });;
    }
}
