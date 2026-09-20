<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Definisikan konstanta untuk role user
    public const ROLE_OWNER = 1;
    public const ROLE_BUYER = 2;
    public const ROLE_ADMIN = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone', // ganti no_telp menjadi phone, mengikuti migrasi dan controller
        'location',
        'role_id',
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

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Method untuk mengecek apakah user adalah owner
    public function isOwner()
    {
        return $this->role_id == self::ROLE_OWNER;
    }

    // Method untuk mengecek apakah user adalah buyer
    public function isBuyer()
    {
        return $this->role_id == self::ROLE_BUYER;
    }

    // Method untuk mengecek apakah user adalah admin (pengelola web)
    public function isAdmin()
    {
        return $this->role_id == self::ROLE_ADMIN;
    }
}
