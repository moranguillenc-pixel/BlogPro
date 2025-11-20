<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class Nota extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'titulo', 'contenido'];

    /**
     * Boot del modelo - CORREGIDO: Configuración correcta para eliminación en cascada
     */
    protected static function booted()
    {
        // Eliminación en cascada para actividades y recordatorios
        static::deleting(function ($nota) {
            // Si es eliminación suave (soft delete)
            if ($nota->isForceDeleting()) {
                // Eliminación permanente - eliminar relaciones permanentemente
                $nota->actividades()->forceDelete();
                $nota->recordatorios()->delete(); // recordatorios sin soft delete
            } else {
                // Eliminación suave - solo hacer soft delete de actividades
                $nota->actividades()->delete();
                // Los recordatorios no tienen soft delete, se eliminan permanentemente
                $nota->recordatorios()->delete();
            }
        });

        // Restauración en cascada
        static::restoring(function ($nota) {
            // Restaurar actividades que fueron eliminadas con la nota
            $nota->actividades()->withTrashed()->restore();
        });
    }

    /**
     * Accesor: Formatear título con estado
     */
    public function getTituloFormateadoAttribute()
    {
        $recordatorio = $this->recordatorios->first();
        
        if ($recordatorio && $recordatorio->completado) {
            return "[Completado] {$this->titulo}";
        }
        
        return $this->titulo;
    }

    /**
     * Relación: Nota pertenece a un usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Nota tiene muchos recordatorios
     */
    public function recordatorios(): HasMany
    {
        return $this->hasMany(Recordatorio::class);
    }

    /**
     * Relación: Nota tiene muchas actividades
     */
    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class);
    }

    /**
     * Helper: Obtener el recordatorio principal (primero)
     */
    public function getRecordatorioAttribute()
    {
        return $this->recordatorios->first();
    }

    /**
     * Verificar si la nota está completada
     */
    public function getEstaCompletadaAttribute(): bool
    {
        // Si existe columna 'completada' en la tabla notas
        if (isset($this->attributes['completada'])) {
            return (bool) $this->attributes['completada'];
        }
        
        // Si no, usar recordatorios como referencia
        $recordatorio = $this->recordatorios->first();
        return $recordatorio && $recordatorio->completado;
    }

    /**
     * Obtener el número total de actividades
     */
    public function getTotalActividadesAttribute(): int
    {
        return $this->actividades()->count();
    }

    /**
     * Obtener el número de actividades completadas
     */
    public function getActividadesCompletadasAttribute(): int
    {
        return $this->actividades()->where('completada', true)->count();
    }

    /**
     * Obtener el progreso de actividades en porcentaje
     */
    public function getProgresoActividadesAttribute(): float
    {
        $total = $this->total_actividades;
        if ($total === 0) {
            return 0;
        }
        
        return ($this->actividades_completadas / $total) * 100;
    }

    /**
     * Scope: Notas del usuario autenticado
     */
    public function scopeDelUsuario($query, $userId = null)
    {
        $userId = $userId ?: auth()->id();
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Notas con recordatorios vencidos
     */
    public function scopeConRecordatoriosVencidos($query)
    {
        return $query->whereHas('recordatorios', function ($q) {
            $q->where('fecha_vencimiento', '<', now())
              ->where('completado', false);
        });
    }

    /**
     * Scope: Notas sin actividades
     */
    public function scopeSinActividades($query)
    {
        return $query->doesntHave('actividades');
    }

    /**
     * Scope: Notas con actividades pendientes
     */
    public function scopeConActividadesPendientes($query)
    {
        return $query->whereHas('actividades', function ($q) {
            $q->where('completada', false);
        });
    }

    /**
     * Scope: Notas recientes
     */
    public function scopeRecientes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope: Solo notas activas (no eliminadas)
     */
    public function scopeActivas($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope: Incluir notas eliminadas
     */
    public function scopeConEliminadas($query)
    {
        return $query->withTrashed();
    }

    /**
     * Scope: Solo notas eliminadas
     */
    public function scopeSoloEliminadas($query)
    {
        return $query->onlyTrashed();
    }

    /**
     * Eliminar nota junto con sus relaciones (para eliminación en cascada)
     * CORREGIDO: Método mejorado y simplificado
     */
    public function eliminarCompleta(): bool
    {
        try {
            // El booted() ya maneja la eliminación en cascada
            // Solo necesitamos eliminar la nota
            return $this->delete();
            
        } catch (\Exception $e) {
            Log::error("Error al eliminar nota {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar nota permanentemente con todas sus relaciones
     */
    public function eliminarPermanentemente(): bool
    {
        try {
            return \DB::transaction(function () {
                // Eliminar permanentemente actividades
                $this->actividades()->forceDelete();
                
                // Eliminar recordatorios (sin soft delete)
                $this->recordatorios()->delete();
                
                // Eliminar permanentemente la nota
                return $this->forceDelete();
            });
            
        } catch (\Exception $e) {
            Log::error("Error al eliminar permanentemente nota {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Restaurar nota junto con sus relaciones
     * CORREGIDO: Más simple, el booted() maneja la restauración
     */
    public function restaurarCompleta(): bool
    {
        try {
            return $this->restore();
            
        } catch (\Exception $e) {
            Log::error("Error al restaurar nota {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si la nota puede ser eliminada
     */
    public function getPuedeEliminarAttribute(): bool
    {
        return $this->user_id === auth()->id();
    }

    /**
     * Verificar si la nota puede ser editada
     */
    public function getPuedeEditarAttribute(): bool
    {
        return $this->user_id === auth()->id();
    }

    /**
     * Obtener estadísticas de la nota
     */
    public function getEstadisticasAttribute(): array
    {
        return [
            'total_actividades' => $this->total_actividades,
            'actividades_completadas' => $this->actividades_completadas,
            'progreso' => $this->progreso_actividades,
            'total_recordatorios' => $this->recordatorios()->count(),
            'recordatorios_completados' => $this->recordatorios()->where('completado', true)->count(),
        ];
    }

    /**
     * Marcar nota como completada
     */
    public function marcarComoCompletada(): bool
    {
        try {
            // Si existe columna 'completada'
            if (isset($this->attributes['completada'])) {
                return $this->update(['completada' => true]);
            }
            
            // Si no, marcar todos los recordatorios como completados
            $this->recordatorios()->update(['completado' => true]);
            
            // Marcar todas las actividades como completadas
            $this->actividades()->update(['completada' => true]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error("Error al completar nota {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Marcar nota como pendiente
     */
    public function marcarComoPendiente(): bool
    {
        try {
            // Si existe columna 'completada'
            if (isset($this->attributes['completada'])) {
                return $this->update(['completada' => false]);
            }
            
            // Si no, marcar todos los recordatorios como pendientes
            $this->recordatorios()->update(['completado' => false]);
            
            // Marcar todas las actividades como pendientes
            $this->actividades()->update(['completada' => false]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error("Error al marcar como pendiente nota {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si la nota tiene actividades pendientes
     */
    public function tieneActividadesPendientes(): bool
    {
        return $this->actividades()->where('completada', false)->exists();
    }

    /**
     * Verificar si la nota tiene recordatorios pendientes
     */
    public function tieneRecordatoriosPendientes(): bool
    {
        return $this->recordatorios()->where('completado', false)->exists();
    }
}