@extends('layouts.app')

@section('title', 'Editar Nota: ' . ($nota->titulo ?? ''))

@section('hero-title', 'Editar Nota')
@section('hero-subtitle', 'Modifica los detalles de tu nota')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Editar Nota: {{ $nota->titulo ?? '' }}
                    </h4>
                </div>
                <div class="card-body">
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

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Por favor corrige los siguientes errores:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('notas.update', $nota) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="titulo" class="form-label">
                                <strong>Título de la Nota</strong>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('titulo') is-invalid @enderror" 
                                   id="titulo" 
                                   name="titulo" 
                                   value="{{ old('titulo', $nota->titulo ?? '') }}" 
                                   placeholder="Ingresa un título descriptivo..."
                                   required
                                   maxlength="255">
                            @error('titulo')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                Máximo 255 caracteres. Sé específico con el título.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="contenido" class="form-label">
                                <strong>Contenido</strong>
                                <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('contenido') is-invalid @enderror" 
                                      id="contenido" 
                                      name="contenido" 
                                      rows="8" 
                                      placeholder="Describe el contenido de tu nota..."
                                      required>{{ old('contenido', $nota->contenido ?? '') }}</textarea>
                            @error('contenido')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                Describe detalladamente el contenido de tu nota.
                            </div>
                        </div>

                        <!-- Información de la nota -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-info-circle me-2 text-primary"></i>Información de la Nota
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong>Creada:</strong> {{ $nota->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong>Última actualización:</strong> {{ $nota->updated_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BOTONES MEJORADOS -->
                        <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                            <div>
                                <a href="{{ route('notas.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                                </a>
                                <a href="{{ route('notas.show', $nota) }}" class="btn btn-outline-info ms-2">
                                    <i class="fas fa-eye me-2"></i>Ver Detalles
                                </a>
                            </div>
                            
                            <div class="d-flex gap-3">
                                <button type="reset" class="btn btn-outline-danger px-4 py-2">
                                    <i class="fas fa-undo me-2"></i> Restablecer
                                </button>
                                
                                <!-- BOTÓN PRINCIPAL DESTACADO -->
                                <button type="submit" class="btn btn-success btn-lg px-6 py-3 shadow-sm">
                                    <i class="fas fa-save me-3"></i>
                                    <strong>Actualizar Nota</strong>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Estadísticas rápidas -->
            @php
                $totalActividades = $nota->actividades ? $nota->actividades->count() : 0;
                $actividadesCompletadas = $nota->actividades ? $nota->actividades->where('completada', true)->count() : 0;
                $totalRecordatorios = $nota->recordatorios ? $nota->recordatorios->count() : 0;
            @endphp

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white text-center">
                        <div class="card-body py-3">
                            <h5 class="mb-1">{{ $totalActividades }}</h5>
                            <small>Actividades</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white text-center">
                        <div class="card-body py-3">
                            <h5 class="mb-1">{{ $actividadesCompletadas }}</h5>
                            <small>Completadas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white text-center">
                        <div class="card-body py-3">
                            <h5 class="mb-1">{{ $totalRecordatorios }}</h5>
                            <small>Recordatorios</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tus scripts y estilos originales siguen igual -->
<script>
    // ... (tu script original se mantiene igual)
</script>

<style>
    /* Tus estilos originales */
    .card { border: none; border-radius: 10px; }
    .card-header { border-radius: 10px 10px 0 0 !important; }
    .form-control:focus { border-color: #fd7e14; box-shadow: 0 0 0 0.2rem rgba(253, 126, 20, 0.25); }
    .btn { border-radius: 6px; font-weight: 500; }
    .alert { border-radius: 8px; border: none; }
    .invalid-feedback { font-weight: 500; }
</style>
@endsection