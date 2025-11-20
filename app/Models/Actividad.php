<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Actividad extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Especificar el nombre de la tabla explícitamente
     */
    protected $table = 'actividades';

    /**
     * Los atributos que son asignables en masa.
     */
    protected $fillable = [
        'nota_id',
        'user_id',
        'titulo',
        'descripcion',
        'fecha_limite',
        'completada',
        'prioridad'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'fecha_limite' => 'datetime',
        'completada' => 'boolean',
        'prioridad' => 'integer'
    ];

    /**
     * Valores por defecto para los atributos.
     */
    protected $attributes = [
        'completada' => false,
        'prioridad' => 2
    ];

    /**
     * Relación: La actividad pertenece a una nota.
     */
    public function nota(): BelongsTo
    {
        return $this->belongsTo(Nota::class);
    }

    /**
     * Relación: La actividad pertenece a un usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para actividades completadas.
     */
    public function scopeCompletadas($query)
    {
        return $query->where('completada', true);
    }

    /**
     * Scope para actividades pendientes.
     */
    public function scopePendientes($query)
    {
        return $query->where('completada', false);
    }

    /**
     * Scope para actividades por prioridad.
     */
    public function scopePorPrioridad($query, $prioridad)
    {
        return $query->where('prioridad', $prioridad);
    }

    /**
     * Scope para actividades del usuario autenticado.
     */
    public function scopeDelUsuario($query, $userId = null)
    {
        $userId = $userId ?: auth()->id();
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para actividades vencidas.
     */
    public function scopeVencidas($query)
    {
        return $query->where('completada', false)
                    ->where('fecha_limite', '<', now());
    }

    /**
     * Scope para actividades próximas a vencer (próximos 3 días).
     */
    public function scopeProximasAVencer($query)
    {
        return $query->where('completada', false)
                    ->whereBetween('fecha_limite', [now(), now()->addDays(3)]);
    }

    /**
     * Obtener el texto de la prioridad.
     */
    public function getTextoPrioridadAttribute(): string
    {
        return match($this->prioridad) {
            1 => 'Baja',
            2 => 'Media',
            3 => 'Alta',
            default => 'Desconocida'
        };
    }

    /**
     * Obtener la clase CSS para la prioridad.
     */
    public function getClasePrioridadAttribute(): string
    {
        return match($this->prioridad) {
            1 => 'badge bg-success',
            2 => 'badge bg-warning',
            3 => 'badge bg-danger',
            default => 'badge bg-secondary'
        };
    }

    /**
     * Obtener la clase CSS para el estado de completada.
     */
    public function getClaseEstadoAttribute(): string
    {
        return $this->completada ? 'badge bg-success' : 'badge bg-warning';
    }

    /**
     * Obtener el texto del estado.
     */
    public function getTextoEstadoAttribute(): string
    {
        return $this->completada ? 'Completada' : 'Pendiente';
    }

    /**
     * Verificar si la actividad está vencida.
     */
    public function getEstaVencidaAttribute(): bool
    {
        if (!$this->fecha_limite || $this->completada) {
            return false;
        }
        
        return $this->fecha_limite->isPast();
    }

    /**
     * Verificar si la actividad está próxima a vencer.
     */
    public function getEstaProximaAttribute(): bool
    {
        if (!$this->fecha_limite || $this->completada) {
            return false;
        }
        
        return $this->fecha_limite->isFuture() && 
               $this->fecha_limite->diffInDays(now()) <= 3;
    }

    /**
     * Obtener días restantes para la fecha límite.
     */
    public function getDiasRestantesAttribute(): ?int
    {
        if (!$this->fecha_limite) {
            return null;
        }
        
        return $this->fecha_limite->diffInDays(now(), false) * -1;
    }

    /**
     * Marcar actividad como completada.
     */
    public function marcarComoCompletada(): bool
    {
        return $this->update(['completada' => true]);
    }

    /**
     * Marcar actividad como pendiente.
     */
    public function marcarComoPendiente(): bool
    {
        return $this->update(['completada' => false]);
    }

    /**
     * Alternar estado de completada.
     */
    public function alternarEstado(): bool
    {
        return $this->update(['completada' => !$this->completada]);
    }

    /**
     * Boot del modelo - CORREGIDO
     * Se elimina la asignación automática de user_id que causaba el error
     */
    protected static function boot()
    {
        parent::boot();

        // Solo mantener validaciones seguras
        static::creating(function ($actividad) {
            // Establecer prioridad por defecto si no se proporciona
            if (is_null($actividad->prioridad)) {
                $actividad->prioridad = 2; // Media
            }
        });

        // Validación opcional de consistencia usuario-nota
        static::saving(function ($actividad) {
            if ($actividad->nota && $actividad->nota->user_id !== $actividad->user_id) {
                throw new \Exception('La actividad debe pertenecer al mismo usuario que la nota');
            }
        });
    }
}