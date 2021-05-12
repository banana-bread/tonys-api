<?php

namespace App\Policies;

use App\Models\ServiceDefinition;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceDefinitionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceDefinition  $serviceDefinition
     * @return mixed
     */
    public function view(User $user, ServiceDefinition $serviceDefinition)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->isOwner();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceDefinition  $serviceDefinition
     * @return mixed
     */
    public function update(User $user, ServiceDefinition $serviceDefinition)
    {
        return ($user->isAdmin() || $user->isOwner()) && $user->employee->company_id === $serviceDefinition->company_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceDefinition  $serviceDefinition
     * @return mixed
     */
    public function delete(User $user, ServiceDefinition $serviceDefinition)
    {
        return ($user->isAdmin() || $user->isOwner()) && $user->employee->company_id === $serviceDefinition->company_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceDefinition  $serviceDefinition
     * @return mixed
     */
    public function restore(User $user, ServiceDefinition $serviceDefinition)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceDefinition  $serviceDefinition
     * @return mixed
     */
    public function forceDelete(User $user, ServiceDefinition $serviceDefinition)
    {
        //
    }
    
}
