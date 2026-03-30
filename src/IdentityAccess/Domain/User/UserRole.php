<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\IdentityAccess\Domain\User
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\IdentityAccess\Domain\User;

/**
 * All roles recognised by the platform.
 *
 * Admin roles: super-admin, admin, catalog-manager, order-manager,
 *              marketing-manager, customer-support, reports-viewer.
 * Storefront role: customer.
 */
enum UserRole: string
{
    case SuperAdmin        = 'super-admin';
    case Admin             = 'admin';
    case CatalogManager    = 'catalog-manager';
    case OrderManager      = 'order-manager';
    case MarketingManager  = 'marketing-manager';
    case CustomerSupport   = 'customer-support';
    case ReportsViewer     = 'reports-viewer';
    case Customer          = 'customer';

    /** Returns all roles that grant access to the admin panel. */
    public static function adminRoles(): array
    {
        return [
            self::SuperAdmin,
            self::Admin,
            self::CatalogManager,
            self::OrderManager,
            self::MarketingManager,
            self::CustomerSupport,
            self::ReportsViewer,
        ];
    }

    /** Returns the string values of all admin roles. */
    public static function adminRoleNames(): array
    {
        return array_map(fn (self $role) => $role->value, self::adminRoles());
    }

    public function isAdminRole(): bool
    {
        return in_array($this, self::adminRoles(), true);
    }

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin       => 'Super Admin',
            self::Admin            => 'Admin',
            self::CatalogManager   => 'Catalog Manager',
            self::OrderManager     => 'Order Manager',
            self::MarketingManager => 'Marketing Manager',
            self::CustomerSupport  => 'Customer Support',
            self::ReportsViewer    => 'Reports Viewer',
            self::Customer         => 'Customer',
        };
    }
}
