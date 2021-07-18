<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePolicy
{
    use HandlesAuthorization;

    public function update(User $user, Employee $employee)
    {
        return ($user->isOwner() && $user->employee->company_id === $employee->company_id) || 
                $user->employee->id === $employee->id;
    }
}
