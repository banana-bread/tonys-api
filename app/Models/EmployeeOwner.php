<?php

namespace App\Models;

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
        $this->owner = false; 
        
        return $this->save();
    }
}