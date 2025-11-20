@extends('layouts.app')

@section('title', 'Gestión de Actividades')

@section('hero-title', 'Gestión de Actividades')
@section('hero-subtitle', 'Organiza y gestiona tus actividades')

@section('content')
<div class="container">
    <!-- Mensajes -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header + Botón Nueva Actividad -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h2 mb-1 fw-bold text-dark">Mis Actividades</h1>
            <p class="text-muted mb-0">Organiza todo lo que tienes pendiente</p>
        </div>
        <a href="{{ route('actividades.create') }}" class="btn btn-primary btn-lg shadow-sm">
            <i class="fas fa-plus me-2"></i> Nueva Actividad
        </a>
    </div>

    <!-- FILTROS CON CONTADORES REALES Y ESTILO MODERNO -->
    <div class="card shadow-sm mb-5 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap gap-3">
                <a href="{{ route('actividades.index') }}"
                   class="btn btn-lg px-5 {{ $filter === 'todas' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-list me-2"></i> Todas <span class="badge bg-light text-dark ms-2">{{ $totalActividades }}</span>
                </a>

                <a href="{{ route('actividades.index', ['filter' => 'pendientes']) }}"
                   class="btn btn-lg px-5 {{ $filter === 'pendientes' ? 'btn-warning text-white' : 'btn-outline-warning' }}">
                    <i class="fas fa-clock me-2"></i> Pendientes <span class="badge {{ $filter === 'pendientes' ? 'bg-white text-warning' : 'bg-light text-dark' }} ms-2">{{ $pendientesCount }}</span>
                </a>

                <a href="{{ route('actividades.index', ['filter' => 'completadas']) }}"
                   class="btn btn-lg px-5 {{ $filter === 'completadas' ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="fas fa-check-circle me-2"></i> Completadas <span class="badge {{ $filter === 'completadas' ? 'bg-white text-success' : 'bg-light text-dark' }} ms-2">{{ $completadasCount }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Tabla de Actividades -->
    <div class="card shadow border-0">
        <div class="card-body p-0">
            @if($actividades->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Título</th>
                                <th>Nota</th>
                                <th>Prioridad</th>
                                <th>Fecha Límite</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($actividades as $actividad)
                                <tr class="{{ $actividad->completada ? 'table-success' : '' }}">
                                    <td class="ps-4">
                                        <strong>{{ $actividad->titulo }}</strong>
                                        @if($actividad->descripcion)
                                            <br><small class="text-muted">{{ Str::limit($actividad->descripcion, 60) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($actividad->nota)
                                            <a href="{{ route('notas.show', $actividad->nota) }}" class="text-decoration-none text-primary">
                                                <i class="fas fa-sticky-note me-1"></i> {{ $actividad->nota->titulo }}
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($actividad->prioridad)
                                            @case(3)
                                                <span class="badge bg-danger">Alta</span>
                                                @break
                                            @case(2)
                                                <span class="badge bg-warning">Media</span>
                                                @break
                                            @default
                                                <span class="badge bg-info">Baja</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($actividad->fecha_limite)
                                            <span class="{{ $actividad->fecha_limite->isPast() && !$actividad->completada ? 'text-danger fw-bold' : '' }}">
                                                {{ $actividad->fecha_limite->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sin fecha</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($actividad->completada)
                                            <span class="badge bg-success fs-6">✓ Completada</span>
                                        @else
                                            <span class="badge bg-warning text-dark fs-6">⏳ Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            @if($actividad->completada)
                                                <form action="{{ route('actividades.pendiente', $actividad) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Marcar como pendiente">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('actividades.completar', $actividad) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Marcar como completada">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('actividades.edit', $actividad) }}" class="btn btn-sm btn-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('actividades.destroy', $actividad) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"
                                                        onclick="return confirm('¿Seguro que quieres eliminar esta actividad?')">
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
                <div class="card-footer bg-transparent border-0">
                    {{ $actividades->onEachSide(1)->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-5x text-muted mb-4"></i>
                    <h3 class="text-muted">No hay actividades {{ $filter === 'pendientes' ? 'pendientes' : ($filter === 'completadas' ? 'completadas' : '') }}</h3>
                    <p class="text-muted lead">
                        @if($filter === 'completadas')
                            ¡Excelente trabajo! No tienes actividades completadas que mostrar.
                        @elseif($filter === 'pendientes')
                            ¡Genial! No tienes nada pendiente.
                        @else
                            Empieza organizando tu día creando tu primera actividad.
                        @endif
                    </p>
                    <a href="{{ route('actividades.create') }}" class="btn btn-primary btn-lg mt-3">
                        <i class="fas fa-plus me-2"></i> Crear Nueva Actividad
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Auto-ocultar alertas
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            bootstrap.Alert.getOrCreateInstance(alert).close();
        });
    }, 5000);
</script>
@endsection