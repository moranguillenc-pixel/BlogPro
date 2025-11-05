@extends('layouts.app')

@section('title', 'Publicaciones')
@section('hero-title', 'Publicaciones Profesionales')
@section('hero-subtitle', 'Comparte tus ideas y conecta con la comunidad')

@section('content')
<div class="container">
    <!-- Header con Búsqueda y Acciones -->
    <div class="row mb-5">
        <div class="col-md-8">
            <div class="search-box">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 ps-0" placeholder="Buscar publicaciones...">
                    <button class="btn btn-primary px-4">
                        <i class="fas fa-search me-2"></i>Buscar
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-end">
            @auth
            <a href="{{ route('posts.create') }}" class="btn btn-primary btn-lg w-100">
                <i class="fas fa-plus-circle me-2"></i>Nueva Publicación
            </a>
            @else
            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg w-100">
                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
            </a>
            @endauth
        </div>
    </div>

    <!-- Filtros Rápidos -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-primary rounded-pill px-4 active">
                    <i class="fas fa-stream me-2"></i>Todos
                </button>
                <button class="btn btn-outline-primary rounded-pill px-4">
                    <i class="fas fa-fire me-2"></i>Populares
                </button>
                <button class="btn btn-outline-primary rounded-pill px-4">
                    <i class="fas fa-clock me-2"></i>Recientes
                </button>
                <button class="btn btn-outline-primary rounded-pill px-4">
                    <i class="fas fa-users me-2"></i>Siguiendo
                </button>
            </div>
        </div>
    </div>

    <!-- Mensajes de Estado -->
    @if (session('success'))
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

    <!-- Grid de Publicaciones -->
    <div class="row" id="posts-container">
        @forelse ($posts as $post)
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card post-card h-100 border-0 shadow-sm">
                <!-- Header con Información del Usuario -->
                <div class="card-header bg-transparent border-0 pb-0">
                    <div class="d-flex align-items-center">
                        <div class="user-avatar me-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($post->user->name) }}&background=667eea&color=fff&bold=true" 
                                 alt="{{ $post->user->name }}" class="rounded-circle" width="45" height="45">
                        </div>
                        <div class="user-info flex-grow-1">
                            <h6 class="mb-0 fw-bold text-dark">{{ $post->user->name }}</h6>
                            <small class="text-muted">
                                <i class="far fa-clock me-1"></i>{{ $post->created_at->diffForHumans() }}
                            </small>
                        </div>
                        @if ($post->user_id === Auth::id())
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary border-0 dropdown-toggle" 
                                    type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('posts.edit', $post) }}">
                                        <i class="fas fa-edit me-2"></i>Editar
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger" 
                                                onclick="return confirm('¿Eliminar esta publicación?')">
                                            <i class="fas fa-trash me-2"></i>Eliminar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Contenido de la Publicación -->
                <div class="card-body py-3">
                    <!-- Título -->
                    <h5 class="card-title fw-bold text-dark mb-3 line-clamp-2">
                        <a href="{{ route('posts.show', $post) }}" class="text-decoration-none text-dark">
                            {{ $post->title ?: 'Publicación sin título' }}
                        </a>
                    </h5>
                    
                    <!-- Contenido -->
                    <div class="post-content mb-3">
                        <p class="card-text text-muted line-clamp-3 lh-base">
                            {{ Str::limit($post->content, 150) }}
                        </p>
                    </div>

                    <!-- Estadísticas -->
                    <div class="post-stats d-flex gap-3">
                        <div class="stat-item">
                            <i class="far fa-comment me-1 text-primary"></i>
                            <small class="text-muted">{{ $post->comments_count ?? 0 }}</small>
                        </div>
                        <div class="stat-item">
                            <i class="far fa-heart me-1 text-danger"></i>
                            <small class="text-muted">{{ $post->likes_count ?? 0 }}</small>
                        </div>
                        <div class="stat-item">
                            <i class="far fa-eye me-1 text-info"></i>
                            <small class="text-muted">245</small>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="card-footer bg-transparent border-0 pt-0">
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-danger btn-sm flex-fill like-btn">
                            <i class="far fa-heart me-1"></i>Me gusta
                        </button>
                        <a href="{{ route('posts.show', $post) }}" class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="far fa-comment me-1"></i>Comentar
                        </a>
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="far fa-bookmark"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <!-- Estado Vacío -->
        <div class="col-12">
            <div class="text-center py-5 my-5">
                <div class="empty-state-icon mb-4">
                    <i class="fas fa-newspaper fa-4x text-muted opacity-25"></i>
                </div>
                <h3 class="text-muted mb-3">No hay publicaciones aún</h3>
                <p class="text-muted mb-4">Sé el primero en compartir una publicación con la comunidad</p>
                @auth
                <a href="{{ route('posts.create') }}" class="btn btn-primary btn-lg px-4">
                    <i class="fas fa-plus me-2"></i>Crear Primera Publicación
                </a>
                @else
                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-4">
                        <i class="fas fa-user-plus me-2"></i>Registrarse
                    </a>
                </div>
                @endauth
            </div>
        </div>
        @endforelse
    </div>

    <!-- Paginación Simplificada -->
    @if($posts->hasMorePages())
    <div class="row mt-5">
        <div class="col-12 text-center">
            <button id="load-more-btn" class="btn btn-outline-primary px-5" data-next-page="2">
                <i class="fas fa-spinner fa-spin d-none me-2" id="loading-spinner"></i>
                <span id="load-more-text">Cargar más publicaciones</span>
            </button>
            <div class="mt-2">
                <small class="text-muted">
                    Mostrando {{ $posts->count() }} de {{ $posts->total() }} publicaciones
                </small>
            </div>
        </div>
    </div>
    @elseif($posts->total() > 0)
    <div class="row mt-4">
        <div class="col-12 text-center">
            <div class="text-muted py-3">
                <i class="fas fa-check-circle text-success me-2"></i>
                Has visto todas las publicaciones
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.post-card {
    transition: all 0.3s ease;
    border-radius: 16px;
    overflow: hidden;
}

.post-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.15) !important;
}

.user-avatar img {
    border: 3px solid #f8f9fa;
    transition: border-color 0.3s ease;
}

.post-card:hover .user-avatar img {
    border-color: #667eea;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.stat-item {
    display: inline-flex;
    align-items: center;
    padding: 4px 8px;
    border-radius: 6px;
    background: #f8f9fa;
}

.like-btn:hover {
    background-color: #dc3545;
    color: white;
}

.empty-state-icon {
    opacity: 0.5;
}

.search-box .input-group:focus-within {
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    border-radius: 12px;
}

.btn-rounded {
    border-radius: 50px;
}

.card-title a:hover {
    color: #667eea !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Load More functionality
    const loadMoreBtn = document.getElementById('load-more-btn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            const nextPage = this.getAttribute('data-next-page');
            const loadingSpinner = document.getElementById('loading-spinner');
            const loadMoreText = document.getElementById('load-more-text');
            
            if (!nextPage) return;
            
            // Show loading state
            loadingSpinner.classList.remove('d-none');
            loadMoreText.textContent = 'Cargando...';
            this.disabled = true;
            
            // Simulate API call (replace with actual fetch)
            setTimeout(() => {
                // For demo purposes - in real app, you'd fetch next page
                loadingSpinner.classList.add('d-none');
                loadMoreText.textContent = 'No hay más publicaciones';
                this.disabled = true;
                this.classList.add('d-none');
            }, 1500);
        });
    }

    // Like button functionality
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon.classList.contains('far')) {
                icon.classList.replace('far', 'fas');
                this.classList.replace('btn-outline-danger', 'btn-danger');
                this.classList.add('text-white');
            } else {
                icon.classList.replace('fas', 'far');
                this.classList.replace('btn-danger', 'btn-outline-danger');
                this.classList.remove('text-white');
            }
        });
    });
});
</script>
@endsection