<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Database\Seeders
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Permissions by domain ---

        $permissions = [
            // Catalog
            'catalog.products.view',
            'catalog.products.create',
            'catalog.products.edit',
            'catalog.products.delete',
            'catalog.categories.manage',
            'catalog.attributes.manage',
            'catalog.brands.manage',

            // Inventory
            'inventory.view',
            'inventory.manage',

            // Orders
            'orders.view',
            'orders.edit',
            'orders.cancel',
            'orders.refund',
            'orders.comments.add',

            // Customers
            'customers.view',
            'customers.edit',
            'customers.impersonate',

            // Promotions
            'promotions.coupons.manage',
            'promotions.cart_rules.manage',
            'promotions.catalog_rules.manage',

            // CMS
            'cms.pages.manage',
            'cms.blocks.manage',

            // Settings
            'settings.manage',
            'payment_methods.manage',
            'tax.manage',

            // Reports
            'reports.view',

            // Roles & Users
            'admin.users.manage',
            'admin.roles.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // --- Roles ---

        // Super Admin: bypasses all permission checks via Spatie's super-admin gate
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);

        // Admin: full access except role/user management
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(
            array_filter($permissions, fn ($p) => ! in_array($p, ['admin.roles.manage', 'admin.users.manage']))
        );

        // Catalog Manager
        $catalogManager = Role::firstOrCreate(['name' => 'catalog-manager', 'guard_name' => 'web']);
        $catalogManager->syncPermissions([
            'catalog.products.view',
            'catalog.products.create',
            'catalog.products.edit',
            'catalog.categories.manage',
            'catalog.attributes.manage',
            'catalog.brands.manage',
            'inventory.view',
            'inventory.manage',
        ]);

        // Order Manager
        $orderManager = Role::firstOrCreate(['name' => 'order-manager', 'guard_name' => 'web']);
        $orderManager->syncPermissions([
            'orders.view',
            'orders.edit',
            'orders.cancel',
            'orders.refund',
            'orders.comments.add',
            'customers.view',
        ]);

        // Customer Support
        $support = Role::firstOrCreate(['name' => 'customer-support', 'guard_name' => 'web']);
        $support->syncPermissions([
            'orders.view',
            'orders.comments.add',
            'customers.view',
            'customers.edit',
        ]);

        // Customer (storefront user — minimal permissions, mostly handled by policy)
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
    }
}
