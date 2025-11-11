<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recordatorio extends Model
{
    use HasFactory;

    protected $fillable = ['nota_id', 'fecha_vencimiento', 'completado'];

    protected $casts = [
        'fecha_vencimiento' => 'datetime',
        'completado' => 'boolean',
    ];

    // Relación: Recordatorio pertenece a una nota
    public function nota()
    {
        return $this->belongsTo(Nota::class);
    }
}