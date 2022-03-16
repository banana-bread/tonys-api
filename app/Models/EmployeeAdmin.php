<?php

namespace App\Models;

use App\Exceptions\EmployeeException;

class EmployeeAdmin extends Employee 
{
    protected $table = 'employees';

    public function create()
    {
        $this->admin = true;
        return $this->save();
    }

    public function delete()
    {
        if ($this->isOnlyOwner())
        {
            throw new EmployeeException([], 'At least user must be an owner');
        }

        if ($this->isOwner())
        {
            $this->owner = false;
        }

        $this->admin = false;
        
        return $this->save();
    }
}