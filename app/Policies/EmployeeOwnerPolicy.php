<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeeOwnerPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->isOwner();
    }

    public function delete(User $user)
    {
        return $user->isOwner();
    }
}
