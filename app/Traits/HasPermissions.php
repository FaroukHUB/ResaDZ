<?php

namespace App\Traits;

trait HasPermissions
{
    /**
     * Define permissions for each role.
     */
    protected static array $rolePermissions = [
        'super_admin' => ['*'], // All permissions
        'admin' => [
            'view_bookings',
            'edit_bookings',
            'view_vehicles',
            'edit_vehicles',
            'view_loueurs',
            'edit_loueurs',
            'view_reviews',
            'edit_reviews',
            'approve_reviews',
            'view_invoices',
            'create_invoices',
            'view_statistics',
            'view_conversations',
            'manage_blog',
            'manage_catalog',
            'view_leads',
        ],
        'moderator' => [
            'view_bookings',
            'view_vehicles',
            'view_loueurs',
            'view_reviews',
            'approve_reviews',
            'view_conversations',
        ],
        'support' => [
            'view_bookings',
            'view_loueurs',
            'view_conversations',
            'reply_conversations',
        ],
    ];

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        $role = $this->role ?? 'user';

        // Super admin has all permissions
        if ($role === 'super_admin') {
            return true;
        }

        // Get permissions for the role
        $permissions = self::$rolePermissions[$role] ?? [];

        // Check for wildcard permission
        if (in_array('*', $permissions)) {
            return true;
        }

        return in_array($permission, $permissions);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is any type of admin.
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin', 'moderator', 'support']);
    }

    /**
     * Check if user can access the admin panel.
     */
    public function canAccessAdminPanel(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Get all permissions for this user.
     */
    public function getPermissions(): array
    {
        $role = $this->role ?? 'user';

        if ($role === 'super_admin') {
            return ['*'];
        }

        return self::$rolePermissions[$role] ?? [];
    }
}
