<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\UserController;

// ==================== RUTAS PÚBLICAS ====================
Route::get('/', fn() => redirect()->route('posts.index'));

Auth::routes();

// ==================== RUTAS PROTEGIDAS ====================
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', fn() => redirect()->route('posts.index'))->name('dashboard');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::resource('users', UserController::class)->only(['index', 'show', 'edit', 'update']);

    Route::get('/sistema-notas', fn() => redirect()->route('notas.index'))->name('sistema.notas');

    // ==================== POSTS ====================
    Route::resource('posts', PostController::class);
    Route::get('/posts/search', [PostController::class, 'search'])->name('posts.search');
    Route::get('/posts/filter/{filter}', [PostController::class, 'filter'])->name('posts.filter');

    Route::post('/likes/toggle', [LikeController::class, 'toggle'])->name('likes.toggle');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::resource('comments', CommentController::class)->only(['edit', 'update', 'destroy']);

    // ==================== NOTAS ====================
    Route::resource('notas', NotaController::class);

    Route::prefix('notas')->group(function () {
        Route::get('/load-for-tabs', [NotaController::class, 'loadNotesForTabs'])->name('notas.load-for-tabs');
        Route::post('/{nota}/completar', [NotaController::class, 'completar'])->name('notas.completar');
        Route::post('/{nota}/pendiente', [NotaController::class, 'pendiente'])->name('notas.pendiente');
        Route::post('/{nota}/toggle-completada', [NotaController::class, 'toggleCompletada'])->name('notas.toggle-completada');
        Route::post('/{nota}/restore', [NotaController::class, 'restore'])->name('notas.restore');
        Route::delete('/{nota}/force-delete', [NotaController::class, 'forceDestroy'])->name('notas.force-delete');
        Route::get('/estadisticas', [NotaController::class, 'estadisticas'])->name('notas.estadisticas');
        Route::get('/search', [NotaController::class, 'search'])->name('notas.search');
    });

    // ==================== ACTIVIDADES (LÍNEA CLAVE CORREGIDA) ====================
    // Esta línea fuerza el nombre correcto del parámetro → {actividad}
    Route::resource('actividades', ActividadController::class)->parameters([
        'actividades' => 'actividad'
    ]);

    // Rutas extra de actividades
    Route::prefix('actividades')->group(function () {
        Route::post('/{actividad}/completar', [ActividadController::class, 'completar'])->name('actividades.completar');
        Route::post('/{actividad}/pendiente', [ActividadController::class, 'pendiente'])->name('actividades.pendiente');
        Route::post('/{actividad}/toggle-completada', [ActividadController::class, 'toggleCompletada'])->name('actividades.toggle-completada');
        Route::get('/por-nota/{nota}', [ActividadController::class, 'porNota'])->name('actividades.por-nota');
        Route::get('/corregir-actividades', [ActividadController::class, 'corregirActividades'])->name('actividades.corregir');
        Route::get('/diagnostico', [ActividadController::class, 'diagnostico'])->name('actividades.diagnostico');
    });
});

// ==================== RUTAS PÚBLICAS ADICIONALES ====================
Route::get('/public/posts', [PostController::class, 'index'])->name('posts.public.index');
Route::get('/public/posts/{post}', [PostController::class, 'show'])->name('posts.public.show');

// ==================== RUTAS DE DEBUG (puedes dejarlas o borrarlas después) ====================
Route::get('/fix-actividades-now', function () { /* tu código de fix */ });
Route::get('/debug-actividades-table', function () { /* tu código */ });
// ... (el resto de rutas de debug que tenías)