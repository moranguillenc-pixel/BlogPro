<?php

namespace App\Policies;

use App\Models\Nota;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return true; // Usuarios autenticados pueden ver sus notas
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Nota $nota)
    {
        // Solo el dueño de la nota puede verla
        return $user->id === $nota->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return true; // Usuarios autenticados pueden crear notas
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Nota $nota)
    {
        // Solo el dueño de la nota puede actualizarla
        return $user->id === $nota->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Nota $nota)
    {
        // Solo el dueño de la nota puede eliminarla
        return $user->id === $nota->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Nota $nota)
    {
        // Solo el dueño de la nota puede restaurarla
        return $user->id === $nota->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Nota $nota)
    {
        // Solo el dueño de la nota puede eliminarla permanentemente
        return $user->id === $nota->user_id;
    }
}