<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function createUser(User $user){
        return $user->can('Crear usuarios');
    }
    public function updateUser(User $user){
        return $user->can('Editar usuarios');
    }
    public function deleteUser(User $user){
        return $user->can('Eliminar usuarios');
    }
    public function navegarUser(User $user){
        return $user->can('Navegar usuarios');
    }
}
