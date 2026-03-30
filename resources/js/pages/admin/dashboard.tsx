import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ShoppingCart, Package, Users, TrendingUp, AlertTriangle } from 'lucide-react';
import type { PageProps } from '@inertiajs/core';

interface Stats {
    products_total: number; products_active: number;
    orders_today: number; orders_this_month: number; orders_last_month: number;
    revenue_today: number; revenue_this_month: number; revenue_last_month: number;
    customers_total: number; customers_new_month: number;
}
interface RecentOrder { id: string; order_number: string; status: string; grand_total: number; created_at: string; }
interface LowStockItem { id: string; name: string; sku: string; qty_available: number; }
interface Props extends PageProps { stats: Stats; recentOrders: RecentOrder[]; lowStock: LowStockItem[]; }

function fmt(cents: number): string {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(cents / 100);
}

function pct(current: number, previous: number): { value: number; positive: boolean } | null {
    if (previous === 0) return null;
    const v = Math.round(((current - previous) / previous) * 100);
    return { value: Math.abs(v), positive: v >= 0 };
}

const ORDER_STATUS_VARIANT: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    processing: 'default', completed: 'default',
    pending_payment: 'secondary', on_hold: 'secondary', shipped: 'secondary',
    cancelled: 'destructive', payment_failed: 'destructive',
    refunded: 'outline',
};

export default function AdminDashboard({ stats, recentOrders, lowStock }: Props) {
    const revChange = pct(stats.revenue_this_month, stats.revenue_last_month);
    const ordChange = pct(stats.orders_this_month, stats.orders_last_month);

    return (
        <AdminLayout>
            <Head title="Dashboard" />
            <div className="flex flex-col gap-6 p-6">
                <h1 className="text-2xl font-bold tracking-tight">Dashboard</h1>

                {/* Stat cards */}
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Revenue this month</CardTitle>
                            <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{fmt(stats.revenue_this_month)}</div>
                            {revChange && (
                                <p className={`text-xs mt-1 ${revChange.positive ? 'text-green-600' : 'text-red-600'}`}>
                                    {revChange.positive ? '+' : '-'}{revChange.value}% vs last month
                                </p>
                            )}
                            <p className="text-xs text-muted-foreground mt-0.5">Today: {fmt(stats.revenue_today)}</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Orders this month</CardTitle>
                            <ShoppingCart className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.orders_this_month}</div>
                            {ordChange && (
                                <p className={`text-xs mt-1 ${ordChange.positive ? 'text-green-600' : 'text-red-600'}`}>
                                    {ordChange.positive ? '+' : '-'}{ordChange.value}% vs last month
                                </p>
                            )}
                            <p className="text-xs text-muted-foreground mt-0.5">Today: {stats.orders_today}</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Products</CardTitle>
                            <Package className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.products_active}</div>
                            <p className="text-xs text-muted-foreground mt-1">Active of {stats.products_total} total</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Customers</CardTitle>
                            <Users className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.customers_total}</div>
                            <p className="text-xs text-muted-foreground mt-1">+{stats.customers_new_month} this month</p>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Recent orders */}
                    <Card className="lg:col-span-2">
                        <CardHeader className="flex flex-row items-center justify-between space-y-0">
                            <CardTitle className="text-base">Recent orders</CardTitle>
                            <Link href="/admin/orders" className="text-xs text-muted-foreground hover:underline">View all</Link>
                        </CardHeader>
                        <CardContent>
                            {recentOrders.length === 0 ? (
                                <p className="text-sm text-muted-foreground text-center py-6">No orders yet.</p>
                            ) : (
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Order</TableHead>
                                            <TableHead>Status</TableHead>
                                            <TableHead className="text-right">Total</TableHead>
                                            <TableHead>Date</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {recentOrders.map(o => (
                                            <TableRow key={o.id}>
                                                <TableCell>
                                                    <Link href={`/admin/orders/${o.id}`} className="font-mono text-sm hover:underline">
                                                        {o.order_number}
                                                    </Link>
                                                </TableCell>
                                                <TableCell>
                                                    <Badge variant={ORDER_STATUS_VARIANT[o.status] ?? 'secondary'} className="text-xs">
                                                        {o.status.replace(/_/g, ' ')}
                                                    </Badge>
                                                </TableCell>
                                                <TableCell className="text-right text-sm">{fmt(o.grand_total)}</TableCell>
                                                <TableCell className="text-xs text-muted-foreground">
                                                    {new Date(o.created_at).toLocaleDateString()}
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            )}
                        </CardContent>
                    </Card>

                    {/* Low stock */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0">
                            <CardTitle className="text-base">Low stock</CardTitle>
                            <AlertTriangle className="h-4 w-4 text-amber-500" />
                        </CardHeader>
                        <CardContent>
                            {lowStock.length === 0 ? (
                                <p className="text-sm text-muted-foreground text-center py-6">All stock levels healthy.</p>
                            ) : (
                                <div className="space-y-3">
                                    {lowStock.map(item => (
                                        <div key={item.id} className="flex items-center justify-between">
                                            <div>
                                                <Link href={`/admin/products/${item.id}/edit`} className="text-sm font-medium hover:underline line-clamp-1">
                                                    {item.name}
                                                </Link>
                                                <p className="text-xs text-muted-foreground font-mono">{item.sku}</p>
                                            </div>
                                            <Badge variant={item.qty_available <= 0 ? 'destructive' : 'secondary'} className="ml-2 shrink-0">
                                                {item.qty_available} left
                                            </Badge>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AdminLayout>
    );
}
