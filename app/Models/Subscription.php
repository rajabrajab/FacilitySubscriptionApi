<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'used_times' => 'integer',
        'expire_at'  => 'datetime'
    ];

    public const STATUS_PENDING   = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED   = 'expired';


   public static function statuses()
   {
    return [
        self::STATUS_PENDING   => __('panel.pending'),
        self::STATUS_CONFIRMED => __('panel.confirmed'),
        self::STATUS_CANCELLED => __('panel.cancelled'),
        self::STATUS_EXPIRED   => __('panel.expired'),
    ];
   }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_CONFIRMED]);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('status', self::STATUS_EXPIRED)
              ->orWhere(function (Builder $q2) {
                  $q2->whereNotNull('expire_at')
                     ->where('expire_at', '<', now());
              });
        });
    }

    public function incrementUsage(int $times = 1): self
    {
        $this->used_times = $this->used_times + $times;
        $this->save();

        return $this;
    }
}
