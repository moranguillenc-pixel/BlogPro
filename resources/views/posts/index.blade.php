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
                            <a href="{{ route('posts.index') }}" class="btn btn-primary rounded-pill px-4">
                                <i class="fas fa-stream me-2"></i>Todos
                            </a>
                            <a href="{{ route('posts.filter', 'popular') }}" class="btn btn-outline-primary rounded-pill px-4">
                                <i class="fas fa-fire me-2"></i>Populares
                            </a>
                            <a href="{{ route('posts.filter', 'recent') }}" class="btn btn-outline-primary rounded-pill px-4">
                                <i class="fas fa-clock me-2"></i>Recientes
                            </a>
                            @auth
                            <a href="{{ route('posts.filter', 'mine') }}" class="btn btn-outline-primary rounded-pill px-4">
                                <i class="fas fa-user me-2"></i>Mis Publicaciones
                            </a>
                            @endauth
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

                @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-4 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-3 fs-5"></i>
                        <div class="flex-grow-1">
                            <strong>Error!</strong> {{ session('error') }}
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
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($post->user->name ?? 'Usuario') }}&background=667eea&color=fff&bold=true" 
                                             alt="{{ $post->user->name ?? 'Usuario' }}" class="rounded-circle" width="45" height="45">
                                    </div>
                                    <div class="user-info flex-grow-1">
                                        <h6 class="mb-0 fw-bold text-dark">{{ $post->user->name ?? 'Usuario' }}</h6>
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>{{ $post->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    @auth
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
                                                                onclick="return confirm('¿Estás seguro de eliminar esta publicación?')">
                                                            <i class="fas fa-trash me-2"></i>Eliminar
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                        @endif
                                    @endauth
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
                                    @auth
                                        <form action="{{ route('likes.toggle') }}" method="POST" class="d-inline flex-fill">
                                            @csrf
                                            <input type="hidden" name="post_id" value="{{ $post->id }}">
                                            <button type="submit" 
                                                    class="btn btn-outline-danger btn-sm w-100 like-btn {{ $post->isLikedBy(auth()->user()) ? 'btn-danger text-white' : '' }}">
                                                <i class="{{ $post->isLikedBy(auth()->user()) ? 'fas' : 'far' }} fa-heart me-1"></i>
                                                Me gusta
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-outline-danger btn-sm flex-fill">
                                            <i class="far fa-heart me-1"></i>Me gusta
                                        </a>
                                    @endauth
                                    
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

                <!-- Paginación -->
                @if($posts->hasPages())
                <div class="row mt-4 px-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    Mostrando {{ $posts->firstItem() ?? 0 }}-{{ $posts->lastItem() ?? 0 }} de {{ $posts->total() }} publicaciones
                                </small>
                            </div>
                            <div>
                                {{ $posts->links() }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Notas Tab -->
            <div class="tab-pane fade" id="content-notas" role="tabpanel">
                <div class="p-4">
                    <!-- Mensajes para notas -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Header de Notas -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="h3 mb-0">Sistema de Notas</h2>
                        <a href="{{ route('notas.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Nueva Nota
                        </a>
                    </div>

                    <!-- Contenido dinámico de notas -->
                    <div id="notas-content">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">Cargando notas...</span>
                            </div>
                            <p>Cargando sistema de notas...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas Tab -->
            <div class="tab-pane fade" id="content-estadisticas" role="tabpanel">
                <div class="p-4">
                    <div class="row g-4">
                        <!-- Tarjetas de Estadísticas Principales -->
                        <div class="col-md-3">
                            <div class="card stat-card border-0 bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-newspaper fa-2x"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h4 class="mb-0">{{ $posts->total() ?? 0 }}</h4>
                                            <p class="mb-0">Total Publicaciones</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card border-0 bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-sticky-note fa-2x"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h4 class="mb-0" id="total-notas">0</h4>
                                            <p class="mb-0">Notas Activas</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card border-0 bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h4 class="mb-0" id="total-usuarios">0</h4>
                                            <p class="mb-0">Usuarios</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card border-0 bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-comments fa-2x"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h4 class="mb-0" id="total-comentarios">0</h4>
                                            <p class="mb-0">Comentarios</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas Detalladas -->
                    <div class="row g-4 mt-2">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-line me-2 text-primary"></i>
                                        Actividad Reciente
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Publicaciones hoy</span>
                                            <span class="badge bg-primary rounded-pill" id="posts-today">0</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Comentarios hoy</span>
                                            <span class="badge bg-success rounded-pill" id="comments-today">0</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Likes totales</span>
                                            <span class="badge bg-danger rounded-pill" id="total-likes">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-tasks me-2 text-success"></i>
                                        Sistema de Notas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Total actividades</span>
                                            <span class="badge bg-info rounded-pill" id="total-actividades">0</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Actividades completadas</span>
                                            <span class="badge bg-success rounded-pill" id="actividades-completadas">0</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Recordatorios activos</span>
                                            <span class="badge bg-warning rounded-pill" id="recordatorios-activos">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

/* Mejoras de responsividad */
@media (max-width: 768px) {
    .nav-tabs-custom .nav-link {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    
    .search-box .input-group-lg {
        flex-direction: column;
    }
    
    .search-box .input-group-lg .btn {
        margin-top: 0.5rem;
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-ocultar alertas después de 5 segundos
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Confirmación para eliminación
    document.querySelectorAll('form[action*="destroy"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('¿Estás seguro de que deseas eliminar esta publicación? Esta acción no se puede deshacer.')) {
                e.preventDefault();
            }
        });
    });

    // Efectos hover mejorados
    document.querySelectorAll('.post-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Navegación por tabs - Cargar contenido dinámico
    const tabEl = document.getElementById('myTab');
    if (tabEl) {
        tabEl.addEventListener('shown.bs.tab', function (event) {
            const target = event.target.getAttribute('data-bs-target');
            
            // Guardar el tab activo en localStorage
            localStorage.setItem('activeTab', event.target.getAttribute('id'));
            
            // Cargar contenido dinámico para notas
            if (target === '#content-notas') {
                loadNotasContent();
            }
            
            // Cargar estadísticas
            if (target === '#content-estadisticas') {
                loadEstadisticas();
            }
        });
    }

    // Restaurar tab activo al recargar
    const activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        const triggerEl = document.querySelector(`#${activeTab}`);
        if (triggerEl) {
            bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        }
    }

    // Cargar contenido de notas
    function loadNotasContent() {
        const notasContent = document.getElementById('notas-content');
        if (notasContent && !notasContent.dataset.loaded) {
            fetch('{{ route("notas.load-for-tabs") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        notasContent.innerHTML = data.html;
                        notasContent.dataset.loaded = true;
                    } else {
                        notasContent.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Error al cargar las notas: ${data.error}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    notasContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error de conexión al cargar las notas.
                        </div>
                    `;
                });
        }
    }

    // Cargar estadísticas
    function loadEstadisticas() {
        // Simular carga de estadísticas (deberías crear una ruta API para esto)
        setTimeout(() => {
            document.getElementById('total-notas').textContent = '0';
            document.getElementById('total-usuarios').textContent = '0';
            document.getElementById('total-comentarios').textContent = '0';
            document.getElementById('posts-today').textContent = '0';
            document.getElementById('comments-today').textContent = '0';
            document.getElementById('total-likes').textContent = '0';
            document.getElementById('total-actividades').textContent = '0';
            document.getElementById('actividades-completadas').textContent = '0';
            document.getElementById('recordatorios-activos').textContent = '0';
        }, 500);
    }

    // Cargar notas si el tab está activo al inicio
    if (document.querySelector('#content-notas').classList.contains('show')) {
        loadNotasContent();
    }

    // Cargar estadísticas si el tab está activo al inicio
    if (document.querySelector('#content-estadisticas').classList.contains('show')) {
        loadEstadisticas();
    }
});
</script>
@endpush