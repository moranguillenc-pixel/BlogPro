<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{
    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = ['content', 'user_id', 'post_id'];

    /**
     * Relación: El usuario que escribió el comentario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: El post al que pertenece el comentario
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Relación polimórfica: Los likes que tiene el comentario
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Helper: ¿El usuario ya dio like a este comentario?
     */
    public function isLikedBy(User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Método likedBy (alias para compatibilidad)
     */
    public function likedBy(User $user): bool
    {
        return $this->isLikedBy($user);
    }

    /**
     * Contador de likes (atributo de acceso)
     */
    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }

    /**
     * Scope para comentarios recientes
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Cargar relaciones comúnmente usadas
     */
    public function loadCommonRelations()
    {
        return $this->load(['user', 'post', 'likes'])
                   ->loadCount('likes');
    }
}