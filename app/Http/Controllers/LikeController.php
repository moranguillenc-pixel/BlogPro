<?php

// Autor: Cesar Kana Huillcapacco
// Controlador para manejo de "likes" en posts y comentarios

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function toggle(Request $request)
    {
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
            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }
}
