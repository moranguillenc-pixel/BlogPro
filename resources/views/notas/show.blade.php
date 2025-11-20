@extends('layouts.app')

@section('title', $nota->titulo)

@section('content')
<div class="container">
    <!-- Mensajes -->
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

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-0">{{ $nota->titulo }}</h1>
            <small class="text-muted">Creada: {{ $nota->created_at->format('d/m/Y H:i') }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('notas.edit', $nota) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-edit me-2"></i>Editar Nota
            </a>
            <a href="{{ route('notas.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <!-- Contenido -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Contenido</h5>
            <p class="card-text">{{ $nota->contenido }}</p>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4>{{ $estadisticas['total_actividades'] }}</h4>
                    <p class="mb-0">Total Actividades</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ $estadisticas['actividades_completadas'] }}</h4>
                    <p class="mb-0">Completadas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>{{ $estadisticas['total_recordatorios'] }}</h4>
                    <p class="mb-0">Recordatorios</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="d-grid gap-2 mb-4">
        @if($estadisticas['total_actividades'] > 0)
            @if($estadisticas['actividades_completadas'] < $estadisticas['total_actividades'])
                <form action="{{ route('notas.completar', $nota) }}" method="POST" class="d-grid">
                    @csrf
                    <button type="submit" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-check me-2"></i>Completar Todo
                    </button>
                </form>
            @else
                <form action="{{ route('notas.pendiente', $nota) }}" method="POST" class="d-grid">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-undo me-2"></i>Marcar como Pendiente
                    </button>
                </form>
            @endif
        @endif
    </div>

    <!-- Actividades -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-tasks me-2"></i>Actividades
                <span class="badge bg-secondary">{{ $estadisticas['total_actividades'] }}</span>
            </h5>
            <a href="{{ route('actividades.create', ['nota_id' => $nota->id]) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-2"></i>Nueva Actividad
            </a>
        </div>
        <div class="card-body">
            @if($nota->actividades->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Descripción</th>
                                <th>Prioridad</th>
                                <th>Fecha Límite</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($nota->actividades as $actividad)
                                <tr class="{{ $actividad->completada ? 'table-success' : '' }}">
                                    <td>{{ $actividad->titulo }}</td>
                                    <td>{{ Str::limit($actividad->descripcion, 50) }}</td>
                                    <td>
                                        @switch($actividad->prioridad)
                                            @case(1) <span class="badge bg-danger">Alta</span> @break
                                            @case(2) <span class="badge bg-warning">Media</span> @break
                                            @case(3) <span class="badge bg-success">Baja</span> @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($actividad->fecha_limite)
                                            {{ $actividad->fecha_limite->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">Sin fecha</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($actividad->completada)
                                            <span class="badge bg-success">Completada</span>
                                        @else
                                            <span class="badge bg-secondary">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if($actividad->completada)
                                                <form action="{{ route('actividades.pendiente', $actividad) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-warning" title="Marcar como pendiente">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('actividades.completar', $actividad) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="Marcar como completada">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('actividades.edit', $actividad) }}" class="btn btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay actividades para esta nota.</p>
                    <a href="{{ route('actividades.create', ['nota_id' => $nota->id]) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Crear Primera Actividad
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Recordatorios -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-bell me-2"></i>Recordatorios
                <span class="badge bg-secondary">{{ $estadisticas['total_recordatorios'] }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($nota->recordatorios->count() > 0)
                <div class="list-group">
                    @foreach($nota->recordatorios as $recordatorio)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $recordatorio->titulo }}</h6>
                                    <p class="mb-1">{{ $recordatorio->descripcion }}</p>
                                    <small class="text-muted">
                                        Fecha: {{ $recordatorio->fecha_vencimiento->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                                <span class="badge bg-warning">Recordatorio</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-bell fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay recordatorios para esta nota.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection