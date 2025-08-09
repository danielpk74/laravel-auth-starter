<?php

namespace Danielpk74\LaravelAuthStarter\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Danielpk74\LaravelAuthStarter\Enums\RoleType;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = [
        'formatted_created_at'
    ];

    /**
     * UserAccessor
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('M d, Y');
    }

    /**
     * Get the role attribute as enum name for JSON serialization
     */
    public function getRoleAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }
        
        try {
            return RoleType::from($value)->name;
        } catch (\ValueError $e) {
            return $value; // Return original value if conversion fails
        }
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(RoleType $role): bool
    {
        return $this->getRawOriginal('role') == $role->value;
    }

    /**
     * Check if user has any of the specified roles
     */
    public function hasAnyRole(array $roles): bool
    {
        $userRoleValue = $this->getRawOriginal('role');
        
        foreach ($roles as $role) {
            // Handle both string and enum inputs
            if (is_string($role)) {
                $roleEnum = match(strtolower($role)) {
                    'admin' => RoleType::Admin,
                    'user' => RoleType::User,
                    default => null
                };
                if ($roleEnum && $userRoleValue == $roleEnum->value) {
                    return true;
                }
            } elseif ($role instanceof RoleType && $userRoleValue == $role->value) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(RoleType::Admin);
    }

    /**
     * Check if user is regular user
     */
    public function isUser(): bool
    {
        return $this->hasRole(RoleType::User);
    }
}
