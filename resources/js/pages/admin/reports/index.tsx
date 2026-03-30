import { Head, router } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { PageProps } from '@inertiajs/core';

interface TrendRow {
    date: string; order_count: number; gross_revenue: number; net_revenue: number;
    discount_total: number; refund_total: number; new_customers: number;
}
interface StatusRow { status: string; count: number; }
interface TopProduct { product_id: string; name: string; sku: string; total_qty: number; total_revenue: number; }
interface Totals { order_count: number; revenue: number; discounts: number; refunds: number; }

interface Props extends PageProps {
    trend: TrendRow[]; statusBreakdown: StatusRow[]; topProducts: TopProduct[];
    totals: Totals; range: number;
}

function fmt(cents: number): string {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(cents / 100);
}

const STATUS_VARIANT: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    processing: 'default', completed: 'default', delivered: 'default',
    pending_payment: 'secondary', shipped: 'secondary', on_hold: 'secondary',
    cancelled: 'destructive', payment_failed: 'destructive',
    refunded: 'outline',
};

const RANGES = [7, 30, 90, 365];

export default function ReportsIndex({ trend, statusBreakdown, topProducts, totals, range }: Props) {

    function setRange(days: number) {
        router.get('/admin/reports', { days }, { preserveState: true });
    }

    // Build a simple bar chart from trend data (CSS-based, no lib needed)
    const maxRevenue = Math.max(...trend.map(r => r.gross_revenue), 1);

    return (
        <AdminLayout>
            <Head title="Analytics" />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">Analytics</h1>
                    <div className="flex gap-1">
                        {RANGES.map(d => (
                            <Button key={d} size="sm" variant={range === d ? 'default' : 'outline'} onClick={() => setRange(d)}>
                                {d === 365 ? '1Y' : `${d}D`}
                            </Button>
                        ))}
                    </div>
                </div>

                {/* Summary cards */}
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="pb-2"><CardTitle className="text-sm font-medium">Total orders</CardTitle></CardHeader>
                        <CardContent><div className="text-2xl font-bold">{totals.order_count.toLocaleString()}</div></CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2"><CardTitle className="text-sm font-medium">Gross revenue</CardTitle></CardHeader>
                        <CardContent><div className="text-2xl font-bold">{fmt(totals.revenue)}</div></CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2"><CardTitle className="text-sm font-medium">Discounts given</CardTitle></CardHeader>
                        <CardContent><div className="text-2xl font-bold text-amber-600">{fmt(totals.discounts)}</div></CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2"><CardTitle className="text-sm font-medium">Refunds issued</CardTitle></CardHeader>
                        <CardContent><div className="text-2xl font-bold text-red-600">{fmt(totals.refunds)}</div></CardContent>
                    </Card>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Revenue bar chart */}
                    <Card className="lg:col-span-2">
                        <CardHeader><CardTitle className="text-base">Revenue trend ({range}d)</CardTitle></CardHeader>
                        <CardContent>
                            {trend.length === 0 ? (
                                <p className="text-sm text-muted-foreground text-center py-10">No data for this period yet.</p>
                            ) : (
                                <div className="flex items-end gap-0.5 h-40 w-full overflow-x-auto">
                                    {trend.map(row => (
                                        <div
                                            key={row.date}
                                            title={`${row.date}: ${fmt(row.gross_revenue)} (${row.order_count} orders)`}
                                            className="flex-1 min-w-[4px] bg-primary rounded-t-sm cursor-default"
                                            style={{ height: `${Math.max(4, Math.round((row.gross_revenue / maxRevenue) * 100))}%` }}
                                        />
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Order status breakdown */}
                    <Card>
                        <CardHeader><CardTitle className="text-base">Order status</CardTitle></CardHeader>
                        <CardContent>
                            {statusBreakdown.length === 0 ? (
                                <p className="text-sm text-muted-foreground text-center py-6">No orders in this period.</p>
                            ) : (
                                <div className="space-y-2">
                                    {statusBreakdown.map(row => (
                                        <div key={row.status} className="flex items-center justify-between">
                                            <Badge variant={STATUS_VARIANT[row.status] ?? 'secondary'} className="text-xs capitalize">
                                                {row.status.replace(/_/g, ' ')}
                                            </Badge>
                                            <span className="text-sm font-medium">{row.count}</span>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>

                {/* Top products */}
                <Card>
                    <CardHeader><CardTitle className="text-base">Top products by quantity sold ({range}d)</CardTitle></CardHeader>
                    <CardContent>
                        {topProducts.length === 0 ? (
                            <p className="text-sm text-muted-foreground text-center py-6">No sales data for this period.</p>
                        ) : (
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>#</TableHead>
                                        <TableHead>Product</TableHead>
                                        <TableHead>SKU</TableHead>
                                        <TableHead className="text-right">Qty sold</TableHead>
                                        <TableHead className="text-right">Revenue</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {topProducts.map((p, i) => (
                                        <TableRow key={p.product_id}>
                                            <TableCell className="text-muted-foreground text-sm">{i + 1}</TableCell>
                                            <TableCell className="font-medium text-sm">{p.name}</TableCell>
                                            <TableCell className="font-mono text-xs text-muted-foreground">{p.sku}</TableCell>
                                            <TableCell className="text-right text-sm">{p.total_qty.toLocaleString()}</TableCell>
                                            <TableCell className="text-right text-sm">{fmt(p.total_revenue)}</TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
