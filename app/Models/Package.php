<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'requires_approval' => 'boolean',
    ];

    public function targetGroup()
    {
        return $this->belongsToMany(FacilityType::class);
    }

    public function facilityTypes()
    {
        return $this->belongsToMany(FacilityType::class);
    }
}
