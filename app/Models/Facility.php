<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends BaseModel
{
    use SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        'coordinates' => 'array',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function type()
    {
        return $this->belongsTo(FacilityType::class, 'facility_type_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
