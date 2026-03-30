import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { ArrowLeft, Package, Truck } from 'lucide-react';

interface OrderDetail {
    id: string;
    number: string;
    status: string;
    status_label: string;
    status_badge: string;
    payment_status: string;
    payment_status_label: string;
    customer_email: string;
    customer_name: string | null;
    customer_id: string | null;
    billing_address: Record<string, string> | null;
    shipping_address: Record<string, string> | null;
    subtotal: number;
    discount_amount: number;
    shipping_amount: number;
    tax_amount: number;
    grand_total: number;
    currency_code: string;
    coupon_code: string | null;
    notes: string | null;
    placed_at: string | null;
    created_at: string;
}

interface OrderItem {
    id: string;
    sku: string;
    name: string;
    quantity: number;
    unit_price: number;
    unit_price_incl_tax: number;
    row_total: number;
    row_total_incl_tax: number;
    tax_amount: number;
    discount_amount: number;
    tax_rate: number;
}

interface OrderComment {
    id: string;
    author_type: string;
    author_id: string | null;
    comment: string;
    is_customer_notified: boolean;
    is_visible_to_customer: boolean;
    created_at: string;
}

interface StatusOption {
    value: string;
    label: string;
}

interface Props {
    order: OrderDetail;
    items: OrderItem[];
    comments: OrderComment[];
    allowedTransitions: StatusOption[];
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

function AddressCard({ title, address }: { title: string; address: Record<string, string> | null }) {
    if (!address) return null;
    return (
        <div>
            <p className="text-sm font-medium text-muted-foreground mb-1">{title}</p>
            {Object.entries(address).filter(([, v]) => v).map(([k, v]) => (
                <p key={k} className="text-sm">{v}</p>
            ))}
        </div>
    );
}

export default function OrderShow({ order, items, comments, allowedTransitions }: Props) {
    const statusForm = useForm({
        new_status:      '',
        comment:         '',
        notify_customer: false as boolean,
    });

    const commentForm = useForm({
        comment:                '',
        is_customer_notified:   false as boolean,
        is_visible_to_customer: true as boolean,
    });

    function submitStatus(e: React.FormEvent) {
        e.preventDefault();
        statusForm.put(`/admin/orders/${order.id}/status`);
    }

    function submitComment(e: React.FormEvent) {
        e.preventDefault();
        commentForm.post(`/admin/orders/${order.id}/comments`, {
            onSuccess: () => commentForm.reset(),
        });
    }

    return (
        <AdminLayout>
            <Head title={`Order ${order.number}`} />

            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild>
                    <Link href="/admin/orders"><ArrowLeft className="h-4 w-4" /></Link>
                </Button>
                <h1 className="text-2xl font-semibold">Order {order.number}</h1>
                <Badge variant={BADGE[order.status_badge] ?? 'secondary'}>{order.status_label}</Badge>
                <Badge variant="outline">{order.payment_status_label}</Badge>
                <div className="ml-auto flex gap-2">
                    <Button variant="outline" size="sm" asChild>
                        <Link href={`/admin/orders/${order.id}/shipments`}>
                            <Truck className="h-4 w-4 mr-1" /> Shipments
                        </Link>
                    </Button>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Left column */}
                <div className="lg:col-span-2 space-y-6">
                    {/* Items */}
                    <Card>
                        <CardHeader><CardTitle className="flex items-center gap-2"><Package className="h-4 w-4" /> Items</CardTitle></CardHeader>
                        <CardContent className="p-0">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b bg-muted/30">
                                        <th className="text-left px-4 py-2">Product</th>
                                        <th className="text-right px-4 py-2">Unit</th>
                                        <th className="text-right px-4 py-2">Qty</th>
                                        <th className="text-right px-4 py-2">Row Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {items.map(item => (
                                        <tr key={item.id} className="border-b last:border-0">
                                            <td className="px-4 py-2">
                                                <div className="font-medium">{item.name}</div>
                                                <div className="text-xs text-muted-foreground">{item.sku}</div>
                                            </td>
                                            <td className="px-4 py-2 text-right">{formatMoney(item.unit_price_incl_tax, order.currency_code)}</td>
                                            <td className="px-4 py-2 text-right">{item.quantity}</td>
                                            <td className="px-4 py-2 text-right font-medium">{formatMoney(item.row_total_incl_tax, order.currency_code)}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </CardContent>
                    </Card>

                    {/* Totals */}
                    <Card>
                        <CardContent className="pt-4">
                            <div className="space-y-1 text-sm max-w-xs ml-auto">
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Subtotal</span>
                                    <span>{formatMoney(order.subtotal, order.currency_code)}</span>
                                </div>
                                {order.discount_amount > 0 && (
                                    <div className="flex justify-between text-green-600">
                                        <span>Discount{order.coupon_code ? ` (${order.coupon_code})` : ''}</span>
                                        <span>-{formatMoney(order.discount_amount, order.currency_code)}</span>
                                    </div>
                                )}
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Shipping</span>
                                    <span>{formatMoney(order.shipping_amount, order.currency_code)}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Tax</span>
                                    <span>{formatMoney(order.tax_amount, order.currency_code)}</span>
                                </div>
                                <Separator className="my-1" />
                                <div className="flex justify-between font-semibold text-base">
                                    <span>Grand Total</span>
                                    <span>{formatMoney(order.grand_total, order.currency_code)}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Comments */}
                    <Card>
                        <CardHeader><CardTitle>Comments &amp; History</CardTitle></CardHeader>
                        <CardContent className="space-y-4">
                            {comments.length === 0 && (
                                <p className="text-sm text-muted-foreground">No comments yet.</p>
                            )}
                            {comments.map(c => (
                                <div key={c.id} className="border rounded p-3 text-sm">
                                    <div className="flex justify-between text-xs text-muted-foreground mb-1">
                                        <span>{c.author_type === 'admin' ? 'Admin' : 'System'}</span>
                                        <span>{new Date(c.created_at).toLocaleString()}</span>
                                    </div>
                                    <p>{c.comment}</p>
                                    {c.is_customer_notified && (
                                        <Badge variant="outline" className="mt-1 text-xs">Customer notified</Badge>
                                    )}
                                </div>
                            ))}

                            <Separator />

                            <form onSubmit={submitComment} className="space-y-3">
                                <Label>Add comment</Label>
                                <Textarea
                                    value={commentForm.data.comment}
                                    onChange={e => commentForm.setData('comment', e.target.value)}
                                    placeholder="Write a comment…"
                                    rows={3}
                                />
                                {commentForm.errors.comment && (
                                    <p className="text-sm text-destructive">{commentForm.errors.comment}</p>
                                )}
                                <div className="flex flex-wrap gap-4 text-sm">
                                    <label className="flex items-center gap-2 cursor-pointer">
                                        <Checkbox
                                            checked={commentForm.data.is_customer_notified}
                                            onCheckedChange={v => commentForm.setData('is_customer_notified', !!v)}
                                        />
                                        Notify customer
                                    </label>
                                    <label className="flex items-center gap-2 cursor-pointer">
                                        <Checkbox
                                            checked={commentForm.data.is_visible_to_customer}
                                            onCheckedChange={v => commentForm.setData('is_visible_to_customer', !!v)}
                                        />
                                        Visible to customer
                                    </label>
                                </div>
                                <Button type="submit" size="sm" disabled={commentForm.processing}>
                                    Add Comment
                                </Button>
                            </form>
                        </CardContent>
                    </Card>
                </div>

                {/* Right column */}
                <div className="space-y-6">
                    {/* Change status */}
                    {allowedTransitions.length > 0 && (
                        <Card>
                            <CardHeader><CardTitle>Change Status</CardTitle></CardHeader>
                            <CardContent>
                                <form onSubmit={submitStatus} className="space-y-3">
                                    <Select
                                        value={statusForm.data.new_status}
                                        onValueChange={v => statusForm.setData('new_status', v)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select new status" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {allowedTransitions.map(t => (
                                                <SelectItem key={t.value} value={t.value}>{t.label}</SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {statusForm.errors.new_status && (
                                        <p className="text-sm text-destructive">{statusForm.errors.new_status}</p>
                                    )}
                                    <Textarea
                                        value={statusForm.data.comment}
                                        onChange={e => statusForm.setData('comment', e.target.value)}
                                        placeholder="Optional comment…"
                                        rows={2}
                                    />
                                    <label className="flex items-center gap-2 text-sm cursor-pointer">
                                        <Checkbox
                                            checked={statusForm.data.notify_customer}
                                            onCheckedChange={v => statusForm.setData('notify_customer', !!v)}
                                        />
                                        Notify customer
                                    </label>
                                    <Button
                                        type="submit"
                                        className="w-full"
                                        disabled={statusForm.processing || !statusForm.data.new_status}
                                    >
                                        Update Status
                                    </Button>
                                </form>
                            </CardContent>
                        </Card>
                    )}

                    {/* Customer */}
                    <Card>
                        <CardHeader><CardTitle>Customer</CardTitle></CardHeader>
                        <CardContent className="text-sm space-y-1">
                            {order.customer_name && <p className="font-medium">{order.customer_name}</p>}
                            <p>{order.customer_email}</p>
                            {order.customer_id && (
                                <Button variant="link" className="h-auto p-0 text-xs" asChild>
                                    <Link href={`/admin/customers/${order.customer_id}`}>View customer</Link>
                                </Button>
                            )}
                        </CardContent>
                    </Card>

                    {/* Addresses */}
                    <Card>
                        <CardHeader><CardTitle>Addresses</CardTitle></CardHeader>
                        <CardContent className="space-y-4">
                            <AddressCard title="Billing" address={order.billing_address} />
                            <AddressCard title="Shipping" address={order.shipping_address} />
                        </CardContent>
                    </Card>

                    {/* Order info */}
                    <Card>
                        <CardHeader><CardTitle>Order Info</CardTitle></CardHeader>
                        <CardContent className="text-sm space-y-2">
                            <div className="flex justify-between">
                                <span className="text-muted-foreground">Order #</span>
                                <span className="font-mono">{order.number}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-muted-foreground">Placed</span>
                                <span>{order.placed_at
                                    ? new Date(order.placed_at).toLocaleString()
                                    : new Date(order.created_at).toLocaleString()}</span>
                            </div>
                            {order.notes && (
                                <div className="pt-2 border-t">
                                    <p className="text-muted-foreground text-xs mb-1">Notes</p>
                                    <p>{order.notes}</p>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AdminLayout>
    );
}
