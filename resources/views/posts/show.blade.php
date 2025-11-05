@extends('layouts.app')

@section('title', $post->title)
@section('hero-title', 'Detalles de Publicación')
@section('hero-subtitle', 'Comparte y conecta con la comunidad')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Botón Volver Mejorado -->
            <div class="mb-4">
                <a href="{{ route('posts.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Publicaciones
                </a>
            </div>

            <!-- Tarjeta de Publicación Principal -->
            <div class="card post-card shadow-lg mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($post->user->name) }}&background=667eea&color=fff" 
                                 alt="Avatar" class="rounded-circle me-3" width="50">
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $post->user->name }}</h6>
                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i>{{ $post->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                        @if($post->user_id == auth()->id())
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('posts.edit', $post) }}">
                                        <i class="fas fa-edit me-2"></i>Editar
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger" 
                                                onclick="return confirm('¿Estás seguro de eliminar esta publicación?')">
                                            <i class="fas fa-trash me-2"></i>Eliminar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <!-- Contenido de la Publicación -->
                    <h4 class="card-title fw-bold text-dark mb-3">{{ $post->title }}</h4>
                    <div class="post-content">
                        <p class="card-text text-dark fs-6 lh-base">{{ $post->content }}</p>
                    </div>
                </div>

                <!-- Estadísticas y Acciones -->
                <div class="card-footer bg-light">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center text-muted">
                                <button onclick="toggleLike('post', {{ $post->id }})" 
                                        class="btn btn-sm btn-outline-danger me-2 like-btn {{ $post->likedBy(auth()->user()) ? 'active' : '' }}"
                                        data-bs-toggle="tooltip" title="Me gusta">
                                    <i class="{{ $post->likedBy(auth()->user()) ? 'fas' : 'far' }} fa-heart me-1"></i>
                                    <span id="like-count-post-{{ $post->id }}">{{ $post->likes->count() }}</span>
                                </button>
                                <span class="text-muted">
                                    <i class="far fa-comment me-1"></i>{{ $post->comments->count() }} comentarios
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">
                                <i class="far fa-eye me-1"></i> 245 vistas
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de Comentario Mejorado -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="card-title fw-bold mb-3">Deja tu comentario</h6>
                    <form action="{{ route('comments.store', $post) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea name="content" class="form-control" rows="3" 
                                      placeholder="Escribe tu comentario aquí..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Máximo 500 caracteres</small>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Publicar Comentario
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sección de Comentarios -->
            <div id="comments" class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="far fa-comments me-2"></i>Comentarios
                        <span class="badge bg-primary ms-2">{{ $post->comments->count() }}</span>
                    </h5>
                    <small class="text-muted">Ordenados por más recientes</small>
                </div>

                @forelse($post->comments as $comment)
                <div class="card comment-card mb-3 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex">
                            <!-- Avatar del Usuario -->
                            <div class="flex-shrink-0 me-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($comment->user->name) }}&background=3498db&color=fff" 
                                     alt="Avatar" class="rounded-circle" width="45">
                            </div>
                            
                            <!-- Contenido del Comentario -->
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $comment->user->name }}</h6>
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>{{ $comment->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    
                                    @if($comment->user_id == auth()->id())
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary border-0 dropdown-toggle" 
                                                type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('comments.edit', $comment) }}">
                                                    <i class="fas fa-edit me-2"></i>Editar
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('comments.destroy', $comment) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" 
                                                            onclick="return confirm('¿Eliminar este comentario?')">
                                                        <i class="fas fa-trash me-2"></i>Eliminar
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                                
                                <!-- Texto del Comentario -->
                                <p class="mb-2 text-dark">{{ $comment->content }}</p>
                                
                                <!-- Acciones del Comentario -->
                                <div class="d-flex align-items-center">
                                    <button onclick="toggleLike('comment', {{ $comment->id }})" 
                                            class="btn btn-sm btn-outline-danger me-3 like-btn-sm {{ $comment->likedBy(auth()->user()) ? 'active' : '' }}"
                                            data-bs-toggle="tooltip" title="Me gusta">
                                        <i class="{{ $comment->likedBy(auth()->user()) ? 'fas' : 'far' }} fa-heart me-1"></i>
                                        <span id="like-count-comment-{{ $comment->id }}">{{ $comment->likes->count() }}</span>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary border-0" 
                                            data-bs-toggle="tooltip" title="Responder">
                                        <i class="fas fa-reply me-1"></i>Responder
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="far fa-comments fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay comentarios aún</h5>
                    <p class="text-muted">Sé el primero en comentar esta publicación</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para Likes Mejorado -->
@section('scripts')
<script>
function toggleLike(type, id) {
    fetch("{{ route('likes.toggle') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ type: type, id: id })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        // Actualizar contador de likes
        const countEl = document.getElementById(`like-count-${type}-${id}`);
        if (countEl) {
            countEl.textContent = data.count;
        }
        
        // Actualizar botón y icono
        const button = event.target.closest('button');
        if (button) {
            const icon = button.querySelector('i');
            if (data.liked) {
                button.classList.add('active');
                icon.classList.replace('far', 'fas');
                button.classList.replace('btn-outline-danger', 'btn-danger');
                button.classList.add('text-white');
            } else {
                button.classList.remove('active');
                icon.classList.replace('fas', 'far');
                button.classList.replace('btn-danger', 'btn-outline-danger');
                button.classList.remove('text-white');
            }
        }
        
        // Mostrar notificación de éxito
        showNotification(data.liked ? '❤️ Te gusta esta publicación' : '💔 Ya no te gusta esta publicación');
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('❌ Error al procesar tu like', 'error');
    });
}

function showNotification(message, type = 'success') {
    // Crear notificación toast
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto-remover después de 3 segundos
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

// Inicializar tooltips adicionales
document.addEventListener('DOMContentLoaded', function() {
    // Tooltips para botones de like
    const likeButtons = document.querySelectorAll('.like-btn, .like-btn-sm');
    likeButtons.forEach(btn => {
        new bootstrap.Tooltip(btn);
    });
    
    // Tooltips para botones de acción
    const actionButtons = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    actionButtons.forEach(btn => {
        new bootstrap.Tooltip(btn);
    });
});
</script>

<style>
.like-btn.active,
.like-btn-sm.active {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: white !important;
}

.comment-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.comment-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.post-content {
    line-height: 1.7;
    font-size: 1.1rem;
}

.btn-like {
    transition: all 0.3s ease;
}

.btn-like:hover {
    transform: scale(1.05);
}

.dropdown-toggle::after {
    display: none;
}
</style>
@endsection
@endsection