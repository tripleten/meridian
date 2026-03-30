import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Eye } from 'lucide-react';
import { useCallback, useState } from 'react';

interface Order {
    id: string;
    number: string;
    status: string;
    status_label: string;
    status_badge: string;
    payment_status: string;
    payment_status_label: string;
    customer_email: string;
    customer_name: string | null;
    grand_total: number;
    currency_code: string;
    items_count: number;
    placed_at: string | null;
    created_at: string;
}

interface PaginatedOrders {
    data: Order[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

interface StatusOption {
    value: string;
    label: string;
}

interface Props {
    orders: PaginatedOrders;
    filters: { search?: string; status?: string; payment_status?: string };
    statusOptions: StatusOption[];
    paymentStatusOptions: StatusOption[];
}

const BADGE: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    default:     'default',
    secondary:   'secondary',
    destructive: 'destructive',
    outline:     'outline',
};

function formatMoney(amount: number, currency: string): string {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(amount / 100);
}

export default function OrdersIndex({ orders, filters, statusOptions, paymentStatusOptions }: Props) {
    const [search, setSearch]               = useState(filters.search ?? '');
    const [status, setStatus]               = useState(filters.status ?? '');
    const [paymentStatus, setPaymentStatus] = useState(filters.payment_status ?? '');

    const applyFilters = useCallback(() => {
        const params: Record<string, string> = {};
        if (search)        params.search         = search;
        if (status)        params.status         = status;
        if (paymentStatus) params.payment_status = paymentStatus;
        router.get('/admin/orders', params, { preserveState: true, replace: true });
    }, [search, status, paymentStatus]);

    return (
        <AdminLayout>
            <Head title="Orders" />

            <div className="flex items-center justify-between mb-6">
                <h1 className="text-2xl font-semibold">Orders</h1>
            </div>

            {/* Filters */}
            <div className="flex flex-wrap gap-3 mb-6">
                <Input
                    placeholder="Search order # or email…"
                    value={search}
                    onChange={e => setSearch(e.target.value)}
                    onKeyDown={e => e.key === 'Enter' && applyFilters()}
                    className="w-64"
                />
                <Select value={status} onValueChange={v => setStatus(v === '_all' ? '' : v)}>
                    <SelectTrigger className="w-44">
                        <SelectValue placeholder="All statuses" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="_all">All statuses</SelectItem>
                        {statusOptions.map(o => (
                            <SelectItem key={o.value} value={o.value}>{o.label}</SelectItem>
                        ))}
                    </SelectContent>
                </Select>
                <Select value={paymentStatus} onValueChange={v => setPaymentStatus(v === '_all' ? '' : v)}>
                    <SelectTrigger className="w-48">
                        <SelectValue placeholder="All payment statuses" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="_all">All payment statuses</SelectItem>
                        {paymentStatusOptions.map(o => (
                            <SelectItem key={o.value} value={o.value}>{o.label}</SelectItem>
                        ))}
                    </SelectContent>
                </Select>
                <Button onClick={applyFilters}>Filter</Button>
            </div>

            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Order #</TableHead>
                            <TableHead>Customer</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Payment</TableHead>
                            <TableHead className="text-right">Total</TableHead>
                            <TableHead>Items</TableHead>
                            <TableHead>Placed</TableHead>
                            <TableHead className="w-14" />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {orders.data.length === 0 ? (
                            <TableRow>
                                <TableCell colSpan={8} className="text-center py-10 text-muted-foreground">
                                    No orders found.
                                </TableCell>
                            </TableRow>
                        ) : orders.data.map(order => (
                            <TableRow key={order.id}>
                                <TableCell className="font-mono font-medium">{order.number}</TableCell>
                                <TableCell>
                                    <div>{order.customer_name ?? '—'}</div>
                                    <div className="text-xs text-muted-foreground">{order.customer_email}</div>
                                </TableCell>
                                <TableCell>
                                    <Badge variant={BADGE[order.status_badge] ?? 'secondary'}>
                                        {order.status_label}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge variant="outline">{order.payment_status_label}</Badge>
                                </TableCell>
                                <TableCell className="text-right font-medium">
                                    {formatMoney(order.grand_total, order.currency_code)}
                                </TableCell>
                                <TableCell>{order.items_count}</TableCell>
                                <TableCell className="text-sm text-muted-foreground">
                                    {order.placed_at
                                        ? new Date(order.placed_at).toLocaleDateString()
                                        : new Date(order.created_at).toLocaleDateString()}
                                </TableCell>
                                <TableCell>
                                    <Button variant="ghost" size="icon" asChild>
                                        <Link href={`/admin/orders/${order.id}`}>
                                            <Eye className="h-4 w-4" />
                                        </Link>
                                    </Button>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>

            {/* Pagination */}
            {orders.last_page > 1 && (
                <div className="flex items-center gap-1 mt-4">
                    {orders.links.map((link, i) => (
                        <Button
                            key={i}
                            variant={link.active ? 'default' : 'outline'}
                            size="sm"
                            disabled={!link.url}
                            onClick={() => link.url && router.visit(link.url)}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    ))}
                </div>
            )}
        </AdminLayout>
    );
}
