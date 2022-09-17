<?php

namespace App\Models;

use App\Traits\HasUuid;

class Note extends BaseModel
{
  use HasUuid;

  protected $visible = [
      'id',
      'noteable_id',
      'noteable_type',
      'body',
      'created_at',
      'updated_at'
  ];

  // RELATIONS

  public function notable()
  {
      return $this->morphTo();
  }
}
