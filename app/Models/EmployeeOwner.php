<?php

namespace App\Models;

use App\Exceptions\EmployeeException;

class EmployeeOwner extends Employee 
{
    protected $table = 'employees';

    public function create()
    {
        $this->admin = true;
        $this->owner = true;

        return $this->save();
    }

    public function delete()
    {
        if ($this->isOnlyOwner())
        {
            throw new EmployeeException([], 'At least user must be an owner');
        }

        $this->owner = false; 
        
        return $this->save();
    }
}