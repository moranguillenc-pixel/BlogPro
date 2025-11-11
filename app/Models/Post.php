<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'user_id'];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con comentarios
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Relación polimórfica con likes
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    // Método para verificar si un usuario dio like (combinado)
    public function isLikedBy($user)
    {
        if (!$user) {
            return false;
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    // Método likedBy (alias para compatibilidad)
    public function likedBy(User $user)
    {
        return $this->isLikedBy($user);
    }

    // Contador de likes
    public function likesCount()
    {
        return $this->likes()->count();
    }

    // Contador de comentarios
    public function commentsCount()
    {
        return $this->comments()->count();
    }

    // Scope para posts populares (por likes)
    public function scopePopular($query)
    {
        return $query->withCount('likes')->orderBy('likes_count', 'desc');
    }

    // Scope para posts recientes
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Cargar relaciones comúnmente usadas
    public function loadCommonRelations()
    {
        return $this->load(['user', 'comments.user', 'likes'])
                   ->loadCount(['comments', 'likes']);
    }
}