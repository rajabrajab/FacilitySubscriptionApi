<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class TargetGroup extends BaseModel
{
    public function packages()
    {
        return $this->hasMany(Package::class);
    }
}
