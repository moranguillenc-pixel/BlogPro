<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlogPro - @yield('title', 'Publicaciones')</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --text-dark: #2c3e50;
            --text-light: #7f8c8d;
            --background-light: #f8f9fa;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background-light);
            padding-top: 0;
        }

        .navbar-brand {
            font-size: 1.5rem;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 0;
        }

        .post-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            background: white;
            margin-bottom: 1.5rem;
        }

        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
        }

        .btn-create {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .shadow-hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        /* Tabs Styles */
        .nav-tabs-custom {
            border-bottom: 2px solid #dee2e6;
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

        .tab-pane {
            padding: 2rem 0;
        }

        /* Filter Styles */
        .filter-group .form-check {
            margin-right: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .filter-group .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        /* Stats Cards */
        .stat-card {
            border-radius: 15px;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .comment-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
        }

        /* CORRECCIÓN DE TAMAÑO DE ICONOS */
        .navbar-nav .fas,
        .navbar-nav .far,
        .navbar-nav .fab {
            font-size: 1rem !important;
            width: 1rem !important;
            height: 1rem !important;
        }

        .btn .fas,
        .btn .far,
        .btn .fab {
            font-size: 0.9rem !important;
        }

        .navbar-brand .fas {
            font-size: 1.3rem !important;
        }

        /* Asegurar que todos los iconos tengan tamaño consistente */
        .fas, .far, .fab {
            font-size: 1rem;
            width: 1rem;
            height: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Iconos en navegación */
        .nav-link .fas,
        .nav-link .far,
        .nav-link .fab {
            font-size: 0.9rem;
            width: 1rem;
            margin-right: 0.3rem;
        }

        /* Iconos en botones */
        .btn .fas,
        .btn .far,
        .btn .fab {
            font-size: 0.9rem;
            width: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-gradient-primary shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                <i class="fas fa-blog me-2"></i>BlogPro
            </a>

            <!-- Navigation Links -->
            <div class="navbar-nav ms-auto flex-row">
                @auth
                    <a href="{{ route('posts.index') }}" class="nav-link me-3">
                        <i class="fas fa-home me-1"></i>Inicio
                    </a>
                    <a href="{{ route('notas.index') }}" class="nav-link me-3">
                        <i class="fas fa-sticky-note me-1"></i>Notas
                    </a>
                    <span class="nav-link me-3 text-light">
                        <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <a href="{{ route('logout') }}" class="nav-link d-inline" 
                           onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                        </a>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="nav-link me-3">
                        <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="nav-link">
                            <i class="fas fa-user-plus me-1"></i>Registrarse
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold">@yield('hero-title', 'Publicaciones Profesionales')</h1>
                    <p class="lead mb-0">@yield('hero-subtitle', 'Comparte tus ideas y conecta con la comunidad')</p>
                </div>
                <div class="col-md-4 text-end">
                    @auth
                    <a href="{{ route('posts.create') }}" class="btn btn-light btn-lg rounded-pill px-4">
                        <i class="fas fa-plus me-2"></i>Nueva Publicación
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 BlogPro. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Inicializar tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            // Tab functionality
            function switchTab(tabName) {
                // Hide all tab panes
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                });
                
                // Remove active class from all nav links
                document.querySelectorAll('.nav-tabs-custom .nav-link').forEach(link => {
                    link.classList.remove('active');
                });

                // Show selected tab pane and activate nav link
                document.getElementById('content-' + tabName).classList.add('show', 'active');
                document.getElementById('tab-' + tabName).classList.add('active');
            }

            // Make switchTab function globally available
            window.switchTab = switchTab;

            // Toggle comment sections
            window.toggleComment = function(postId) {
                const commentsSection = document.getElementById('comments-' + postId);
                if (commentsSection) {
                    commentsSection.classList.toggle('d-none');
                }
            }

            // Filter functionality
            document.querySelectorAll('input[name="filter"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const filter = this.value;
                    // Implement filtering logic here
                    console.log('Filtrar por:', filter);
                    
                    // You can add AJAX requests or page reload with parameters
                    switch(filter) {
                        case 'populares':
                            // Filter by popularity (likes)
                            filterByPopularity();
                            break;
                        case 'recientes':
                            // Filter by recent
                            filterByRecent();
                            break;
                        case 'siguiendo':
                            // Filter by following
                            filterByFollowing();
                            break;
                        default:
                            // Show all
                            showAllPosts();
                    }
                });
            });

            function filterByPopularity() {
                // Sort posts by likes count (you'll need to implement this)
                alert('Filtrando por publicaciones populares');
            }

            function filterByRecent() {
                // Sort posts by creation date
                alert('Filtrando por publicaciones recientes');
            }

            function filterByFollowing() {
                // Show posts from followed users
                alert('Filtrando por usuarios que sigues');
            }

            function showAllPosts() {
                // Show all posts
                alert('Mostrando todas las publicaciones');
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>