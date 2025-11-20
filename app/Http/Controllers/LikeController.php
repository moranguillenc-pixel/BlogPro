<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'likeable_type' => 'required|in:post,comment',
            'likeable_id' => 'required|integer'
        ]);

        $user = Auth::user();
        
        try {
            if ($request->likeable_type === 'post') {
                $model = Post::findOrFail($request->likeable_id);
                $likeableType = 'App\Models\Post';
            } else {
                $model = Comment::findOrFail($request->likeable_id);
                $likeableType = 'App\Models\Comment';
            }

            $existingLike = Like::where('user_id', $user->id)
                              ->where('likeable_id', $request->likeable_id)
                              ->where('likeable_type', $likeableType)
                              ->first();

            if ($existingLike) {
                $existingLike->delete();
                $liked = false;
            } else {
                // CORRECCIÓN: Sin named parameters
                Like::create([
                    'user_id' => $user->id,
                    'likeable_id' => $request->likeable_id,
                    'likeable_type' => $likeableType
                ]);
                $liked = true;
            }

            // CORRECCIÓN: Sin named parameters
            $model->loadCount('likes');

            // CORRECCIÓN: Sin named parameters
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'count' => $model->likes_count
            ]);

        } catch (\Exception $e) {
            // CORRECCIÓN: Sin named parameters
            \Log::error("Error en toggleLike: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }
}