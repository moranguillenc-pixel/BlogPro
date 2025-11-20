<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recordatorio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nota_id', 
        'fecha_vencimiento', 
        'completado'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'datetime',
        'completado' => 'boolean',
    ];

    /**
     * Relación: Recordatorio pertenece a una nota
     * MEJORADO: Agregar tipo de retorno
     */
    public function nota(): BelongsTo
    {
        return $this->belongsTo(Nota::class);
    }

    /**
     * Accesor: Verificar si el recordatorio está vencido
     */
    public function getEstaVencidoAttribute(): bool
    {
        return !$this->completado && $this->fecha_vencimiento->isPast();
    }

    /**
     * Accesor: Obtener el estado del recordatorio
     */
    public function getEstadoAttribute(): string
    {
        if ($this->completado) {
            return 'Completado';
        }

        if ($this->esta_vencido) {
            return 'Vencido';
        }

        return 'Pendiente';
    }

    /**
     * Accesor: Obtener la clase CSS para el estado
     */
    public function getClaseEstadoAttribute(): string
    {
        return match(true) {
            $this->completado => 'bg-green-100 text-green-800',
            $this->esta_vencido => 'bg-red-100 text-red-800',
            default => 'bg-yellow-100 text-yellow-800'
        };
    }

    /**
     * Accesor: Formatear fecha de vencimiento para mostrar
     */
    public function getFechaVencimientoFormateadaAttribute(): string
    {
        return $this->fecha_vencimiento->format('d/m/Y H:i');
    }

    /**
     * Accesor: Tiempo restante para el vencimiento
     */
    public function getTiempoRestanteAttribute(): string
    {
        if ($this->completado) {
            return 'Completado';
        }

        $now = now();
        $diff = $this->fecha_vencimiento->diff($now);

        if ($this->esta_vencido) {
            return 'Vencido hace ' . $this->formatearDiferencia($diff);
        }

        return 'En ' . $this->formatearDiferencia($diff);
    }

    /**
     * Helper: Formatear diferencia de tiempo
     */
    private function formatearDiferencia($diff): string
    {
        if ($diff->days > 0) {
            return $diff->days . ' día' . ($diff->days > 1 ? 's' : '');
        }

        if ($diff->h > 0) {
            return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '');
        }

        if ($diff->i > 0) {
            return $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '');
        }

        return 'menos de 1 minuto';
    }

    /**
     * Scope: Recordatorios vencidos
     */
    public function scopeVencidos($query)
    {
        return $query->where('fecha_vencimiento', '<', now())
                    ->where('completado', false);
    }

    /**
     * Scope: Recordatorios pendientes (no vencidos, no completados)
     */
    public function scopePendientes($query)
    {
        return $query->where('fecha_vencimiento', '>=', now())
                    ->where('completado', false);
    }

    /**
     * Scope: Recordatorios completados
     */
    public function scopeCompletados($query)
    {
        return $query->where('completado', true);
    }

    /**
     * Scope: Recordatorios para hoy
     */
    public function scopeParaHoy($query)
    {
        return $query->whereDate('fecha_vencimiento', today())
                    ->where('completado', false);
    }

    /**
     * Scope: Recordatorios para esta semana
     */
    public function scopeParaEstaSemana($query)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        return $query->whereBetween('fecha_vencimiento', [$startOfWeek, $endOfWeek])
                    ->where('completado', false);
    }

    /**
     * Marcar recordatorio como completado
     */
    public function marcarComoCompletado(): bool
    {
        return $this->update(['completado' => true]);
    }

    /**
     * Marcar recordatorio como pendiente
     */
    public function marcarComoPendiente(): bool
    {
        return $this->update(['completado' => false]);
    }

    /**
     * Verificar si el recordatorio es urgente (vence en menos de 24 horas)
     */
    public function getEsUrgenteAttribute(): bool
    {
        return !$this->completado && 
               $this->fecha_vencimiento->diffInHours(now()) <= 24 &&
               !$this->esta_vencido;
    }

    /**
     * Obtener el ícono según el estado
     */
    public function getIconoEstadoAttribute(): string
    {
        return match(true) {
            $this->completado => '✅',
            $this->esta_vencido => '⏰',
            $this->es_urgente => '⚠️',
            default => '📅'
        };
    }
}