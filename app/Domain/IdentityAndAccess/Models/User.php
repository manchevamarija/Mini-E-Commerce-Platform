<?php

namespace App\Domain\IdentityAndAccess\Models;

use App\Domain\IdentityAndAccess\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected static function newFactory(): \Database\Factories\UserFactory
    {
        return \Database\Factories\UserFactory::new();
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_buyer',
        'is_vendor',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_buyer' => 'boolean',
            'is_vendor' => 'boolean',
        ];
    }

    public function vendor(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Domain\ProductCatalog\Models\Vendor::class);
    }

    public function cart(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Domain\Cart\Models\Cart::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Domain\OrderManagement\Models\Order::class);
    }

    public function isVendor(): bool
    {
        return $this->is_vendor || $this->role === UserRole::Vendor;
    }

    public function isBuyer(): bool
    {
        return $this->is_buyer || $this->role === UserRole::Buyer;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }
}
