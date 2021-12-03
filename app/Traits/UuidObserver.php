<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

use Ramsey\Uuid\Uuid;

class UuidObserver
{
    /**
     * Listen to the User created event.
     *
     * @param  Model  $model
     * @return bool
     */
    public function creating(Model $model)
    {
        $model->{$model->getKeyName()} = Uuid::uuid4()->toString();

        return true;
    }
}
