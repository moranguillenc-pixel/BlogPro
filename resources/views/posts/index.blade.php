@extends('layouts.app')

@section('title', 'Publicaciones')
@section('hero-title', 'Publicaciones Profesionales')
@section('hero-subtitle', 'Comparte tus ideas y conecta con la comunidad')

@section('content')
<div class="container-fluid">
    <!-- Tabs Navigation -->
    <div class="bg-white rounded shadow-sm">
        <ul class="nav nav-tabs-custom px-4" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-publicaciones" data-bs-toggle="tab" 
                        data-bs-target="#content-publicaciones" type="button" role="tab">
                    <i class="fas fa-newspaper me-2"></i>Publicaciones
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-notas" data-bs-toggle="tab" 
                        data-bs-target="#content-notas" type="button" role="tab">
                    <i class="fas fa-sticky-note me-2"></i>Sistema de Notas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-estadisticas" data-bs-toggle="tab" 
                        data-bs-target="#content-estadisticas" type="button" role="tab">
                    <i class="fas fa-chart-bar me-2"></i>Estadísticas
                </button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- Publicaciones Tab -->
            <div class="tab-pane fade show active" id="content-publicaciones" role="tabpanel">
                <!-- Header con Búsqueda y Acciones -->
                <div class="row mb-4 p-4">
                    <div class="col-md-8">
                        <div class="search-box">
                            <form action="{{ route('posts.search') }}" method="GET" class="d-flex">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" name="q" class="form-control border-start-0 ps-0" 
                                           placeholder="Buscar publicaciones..." value="{{ request('q') }}">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-search me-2"></i>Buscar
                                    </button>
                                </div>
                            </form>
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
                <div class="row mb-4 px-4">
                    <div class="col-12">
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-primary rounded-pill px-4 active filter-btn" data-filter="todos">
                                <i class="fas fa-stream me-2"></i>Todos
                            </button>
                            <button class="btn btn-outline-primary rounded-pill px-4 filter-btn" data-filter="populares">
                                <i class="fas fa-fire me-2"></i>Populares
                            </button>
                            <button class="btn btn-outline-primary rounded-pill px-4 filter-btn" data-filter="recientes">
                                <i class="fas fa-clock me-2"></i>Recientes
                            </button>
                            <button class="btn btn-outline-primary rounded-pill px-4 filter-btn" data-filter="siguiendo">
                                <i class="fas fa-users me-2"></i>Siguiendo
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mensajes de Estado -->
                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-4 mb-4" role="alert">
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
                <div class="row g-4 p-4" id="posts-container">
                    @forelse ($posts as $post)
                    <div class="col-xl-4 col-lg-6">
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
                                        <small class="text-muted">{{ $post->views ?? 0 }}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="card-footer bg-transparent border-0 pt-0">
                                <div class="d-flex gap-2">
                                    <form action="{{ route('likes.toggle') }}" method="POST" class="d-inline flex-fill">
                                        @csrf
                                        <input type="hidden" name="post_id" value="{{ $post->id }}">
                                        <button type="submit" 
                                                class="btn btn-outline-danger btn-sm w-100 like-btn {{ $post->isLikedBy(auth()->user()) ? 'btn-danger text-white' : '' }}">
                                            <i class="{{ $post->isLikedBy(auth()->user()) ? 'fas' : 'far' }} fa-heart me-1"></i>
                                            Me gusta
                                        </button>
                                    </form>
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
                <div class="row mt-4 px-4">
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
                <div class="row mt-4 px-4">
                    <div class="col-12 text-center">
                        <div class="text-muted py-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Has visto todas las publicaciones
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Notas Tab -->
            <div class="tab-pane fade" id="content-notas" role="tabpanel">
                @include('notas.index')
            </div>

            <!-- Estadísticas Tab -->
            <div class="tab-pane fade" id="content-estadisticas" role="tabpanel">
                <div class="row g-4 p-4">
                    <div class="col-md-4">
                        <div class="card stat-card border-0 bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-newspaper fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h4 class="mb-0">{{ $posts->count() }}</h4>
                                        <p class="mb-0">Total Publicaciones</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card border-0 bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-sticky-note fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h4 class="mb-0">{{ \App\Models\Nota::count() }}</h4>
                                        <p class="mb-0">Notas Activas</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card border-0 bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h4 class="mb-0">{{ \App\Models\User::count() }}</h4>
                                        <p class="mb-0">Usuarios Registrados</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Stats -->
                <div class="card mx-4">
                    <div class="card-body">
                        <h5 class="card-title">Actividad Reciente</h5>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Publicaciones creadas hoy</span>
                                <span class="badge bg-primary rounded-pill">{{ $posts->where('created_at', '>=', today())->count() }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Notas pendientes</span>
                                <span class="badge bg-warning rounded-pill">{{ \App\Models\Nota::whereHas('recordatorio', function($q) {
                                    $q->where('completado', false)->where('fecha_vencimiento', '>=', now());
                                })->count() }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Total comentarios</span>
                                <span class="badge bg-success rounded-pill">{{ \App\Models\Comment::count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

.stat-card {
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.nav-tabs-custom .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
    padding: 1rem 1.5rem;
    transition: all 0.3s ease;
}

.nav-tabs-custom .nav-link:hover {
    border: none;
    color: #495057;
}

.nav-tabs-custom .nav-link.active {
    border: none;
    border-bottom: 3px solid #667eea;
    color: #667eea;
    background: transparent;
    font-weight: 600;
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

    // Filter buttons functionality
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('active', 'btn-primary');
                b.classList.add('btn-outline-primary');
            });
            
            // Add active class to clicked button
            this.classList.add('active', 'btn-primary');
            this.classList.remove('btn-outline-primary');
            
            const filter = this.getAttribute('data-filter');
            // Implement filter logic here
            console.log('Filtrando por:', filter);
            
            // You can add AJAX filtering here
            filterPosts(filter);
        });
    });

    function filterPosts(filter) {
        // Implement your filtering logic here
        // This could be an AJAX call to filter posts
        switch(filter) {
            case 'populares':
                // Filter by popularity
                alert('Filtrando por publicaciones populares');
                break;
            case 'recientes':
                // Filter by recent
                alert('Filtrando por publicaciones recientes');
                break;
            case 'siguiendo':
                // Filter by following
                alert('Filtrando por usuarios que sigues');
                break;
            default:
                // Show all
                alert('Mostrando todas las publicaciones');
        }
    }

    // Bootstrap tab functionality is handled automatically
});
</script>
@endsection