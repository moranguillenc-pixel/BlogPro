@extends('layouts.app')

@section('title', 'Crear Actividad')

@section('hero-title', 'Crear Nueva Actividad')
@section('hero-subtitle', 'Agrega una nueva actividad a tu nota')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Nueva Actividad
                </h5>
            </div>
            <div class="card-body">
                <!-- Mostrar errores generales -->
                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>Por favor corrige los siguientes errores:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('actividades.store') }}" method="POST" id="form-actividad">
                    @csrf

                    <!-- Nota -->
                    <div class="mb-3">
                        <label for="nota_id" class="form-label">Nota *</label>
                        <select name="nota_id" id="nota_id" required 
                                class="form-select @error('nota_id') is-invalid @enderror">
                            <option value="">Selecciona una nota</option>
                            @foreach($notas as $nota)
                                <option value="{{ $nota->id }}" {{ old('nota_id') == $nota->id ? 'selected' : '' }}>
                                    {{ $nota->titulo }}
                                </option>
                            @endforeach
                        </select>
                        @error('nota_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Título -->
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título *</label>
                        <input type="text" name="titulo" id="titulo" required value="{{ old('titulo') }}" 
                               class="form-control @error('titulo') is-invalid @enderror" 
                               placeholder="Ingresa el título de la actividad">
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion" id="descripcion" rows="4" 
                                  class="form-control @error('descripcion') is-invalid @enderror" 
                                  placeholder="Describe la actividad (opcional)">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Fecha Límite y Prioridad -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="fecha_limite" class="form-label">Fecha Límite</label>
                            <input type="date" name="fecha_limite" id="fecha_limite" value="{{ old('fecha_limite') }}"
                                   class="form-control @error('fecha_limite') is-invalid @enderror"
                                   min="{{ date('Y-m-d') }}">
                            @error('fecha_limite')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="prioridad" class="form-label">Prioridad *</label>
                            <select name="prioridad" id="prioridad" required 
                                    class="form-select @error('prioridad') is-invalid @enderror">
                                <option value="">Selecciona prioridad</option>
                                <option value="1" {{ old('prioridad') == '1' ? 'selected' : '' }}>Baja</option>
                                <option value="2" {{ old('prioridad') == '2' ? 'selected' : '' }}>Media</option>
                                <option value="3" {{ old('prioridad') == '3' ? 'selected' : '' }}>Alta</option>
                            </select>
                            @error('prioridad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('actividades.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Regresar Atrás
                        </a>
                        <button type="submit" class="btn btn-success" id="btn-submit">
                            <i class="fas fa-save me-2"></i>Crear Actividad
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script para mostrar notificación después de crear -->
@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Crear y mostrar notificación
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>
        <strong>¡Éxito!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <div class="mt-2">
            <a href="{{ route('actividades.index') }}" class="btn btn-sm btn-outline-success">
                <i class="fas fa-list me-1"></i>Ver todas las actividades
            </a>
        </div>
    `;
    document.body.appendChild(alertDiv);

    // Auto-eliminar después de 8 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 8000);
});
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-actividad');
    const submitBtn = document.getElementById('btn-submit');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('=== INICIANDO ENVÍO DEL FORMULARIO ===');
            
            // Obtener valores
            const notaId = document.getElementById('nota_id').value;
            const titulo = document.getElementById('titulo').value;
            const prioridad = document.getElementById('prioridad').value;
            
            console.log('Datos del formulario:');
            console.log('- Nota ID:', notaId);
            console.log('- Título:', titulo);
            console.log('- Prioridad:', prioridad);
            console.log('- Descripción:', document.getElementById('descripcion').value);
            console.log('- Fecha Límite:', document.getElementById('fecha_limite').value);
            
            // Validación frontend
            let isValid = true;
            let errorMessage = '';
            
            if (!notaId) {
                isValid = false;
                errorMessage = 'Debes seleccionar una nota';
            } else if (!titulo.trim()) {
                isValid = false;
                errorMessage = 'El título es requerido';
            } else if (!prioridad) {
                isValid = false;
                errorMessage = 'Debes seleccionar una prioridad';
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Error: ' + errorMessage);
                console.log('❌ Validación fallida:', errorMessage);
                return false;
            }
            
            console.log('✅ Validación exitosa, enviando formulario...');
            
            // Mostrar estado de loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creando...';
            
            // El formulario se envía normalmente
            return true;
        });
    }
    
    // Debug: verificar que el formulario esté listo
    console.log('Formulario cargado correctamente');
    console.log('Número de notas disponibles:', document.getElementById('nota_id').options.length - 1);
});
</script>

<style>
.card {
    border: none;
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    border-radius: 8px;
    padding: 10px 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
}

.btn-outline-secondary {
    border-radius: 8px;
    padding: 10px 20px;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    padding: 12px 15px;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.alert {
    min-width: 450px;
    text-align: center;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.border-top {
    border-color: #e9ecef !important;
}

/* Estilos para el estado de loading */
.btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
</style>
@endsection