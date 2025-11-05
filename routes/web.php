<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\HomeController;

// ==================== RUTAS PÚBLICAS ====================
// Página de bienvenida - redirige a publicaciones
Route::get('/', function () {
    return redirect()->route('posts.index');
});

// ==================== RUTAS DE AUTENTICACIÓN (Laravel UI) ====================
// sergio ponce :v soy chechito profe
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
