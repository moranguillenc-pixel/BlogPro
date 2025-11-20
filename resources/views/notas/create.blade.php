@extends('layouts.app')

@section('title', 'Crear Nueva Nota')

@section('hero-title', 'Crear Nueva Nota')
@section('hero-subtitle', 'Agrega una nueva nota al sistema')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Nueva Nota
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

                    <form action="{{ route('notas.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="titulo" class="form-label">
                                <strong>Título de la Nota</strong>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('titulo') is-invalid @enderror" 
                                   id="titulo" 
                                   name="titulo" 
                                   value="{{ old('titulo') }}" 
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
                                      required>{{ old('contenido') }}</textarea>
                            @error('contenido')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                Describe detalladamente el contenido de tu nota. Puedes incluir actividades y recordatorios después de crearla.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('notas.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                            </a>
                            
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-outline-danger">
                                    <i class="fas fa-undo me-2"></i>Limpiar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Guardar Nota
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Información Útil
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">
                                <i class="fas fa-lightbulb me-2"></i>Consejos
                            </h6>
                            <ul class="list-unstyled">
                                <li><small>• Usa títulos claros y descriptivos</small></li>
                                <li><small>• Organiza el contenido en secciones</small></li>
                                <li><small>• Sé específico en la descripción</small></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success">
                                <i class="fas fa-plus me-2"></i>Próximos Pasos
                            </h6>
                            <ul class="list-unstyled">
                                <li><small>• Agregar actividades después de crear</small></li>
                                <li><small>• Configurar recordatorios</small></li>
                                <li><small>• Organizar por prioridad</small></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-ocultar alertas después de 5 segundos
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Contador de caracteres para el título
    const tituloInput = document.getElementById('titulo');
    const tituloCounter = document.createElement('small');
    tituloCounter.className = 'form-text text-end d-block mt-1';
    tituloCounter.textContent = '0/255 caracteres';
    tituloInput.parentNode.appendChild(tituloCounter);

    tituloInput.addEventListener('input', function() {
        const length = this.value.length;
        tituloCounter.textContent = `${length}/255 caracteres`;
        
        if (length > 250) {
            tituloCounter.className = 'form-text text-end d-block mt-1 text-danger';
        } else if (length > 200) {
            tituloCounter.className = 'form-text text-end d-block mt-1 text-warning';
        } else {
            tituloCounter.className = 'form-text text-end d-block mt-1 text-muted';
        }
    });

    // Contador de caracteres para el contenido
    const contenidoInput = document.getElementById('contenido');
    const contenidoCounter = document.createElement('small');
    contenidoCounter.className = 'form-text text-end d-block mt-1';
    contenidoCounter.textContent = '0 caracteres';
    contenidoInput.parentNode.appendChild(contenidoCounter);

    contenidoInput.addEventListener('input', function() {
        const length = this.value.length;
        contenidoCounter.textContent = `${length} caracteres`;
        
        if (length > 1000) {
            contenidoCounter.className = 'form-text text-end d-block mt-1 text-success';
        } else {
            contenidoCounter.className = 'form-text text-end d-block mt-1 text-muted';
        }
    });

    console.log('Formulario de creación de notas cargado correctamente');
});
</script>

<style>
.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.btn {
    border-radius: 6px;
    font-weight: 500;
}

.alert {
    border-radius: 8px;
    border: none;
}

.invalid-feedback {
    font-weight: 500;
}
</style>
@endsection