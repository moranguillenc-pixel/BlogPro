@extends('layouts.app')

@section('title', 'Sistema de Notas')

@section('hero-title', 'Sistema de Notas')
@section('hero-subtitle', 'Gestiona tus notas, recordatorios y actividades')

@section('content')
<div class="container">
    <!-- Mensajes de éxito y error -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header con botón crear -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Mis Notas</h1>
        <a href="{{ route('notas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nueva Nota
        </a>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4>{{ $totalNotas ?? 0 }}</h4>
                    <p class="mb-0">Total Notas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ $totalActividades ?? 0 }}</h4>
                    <p class="mb-0">Total Actividades</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>{{ $actividadesCompletadas ?? 0 }}</h4>
                    <p class="mb-0">Completadas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>{{ $totalRecordatorios ?? 0 }}</h4>
                    <p class="mb-0">Recordatorios</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Búsqueda -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('notas.search') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-8">
                    <input type="text" name="q" class="form-control" placeholder="Buscar por título o contenido..." value="{{ request('q') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search me-2"></i>Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Notas -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if($notas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Título</th>
                                <th>Contenido</th>
                                <th>Actividades</th>
                                <th>Recordatorios</th>
                                <th>Fecha Creación</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notas as $nota)
                                @php
                                    // Usar withCount para mejor performance
                                    $totalActividadesNota = $nota->actividades_count ?? $nota->actividades->count();
                                    $actividadesCompletadasNota = $nota->actividades->where('completada', true)->count();
                                    $progresoActividades = $totalActividadesNota > 0 ? 
                                        round(($actividadesCompletadasNota / $totalActividadesNota) * 100) : 0;
                                    $estaCompletada = $totalActividadesNota > 0 && $actividadesCompletadasNota === $totalActividadesNota;
                                    $totalRecordatoriosNota = $nota->recordatorios_count ?? $nota->recordatorios->count();
                                    
                                    // Obtener próximo recordatorio
                                    $proximoRecordatorio = $nota->recordatorios->sortBy('fecha_vencimiento')->first();
                                @endphp
                                <tr class="{{ $estaCompletada ? 'table-success' : '' }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <strong class="me-2">{{ $nota->titulo }}</strong>
                                            @if($estaCompletada)
                                                <span class="badge bg-success" title="Todas las actividades completadas">
                                                    <i class="fas fa-check me-1"></i>Completada
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted small">
                                            {{ Str::limit($nota->contenido, 60) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-2">
                                            @if($totalActividadesNota > 0)
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge bg-primary">
                                                        {{ $actividadesCompletadasNota }}/{{ $totalActividadesNota }}
                                                    </span>
                                                    <small class="text-muted">{{ $progresoActividades }}%</small>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar {{ $estaCompletada ? 'bg-success' : 'bg-info' }}" 
                                                         style="width: {{ $progresoActividades }}%"
                                                         title="{{ $progresoActividades }}% completado">
                                                    </div>
                                                </div>
                                            @else
                                                <span class="badge bg-secondary">Sin actividades</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($totalRecordatoriosNota > 0)
                                            <div class="d-flex flex-column gap-1">
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-bell me-1"></i>{{ $totalRecordatoriosNota }}
                                                </span>
                                                @if($proximoRecordatorio && $proximoRecordatorio->fecha_vencimiento)
                                                    @php
                                                        $hoy = now();
                                                        $fechaRecordatorio = $proximoRecordatorio->fecha_vencimiento;
                                                        $diasDiferencia = $hoy->diffInDays($fechaRecordatorio, false);
                                                        
                                                        $claseBadge = 'bg-secondary';
                                                        if ($diasDiferencia < 0) {
                                                            $claseBadge = 'bg-danger';
                                                        } elseif ($diasDiferencia <= 1) {
                                                            $claseBadge = 'bg-danger';
                                                        } elseif ($diasDiferencia <= 3) {
                                                            $claseBadge = 'bg-warning text-dark';
                                                        } elseif ($diasDiferencia <= 7) {
                                                            $claseBadge = 'bg-info';
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $claseBadge }} small">
                                                        {{ $fechaRecordatorio->format('d/m/Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">Sin recordatorios</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted" title="{{ $nota->created_at->format('d/m/Y H:i:s') }}">
                                            {{ $nota->created_at->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <!-- Botón Ver Actividades -->
                                            <a href="{{ route('actividades.index', ['nota_id' => $nota->id]) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Gestionar actividades">
                                                <i class="fas fa-tasks"></i>
                                            </a>

                                            <!-- Botón Ver Detalles -->
                                            <a href="{{ route('notas.show', $nota) }}" 
                                               class="btn btn-sm btn-outline-secondary" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- Botón Editar -->
                                            <a href="{{ route('notas.edit', $nota) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Editar nota">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- Botón Completar/Pendiente -->
                                            @if($totalActividadesNota > 0)
                                                @if($estaCompletada)
                                                    <form action="{{ route('notas.pendiente', $nota) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-warning" 
                                                                title="Marcar como pendiente">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('notas.completar', $nota) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-success" 
                                                                title="Marcar como completada">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif

                                            <!-- Botón Eliminar -->
                                            <form action="{{ route('notas.destroy', $nota) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Eliminar nota">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($notas->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted small">
                            Mostrando {{ $notas->firstItem() }} - {{ $notas->lastItem() }} de {{ $notas->total() }} notas
                        </div>
                        <div>
                            {{ $notas->links() }}
                        </div>
                    </div>
                @endif

            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-sticky-note fa-4x text-muted"></i>
                    </div>
                    <h4 class="text-muted mb-3">No hay notas creadas</h4>
                    <p class="text-muted mb-4">
                        Comienza organizando tus ideas, tareas y recordatorios creando tu primera nota.
                    </p>
                    <a href="{{ route('notas.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>
                        Crear Primera Nota
                    </a>
                    
                    @if(request()->has('q'))
                        <div class="mt-3">
                            <a href="{{ route('notas.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Limpiar búsqueda
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="deleteMessage"></p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Advertencia:</strong> Esta acción no se puede deshacer.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar modal de eliminación
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const deleteForm = document.getElementById('deleteForm');
    const deleteMessage = document.getElementById('deleteMessage');

    document.querySelectorAll('form[action*="destroy"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const notaRow = this.closest('tr');
            const titulo = notaRow.querySelector('strong').textContent.trim();
            
            // Obtener contadores
            const actividadesText = notaRow.querySelector('.badge.bg-primary')?.textContent || '0/0';
            const [completadas, total] = actividadesText.split('/').map(num => num.trim());
            const recordatorios = notaRow.querySelector('.badge.bg-warning')?.textContent.match(/\d+/)?.[0] || '0';
            
            // Configurar mensaje
            deleteMessage.innerHTML = `
                ¿Estás seguro de eliminar la nota <strong>"${titulo}"</strong>?<br><br>
                Esta acción eliminará permanentemente:<br>
                • <strong>La nota</strong><br>
                • <strong>${recordatorios} recordatorio(s)</strong><br>
                • <strong>${total} actividad(es)</strong> (${completadas} completadas)
            `;
            
            // Configurar formulario
            deleteForm.action = this.action;
            
            // Mostrar modal
            deleteModal.show();
        });
    });

    // Configurar tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-ocultar alertas después de 5 segundos
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Efectos visuales para filas
    document.querySelectorAll('tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    console.log('Sistema de notas cargado correctamente');
});
</script>

<style>
.progress {
    min-width: 80px;
    background-color: #e9ecef;
    border-radius: 3px;
}

.table td {
    vertical-align: middle;
    padding: 0.75rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 0.375rem;
}

.badge {
    font-size: 0.7rem;
    font-weight: 500;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card {
    border: none;
    border-radius: 0.5rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
}

/* Estilos para la paginación */
.pagination {
    margin-bottom: 0;
}

.page-link {
    border-radius: 0.375rem;
    margin: 0 2px;
    border: 1px solid #dee2e6;
}

.page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

/* Responsive */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.8rem;
    }
    
    .badge {
        font-size: 0.65rem;
    }
}
</style>
@endsection