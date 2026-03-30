<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Reporting\Presentation\Admin
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Reporting\Presentation\Admin;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class DashboardController
{
    public function __invoke(): Response
    {
        $today      = now()->toDateString();
        $thisMonth  = now()->startOfMonth()->toDateString();
        $lastMonth  = now()->subMonth()->startOfMonth()->toDateString();
        $lastMonthEnd = now()->subMonth()->endOfMonth()->toDateString();

        // Product counts
        $totalProducts  = DB::table('products')->whereNull('deleted_at')->count();
        $activeProducts = DB::table('products')->whereNull('deleted_at')->where('status', 'active')->count();

        // Order stats
        $ordersToday      = DB::table('orders')->whereDate('created_at', $today)->count();
        $ordersThisMonth  = DB::table('orders')->whereDate('created_at', '>=', $thisMonth)->count();
        $ordersLastMonth  = DB::table('orders')->whereBetween(DB::raw('DATE(created_at)'), [$lastMonth, $lastMonthEnd])->count();

        // Revenue (sum of order totals — uses grand_total column if it exists, falls back to 0)
        $revenueToday = DB::table('orders')
            ->whereDate('created_at', $today)
            ->whereNotIn('status', ['cancelled', 'pending_payment', 'payment_failed'])
            ->sum('grand_total') ?? 0;

        $revenueThisMonth = DB::table('orders')
            ->whereDate('created_at', '>=', $thisMonth)
            ->whereNotIn('status', ['cancelled', 'pending_payment', 'payment_failed'])
            ->sum('grand_total') ?? 0;

        $revenueLastMonth = DB::table('orders')
            ->whereBetween(DB::raw('DATE(created_at)'), [$lastMonth, $lastMonthEnd])
            ->whereNotIn('status', ['cancelled', 'pending_payment', 'payment_failed'])
            ->sum('grand_total') ?? 0;

        // Customers
        $totalCustomers = DB::table('customers')->count();
        $newThisMonth   = DB::table('customers')->whereDate('created_at', '>=', $thisMonth)->count();

        // Recent orders (last 10)
        $recentOrders = DB::table('orders')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['id', 'order_number', 'status', 'grand_total', 'created_at'])
            ->map(fn ($o) => [
                'id'           => $o->id,
                'order_number' => $o->order_number,
                'status'       => $o->status,
                'grand_total'  => (int) $o->grand_total,
                'created_at'   => $o->created_at,
            ])->toArray();

        // Low stock (qty_available <= 5)
        $lowStock = DB::table('inventory_items as i')
            ->join('products as p', 'p.id', '=', 'i.product_id')
            ->whereNull('p.deleted_at')
            ->where('p.status', 'active')
            ->where('i.qty_available', '<=', 5)
            ->limit(5)
            ->get(['p.id', 'p.name', 'p.sku', 'i.qty_available'])
            ->map(fn ($r) => [
                'id'            => $r->id,
                'name'          => $r->name,
                'sku'           => $r->sku,
                'qty_available' => (int) $r->qty_available,
            ])->toArray();

        return Inertia::render('admin/dashboard', [
            'stats' => [
                'products_total'       => $totalProducts,
                'products_active'      => $activeProducts,
                'orders_today'         => $ordersToday,
                'orders_this_month'    => $ordersThisMonth,
                'orders_last_month'    => $ordersLastMonth,
                'revenue_today'        => (int) $revenueToday,
                'revenue_this_month'   => (int) $revenueThisMonth,
                'revenue_last_month'   => (int) $revenueLastMonth,
                'customers_total'      => $totalCustomers,
                'customers_new_month'  => $newThisMonth,
            ],
            'recentOrders' => $recentOrders,
            'lowStock'     => $lowStock,
        ]);
    }
}
