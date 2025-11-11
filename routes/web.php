<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotaController;

// ==================== RUTAS PÚBLICAS ====================
// Página de bienvenida - redirige a publicaciones
Route::get('/', function () {
    return redirect()->route('posts.index');
});

// ==================== RUTAS DE AUTENTICACIÓN (Laravel UI) ====================
// Rutas de autenticación generadas por Laravel UI (login, register, etc.)
Auth::routes();

// ==================== RUTAS PROTEGIDAS ====================

Route::middleware(['auth'])->group(function () {
    
    // Dashboard - redirige a publicaciones
    Route::get('/dashboard', function () {
        return redirect()->route('posts.index');
    })->name('dashboard');

    // Home (opcional - si usas el HomeController)
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // ========== RUTAS DE PUBLICACIONES ==========
    
    // Resource routes para posts (incluye index, create, store, edit, update, destroy)
    Route::resource('posts', PostController::class)->except(['show']);
    
    // Ruta show separada para acceso público opcional
    Route::get('/posts/{post}', [PostController::class, 'show'])
        ->name('posts.show');
    
    // Búsqueda de publicaciones
    Route::get('/posts/search', [PostController::class, 'search'])
        ->name('posts.search');

    // Rutas adicionales para posts
    Route::post('/posts/toggle-like', [PostController::class, 'toggleLike'])->name('posts.toggleLike');
    Route::post('/posts/toggle-like-post', [PostController::class, 'toggleLikePost'])->name('posts.toggleLikePost');
    Route::get('/posts/filter/{filter}', [PostController::class, 'filter'])->name('posts.filter');
    Route::get('/posts/stats', [PostController::class, 'getStats'])->name('posts.stats');
    Route::get('/debug/post/{id}', [PostController::class, 'debugPost'])->name('posts.debug');

    // ========== RUTAS DE COMENTARIOS ==========
    
    Route::prefix('posts/{post}')->group(function () {
        // Crear comentario
        Route::post('/comments', [CommentController::class, 'store'])
            ->name('comments.store');
    });

    // Editar, actualizar y eliminar comentarios
    Route::resource('comments', CommentController::class)->only(['edit', 'update', 'destroy']);

    // ========== RUTAS DE LIKES ==========
    
    Route::post('/likes/toggle', [LikeController::class, 'toggle'])
        ->name('likes.toggle');

    // ========== RUTAS DEL SISTEMA DE NOTAS (LABORATORIO 13) ==========
    
    // Listar todas las notas
    Route::get('/notas', [NotaController::class, 'index'])
        ->name('notas.index');
    
    // Ruta para cargar notas en pestañas
    Route::get('/notas/load-for-tabs', [NotaController::class, 'loadNotesForTabs'])
        ->name('notas.load-for-tabs');
    
    // Crear nueva nota
    Route::post('/notas', [NotaController::class, 'store'])
        ->name('notas.store');
    
    // Opcional: Rutas adicionales para el CRUD completo de notas
    Route::get('/notas/create', [NotaController::class, 'create'])
        ->name('notas.create');
    
    Route::get('/notas/{nota}/edit', [NotaController::class, 'edit'])
        ->name('notas.edit');
    
    Route::put('/notas/{nota}', [NotaController::class, 'update'])
        ->name('notas.update');
    
    Route::delete('/notas/{nota}', [NotaController::class, 'destroy'])
        ->name('notas.destroy');
    
    // Marcar recordatorio como completado
    Route::post('/notas/{nota}/completar', [NotaController::class, 'completar'])
        ->name('notas.completar');

});

// ==================== RUTAS PÚBLICAS ADICIONALES ====================

// Si quieres que las publicaciones sean visibles sin autenticación
Route::get('/public/posts', [PostController::class, 'index'])
    ->name('posts.public.index');

Route::get('/public/posts/{post}', [PostController::class, 'show'])
    ->name('posts.public.show');

// ==================== RUTAS DE DEBUG (TEMPORALES) ====================

// Eliminar estas rutas después de solucionar el problema
Route::get('/fix-posts', function() {
    try {
        $hasTitle = \Illuminate\Support\Facades\Schema::hasColumn('posts', 'title');
        
        if (!$hasTitle) {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE posts ADD COLUMN title VARCHAR(255) NOT NULL AFTER user_id');
            \Illuminate\Support\Facades\DB::update('UPDATE posts SET title = CONCAT("Publicación ", id) WHERE title IS NULL OR title = ""');
            return "✅ Columna 'title' agregada manualmente a la tabla posts";
        }
        
        return "✅ La columna 'title' ya existe en la tabla posts";
        
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});

Route::get('/check-posts-table', function() {
    try {
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('posts');
        $posts = \App\Models\Post::all();
        
        return response()->json([
            'success' => true,
            'table_columns' => $columns,
            'has_title_column' => in_array('title', $columns),
            'posts_count' => $posts->count(),
            'posts_sample' => $posts->take(3)->map(function($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'user_id' => $post->user_id,
                    'created_at' => $post->created_at
                ];
            })
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Debug para verificar las tablas de notas
Route::get('/check-notas-tables', function() {
    try {
        $notasColumns = \Illuminate\Support\Facades\Schema::getColumnListing('notas');
        $recordatoriosColumns = \Illuminate\Support\Facades\Schema::getColumnListing('recordatorios');
        
        $notasCount = \App\Models\Nota::count();
        $recordatoriosCount = \App\Models\Recordatorio::count();
        
        return response()->json([
            'success' => true,
            'notas_table' => [
                'columns' => $notasColumns,
                'count' => $notasCount
            ],
            'recordatorios_table' => [
                'columns' => $recordatoriosColumns,
                'count' => $recordatoriosCount
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Debug para verificar usuarios y relaciones
Route::get('/check-users-notes', function() {
    try {
        $users = \App\Models\User::withCount('notas')->get();
        $usersWithNotes = \App\Models\User::has('notas')->count();
        
        return response()->json([
            'success' => true,
            'total_users' => $users->count(),
            'users_with_notes' => $usersWithNotes,
            'users_data' => $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'notas_count' => $user->notas_count,
                    'has_notes_relationship' => method_exists($user, 'notas') ? 'YES' : 'NO'
                ];
            })
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});