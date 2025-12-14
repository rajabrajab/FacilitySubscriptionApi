<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;
use TomatoPHP\FilamentLanguageSwitcher\Traits\InteractsWithLanguages;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements FilamentUser,HasAvatar
{
    use HasFactory, Notifiable, InteractsWithLanguages, HasApiTokens, SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->type === 'admin';
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

   public function getProfileImageUrlAttribute()
    {
        if (!$this->profile_image) {


            return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=random';
        }

        return asset('storage/'.$this->profile_image);
    }

    public function getPhoneAttribute()
    {
        return  $this->country_code . $this->number;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->profileImageUrl;
    }

    public function updateDeviceToken(string $token)
    {
        $this->update([
            'fcm' => $token
        ]);
    }

    public function facility(){
        return $this->belongsTo(Facility::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function getNoSubscriptionsAttribute()
    {
        return $this->subscriptions->count() === 0;
    }
}
