<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Mostrar todas las publicaciones con paginación y conteos
     */
    public function index()
    {
        $posts = Post::with(['user', 'comments.user', 'likes'])
                    ->withCount(['comments', 'likes'])
                    ->latest()
                    ->paginate(6);

        // Debug: Verificar datos en logs
        foreach($posts as $post) {
            \Log::info("Post ID: {$post->id}, Title: '{$post->title}', User: {$post->user->name}");
        }

        return view('posts.index', compact('posts'));
    }

    /**
     * Formulario para crear publicación
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Guardar nueva publicación con título y contenido
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
        ]);

        $post = Auth::user()->posts()->create([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        // Debug: Verificar creación
        \Log::info("Nueva publicación creada - ID: {$post->id}, Title: '{$post->title}'");

        return redirect()->route('posts.index')
            ->with('success', '¡Publicación creada exitosamente!');
    }

    /**
     * Mostrar una publicación específica con todas sus relaciones
     */
    public function show(Post $post)
    {
        // Cargar todas las relaciones necesarias para la vista show mejorada
        $post->load([
            'user', 
            'comments.user', 
            'comments.likes', 
            'likes',
            'comments' => function($query) {
                $query->withCount('likes')->latest();
            }
        ])->loadCount(['comments', 'likes']);

        return view('posts.show', compact('post'));
    }

    /**
     * Formulario para editar publicación
     */
    public function edit(Post $post)
    {
        $this->authorizePost($post);
        return view('posts.edit', compact('post'));
    }

    /**
     * Actualizar publicación
     */
    public function update(Request $request, Post $post)
    {
        $this->authorizePost($post);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
        ]);

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        \Log::info("Publicación actualizada - ID: {$post->id}, Nuevo título: '{$post->title}'");

        return redirect()->route('posts.index')
            ->with('success', '¡Publicación actualizada exitosamente!');
    }

    /**
     * Eliminar publicación
     */
    public function destroy(Post $post)
    {
        $this->authorizePost($post);
        
        $postId = $post->id;
        $postTitle = $post->title;
        
        $post->delete();

        \Log::info("Publicación eliminada - ID: {$postId}, Título: '{$postTitle}'");

        return redirect()->route('posts.index')
            ->with('success', '¡Publicación eliminada exitosamente!');
    }

    /**
     * Autorización: solo el dueño puede editar/eliminar
     */
    private function authorizePost(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para modificar esta publicación.');
        }
    }

    /**
     * API: Toggle like para publicación (para el JavaScript)
     */
    public function toggleLike(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $request->validate([
            'type' => 'required|in:post,comment',
            'id' => 'required|integer'
        ]);

        $user = Auth::user();
        
        try {
            if ($request->type === 'post') {
                $model = Post::findOrFail($request->id);
            } else {
                $model = Comment::findOrFail($request->id);
            }

            $liked = $model->likes()->where('user_id', $user->id)->exists();

            if ($liked) {
                $model->likes()->where('user_id', $user->id)->delete();
                $liked = false;
            } else {
                $model->likes()->create(['user_id' => $user->id]);
                $liked = true;
            }

            return response()->json([
                'success' => true,
                'liked' => $liked,
                'count' => $model->likes()->count()
            ]);

        } catch (\Exception $e) {
            \Log::error("Error en toggleLike: " . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Buscar publicaciones (para la funcionalidad de búsqueda)
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $posts = Post::with(['user', 'comments.user', 'likes'])
                    ->withCount(['comments', 'likes'])
                    ->where(function($q) use ($query) {
                        $q->where('title', 'LIKE', "%{$query}%")
                          ->orWhere('content', 'LIKE', "%{$query}%")
                          ->orWhereHas('user', function($userQuery) use ($query) {
                              $userQuery->where('name', 'LIKE', "%{$query}%");
                          });
                    })
                    ->latest()
                    ->paginate(6);

        return view('posts.index', compact('posts', 'query'));
    }

    /**
     * Verificar estructura de datos de una publicación (para debug)
     */
    public function debugPost($id)
    {
        $post = Post::with('user')->find($id);
        
        if (!$post) {
            return response()->json(['error' => 'Post no encontrado'], 404);
        }

        return response()->json([
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
            'user_id' => $post->user_id,
            'user_name' => $post->user->name,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at
        ]);
    }

    /**
     * Toggle like para publicación (método alternativo para el formulario tradicional)
     */
    public function toggleLikePost(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id'
        ]);

        $user = Auth::user();
        $post = Post::findOrFail($request->post_id);

        $existingLike = Like::where('user_id', $user->id)
                           ->where('likeable_id', $post->id)
                           ->where('likeable_type', Post::class)
                           ->first();

        if ($existingLike) {
            // Si ya existe el like, lo eliminamos (toggle off)
            $existingLike->delete();
            $liked = false;
            $message = 'Like removido';
        } else {
            // Si no existe, creamos el like (toggle on)
            Like::create([
                'user_id' => $user->id,
                'likeable_id' => $post->id,
                'likeable_type' => Post::class
            ]);
            $liked = true;
            $message = 'Like agregado';
        }

        // Obtener el nuevo conteo de likes
        $likesCount = $post->likes()->count();

        if ($request->ajax()) {
            return response()->json([
                'liked' => $liked,
                'likes_count' => $likesCount,
                'message' => $message
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Filtrar publicaciones por tipo (populares, recientes, etc.)
     */
    public function filter(Request $request)
    {
        $filter = $request->get('filter', 'todos');
        
        $posts = Post::with(['user', 'comments.user', 'likes'])
                    ->withCount(['comments', 'likes']);

        switch ($filter) {
            case 'populares':
                $posts->orderBy('likes_count', 'desc');
                break;
            case 'recientes':
                $posts->latest();
                break;
            case 'siguiendo':
                // Aquí puedes implementar la lógica para usuarios seguidos
                $followingIds = Auth::check() ? Auth::user()->following()->pluck('id') : [];
                $posts->whereIn('user_id', $followingIds)->latest();
                break;
            default:
                $posts->latest();
                break;
        }

        $posts = $posts->paginate(6);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('posts.partials.posts-grid', compact('posts'))->render()
            ]);
        }

        return view('posts.index', compact('posts', 'filter'));
    }

    /**
     * Obtener estadísticas de publicaciones (para el tab de estadísticas)
     */
    public function getStats()
    {
        $stats = [
            'total_posts' => Post::count(),
            'total_comments' => Comment::count(),
            'total_likes' => Like::count(),
            'posts_today' => Post::whereDate('created_at', today())->count(),
            'popular_posts' => Post::withCount('likes')
                                 ->orderBy('likes_count', 'desc')
                                 ->take(5)
                                 ->get()
        ];

        return response()->json($stats);
    }
}