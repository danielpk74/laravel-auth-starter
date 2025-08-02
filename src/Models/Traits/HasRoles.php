<?php

namespace Danielpk74\LaravelAuthStarter\Models\Traits;

use Danielpk74\LaravelAuthStarter\Enums\RoleType;

trait HasRoles
{
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

    /**
     * Assign a role to the user
     */
    public function assignRole(RoleType $role): void
    {
        $this->update(['role' => $role->value]);
    }

    /**
     * Get role label for display
     */
    public function getRoleLabel(): string
    {
        $roleValue = $this->getRawOriginal('role');
        
        try {
            return RoleType::from($roleValue)->getLabel();
        } catch (\ValueError $e) {
            return 'Unknown';
        }
    }
}
