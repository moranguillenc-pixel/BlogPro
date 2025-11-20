<?php

namespace App\Policies;

use App\Models\Actividad;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActividadPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Actividad $actividad)
    {
        // El usuario puede ver la actividad si es dueño de la nota
        return $user->id === $actividad->nota->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Actividad $actividad)
    {
        // El usuario puede editar la actividad si es dueño de la nota
        return $user->id === $actividad->nota->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Actividad $actividad)
    {
        // El usuario puede eliminar la actividad si es dueño de la nota
        return $user->id === $actividad->nota->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Actividad $actividad)
    {
        return $user->id === $actividad->nota->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Actividad $actividad)
    {
        return $user->id === $actividad->nota->user_id;
    }
}