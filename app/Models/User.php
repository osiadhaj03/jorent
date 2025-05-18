<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [

        'name',
        'midname',
        'lastname',
        'role',
        'status',
        'email_verified_at',
        'email',
        'password',
        'remember_token',
        'phone',
        'phone_verified_at',
        'address',
        'birth_date',
        'profile_photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    public function address()
{
    return $this->morphOne(Address::class, 'addressable');
}

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @return list<string>
     */
    protected function appends(): array
    {
        return [
            'full_address',
            'full_address_with_street',
        ];
    }

    public function getFullAddressAttribute()
    {
        return $this->address->full_address ?? null;
    }

    public function getFullAddressWithStreetAttribute()
    {
        return $this->address->full_address_with_street ?? null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true; // يمكنك تعديل هذا حسب احتياجاتك
    }
}
