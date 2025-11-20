@extends('layouts.app')

@section('title', 'Editar Publicación: ' . ($post->title ?? 'Publicación'))

@section('hero-title', 'Editar Publicación')
@section('hero-subtitle', 'Actualiza y mejora tu contenido')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Editar Publicación
                        </h4>
                        <a href="{{ route('posts.show', $post) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-eye me-1"></i>Ver Publicación
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Mensajes de éxito y error -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle me-3 fs-5"></i>
                                <div class="flex-grow-1">
                                    <strong>¡Éxito!</strong> {{ session('success') }}
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-3 fs-5"></i>
                                <div class="flex-grow-1">
                                    <strong>Error!</strong> {{ session('error') }}
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-3 fs-5"></i>
                                <div class="flex-grow-1">
                                    <strong>Por favor corrige los siguientes errores:</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('posts.update', $post) }}" method="POST" id="editPostForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Campo Título -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold text-dark">
                                Título de la Publicación
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title"
                                   class="form-control form-control-lg @error('title') is-invalid @enderror" 
                                   value="{{ old('title', $post->title) }}" 
                                   placeholder="Ingresa un título atractivo para tu publicación..."
                                   required
                                   maxlength="255">
                            @error('title')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text text-end">
                                <span id="titleCounter">0</span>/255 caracteres
                            </div>
                        </div>
                        
                        <!-- Campo Contenido -->
                        <div class="mb-4">
                            <label for="content" class="form-label fw-bold text-dark">
                                Contenido
                                <span class="text-danger">*</span>
                            </label>
                            <textarea name="content" 
                                      id="content"
                                      class="form-control @error('content') is-invalid @enderror" 
                                      rows="12" 
                                      placeholder="Escribe el contenido de tu publicación aquí..."
                                      required>{{ old('content', $post->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text text-end">
                                <span id="contentCounter">0</span> caracteres
                            </div>
                        </div>

                        <!-- Información de la Publicación -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-primary mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Información de la Publicación
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong><i class="far fa-calendar me-1"></i>Creada:</strong> 
                                            {{ $post->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong><i class="fas fa-sync-alt me-1"></i>Última actualización:</strong> 
                                            {{ $post->updated_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong><i class="far fa-heart me-1"></i>Likes:</strong> 
                                            {{ $post->likes_count ?? 0 }}
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong><i class="far fa-comment me-1"></i>Comentarios:</strong> 
                                            {{ $post->comments_count ?? 0 }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botones de Acción -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <div>
                                <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                                </a>
                                <a href="{{ route('posts.show', $post) }}" class="btn btn-outline-info ms-2">
                                    <i class="fas fa-eye me-2"></i>Ver Publicación
                                </a>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-outline-danger">
                                    <i class="fas fa-undo me-2"></i>Restablecer
                                </button>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i>Actualizar Publicación
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Consejos de Edición -->
            <div class="card mt-4 border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-lightbulb me-2"></i>Consejos para Mejorar tu Publicación
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <small><i class="fas fa-check text-success me-2"></i>Usa títulos claros y descriptivos</small>
                                </li>
                                <li class="mb-2">
                                    <small><i class="fas fa-check text-success me-2"></i>Divide el contenido en párrafos cortos</small>
                                </li>
                                <li class="mb-2">
                                    <small><i class="fas fa-check text-success me-2"></i>Incluye ejemplos prácticos</small>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <small><i class="fas fa-check text-success me-2"></i>Revisa la ortografía y gramática</small>
                                </li>
                                <li class="mb-2">
                                    <small><i class="fas fa-check text-success me-2"></i>Mantén un tono profesional</small>
                                </li>
                                <li class="mb-2">
                                    <small><i class="fas fa-check text-success me-2"></i>Actualiza información desactualizada</small>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    border-radius: 12px;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn {
    border-radius: 8px;
    font-weight: 500;
}

.alert {
    border-radius: 8px;
    border: none;
}

#content {
    resize: vertical;
    min-height: 300px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-ocultar alertas después de 5 segundos
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Contador de caracteres para el título
    const titleInput = document.getElementById('title');
    const titleCounter = document.getElementById('titleCounter');
    
    function updateTitleCounter() {
        const length = titleInput.value.length;
        titleCounter.textContent = length;
        
        if (length > 250) {
            titleCounter.className = 'text-danger';
        } else if (length > 200) {
            titleCounter.className = 'text-warning';
        } else {
            titleCounter.className = 'text-muted';
        }
    }
    
    titleInput.addEventListener('input', updateTitleCounter);
    updateTitleCounter(); // Inicializar contador

    // Contador de caracteres para el contenido
    const contentInput = document.getElementById('content');
    const contentCounter = document.getElementById('contentCounter');
    
    function updateContentCounter() {
        const length = contentInput.value.length;
        contentCounter.textContent = length;
        
        if (length > 1000) {
            contentCounter.className = 'text-success';
        } else {
            contentCounter.className = 'text-muted';
        }
    }
    
    contentInput.addEventListener('input', updateContentCounter);
    updateContentCounter(); // Inicializar contador

    // Confirmación antes de restablecer
    document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
        if (!confirm('¿Estás seguro de que deseas restablecer todos los cambios? Se perderán las modificaciones no guardadas.')) {
            e.preventDefault();
        }
    });

    // Validación del formulario
    document.getElementById('editPostForm').addEventListener('submit', function(e) {
        const title = titleInput.value.trim();
        const content = contentInput.value.trim();
        
        if (!title || !content) {
            e.preventDefault();
            alert('Por favor completa todos los campos requeridos.');
            return;
        }
        
        if (title.length > 255) {
            e.preventDefault();
            alert('El título no puede tener más de 255 caracteres.');
            return;
        }
        
        console.log('Formulario de edición enviado correctamente');
    });

    // Auto-guardado local (opcional)
    let autoSaveTimer;
    contentInput.addEventListener('input', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(function() {
            // Guardar en localStorage
            const postData = {
                title: titleInput.value,
                content: contentInput.value,
                timestamp: new Date().toISOString()
            };
            localStorage.setItem('postDraft_' + {{ $post->id }}, JSON.stringify(postData));
            console.log('Borrador guardado localmente');
        }, 2000);
    });

    // Cargar borrador guardado (si existe)
    const savedDraft = localStorage.getItem('postDraft_' + {{ $post->id }});
    if (savedDraft) {
        const draft = JSON.parse(savedDraft);
        if (confirm('¿Deseas recuperar el borrador guardado?')) {
            titleInput.value = draft.title;
            contentInput.value = draft.content;
            updateTitleCounter();
            updateContentCounter();
        }
        // Limpiar el borrador después de ofrecerlo
        localStorage.removeItem('postDraft_' + {{ $post->id }});
    }
});
</script>
@endpush