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

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class ReportsController
{
    public function __invoke(Request $request): Response
    {
        $range = $request->integer('days', 30);
        $range = in_array($range, [7, 30, 90, 365]) ? $range : 30;

        $from = now()->subDays($range - 1)->toDateString();

        // 1. Sales trend from daily_sales_aggregates (pre-aggregated)
        $trend = DB::table('daily_sales_aggregates')
            ->where('date', '>=', $from)
            ->orderBy('date')
            ->get(['date', 'order_count', 'gross_revenue', 'net_revenue', 'discount_total', 'refund_total', 'new_customers'])
            ->map(fn ($r) => [
                'date'           => $r->date,
                'order_count'    => (int) $r->order_count,
                'gross_revenue'  => (int) $r->gross_revenue,
                'net_revenue'    => (int) $r->net_revenue,
                'discount_total' => (int) $r->discount_total,
                'refund_total'   => (int) $r->refund_total,
                'new_customers'  => (int) $r->new_customers,
            ])->toArray();

        // 2. Order status breakdown for the period (live query — lightweight)
        $statusBreakdown = DB::table('orders')
            ->where('created_at', '>=', $from)
            ->groupBy('status')
            ->get([DB::raw('status'), DB::raw('COUNT(*) as count')])
            ->map(fn ($r) => ['status' => $r->status, 'count' => (int) $r->count])
            ->toArray();

        // 3. Top products by order item quantity (live, limited)
        $topProducts = DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->where('o.created_at', '>=', $from)
            ->whereNotIn('o.status', ['cancelled', 'payment_failed'])
            ->groupBy('oi.product_id', 'oi.name_snapshot', 'oi.sku_snapshot')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get([
                'oi.product_id',
                DB::raw('oi.name_snapshot as name'),
                DB::raw('oi.sku_snapshot as sku'),
                DB::raw('SUM(oi.qty) as total_qty'),
                DB::raw('SUM(oi.row_total) as total_revenue'),
            ])
            ->map(fn ($r) => [
                'product_id'    => $r->product_id,
                'name'          => $r->name,
                'sku'           => $r->sku,
                'total_qty'     => (int) $r->total_qty,
                'total_revenue' => (int) $r->total_revenue,
            ])->toArray();

        // 4. Summary totals for the range (from live orders for accuracy)
        $totals = DB::table('orders')
            ->where('created_at', '>=', $from)
            ->whereNotIn('status', ['payment_failed'])
            ->selectRaw('COUNT(*) as order_count, SUM(grand_total) as revenue, SUM(discount_total) as discounts, SUM(refund_amount) as refunds')
            ->first();

        return Inertia::render('admin/reports/index', [
            'trend'          => $trend,
            'statusBreakdown' => $statusBreakdown,
            'topProducts'    => $topProducts,
            'totals'         => [
                'order_count' => (int) ($totals->order_count ?? 0),
                'revenue'     => (int) ($totals->revenue ?? 0),
                'discounts'   => (int) ($totals->discounts ?? 0),
                'refunds'     => (int) ($totals->refunds ?? 0),
            ],
            'range' => $range,
        ]);
    }
}
