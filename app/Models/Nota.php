<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Nota extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'titulo', 'contenido'];

    // Alcance global: Solo mostrará notas activas (no completadas)
    protected static function booted()
    {
        static::addGlobalScope('activa', function (Builder $builder) {
            $builder->whereHas('recordatorio', function ($query) {
                $query->where('fecha_vencimiento', '>=', now())
                      ->where('completado', false);
            });
        });
    }

    // Accesor: Formatear título con estado
    public function getTituloFormateadoAttribute()
    {
        if ($this->recordatorio) {
            return $this->recordatorio->completado ? "[Completado] {$this->titulo}" : $this->titulo;
        }
        return $this->titulo;
    }

    // Relación: Nota pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Nota tiene un recordatorio
    public function recordatorio()
    {
        return $this->hasOne(Recordatorio::class);
    }
}