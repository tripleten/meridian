import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { ArrowLeft } from 'lucide-react';

interface OrderItem {
    id: string;
    sku: string;
    name: string;
    quantity: number;
    quantity_refunded: number;
}

interface Props {
    orderId: string;
    orderItems: OrderItem[];
}

export default function ShipmentCreate({ orderId, orderItems }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        carrier:         '',
        tracking_number: '',
        tracking_url:    '',
        notes:           '',
        items: orderItems.map(i => ({
            order_item_id:    i.id,
            sku:              i.sku,
            name:             i.name,
            quantity_shipped: i.quantity - i.quantity_refunded,
            include:          true,
        })),
    });

    type ItemField = 'include' | 'quantity_shipped';

    function updateItem(idx: number, field: ItemField, value: boolean | number) {
        const items = data.items.map((item, i) =>
            i === idx ? { ...item, [field]: value } : item
        );
        setData('items', items);
    }

    function submit(e: React.FormEvent) {
        e.preventDefault();
        const filtered = data.items
            .filter(i => i.include && i.quantity_shipped > 0)
            .map(({ order_item_id, sku, name, quantity_shipped }) => ({
                order_item_id, sku, name, quantity_shipped,
            }));
        post(`/admin/orders/${orderId}/shipments`, {
            data: { ...data, items: filtered },
        } as never);
    }

    return (
        <AdminLayout>
            <Head title="Create Shipment" />

            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild>
                    <Link href={`/admin/orders/${orderId}/shipments`}><ArrowLeft className="h-4 w-4" /></Link>
                </Button>
                <h1 className="text-2xl font-semibold">Create Shipment</h1>
            </div>

            <form onSubmit={submit} className="max-w-2xl space-y-6">
                {/* Items */}
                <div className="space-y-2">
                    <Label>Items to Ship</Label>
                    {typeof errors.items === 'string' && (
                        <p className="text-sm text-destructive">{errors.items}</p>
                    )}
                    <div className="border rounded-md divide-y">
                        {data.items.map((item, idx) => (
                            <div key={item.order_item_id} className="flex items-center gap-4 p-3">
                                <input
                                    type="checkbox"
                                    checked={item.include}
                                    onChange={e => updateItem(idx, 'include', e.target.checked)}
                                    className="h-4 w-4"
                                />
                                <div className="flex-1">
                                    <p className="font-medium text-sm">{item.name}</p>
                                    <p className="text-xs text-muted-foreground font-mono">{item.sku}</p>
                                </div>
                                <Input
                                    type="number"
                                    min={1}
                                    max={orderItems[idx].quantity - orderItems[idx].quantity_refunded}
                                    value={item.quantity_shipped}
                                    onChange={e => updateItem(idx, 'quantity_shipped', parseInt(e.target.value, 10) || 1)}
                                    disabled={!item.include}
                                    className="w-20"
                                />
                            </div>
                        ))}
                    </div>
                </div>

                {/* Carrier info */}
                <div className="grid grid-cols-2 gap-4">
                    <div className="space-y-1">
                        <Label>Carrier</Label>
                        <Input value={data.carrier} onChange={e => setData('carrier', e.target.value)} placeholder="e.g. FedEx" />
                    </div>
                    <div className="space-y-1">
                        <Label>Tracking Number</Label>
                        <Input value={data.tracking_number} onChange={e => setData('tracking_number', e.target.value)} />
                    </div>
                </div>

                <div className="space-y-1">
                    <Label>Tracking URL</Label>
                    <Input value={data.tracking_url} onChange={e => setData('tracking_url', e.target.value)} placeholder="https://…" />
                </div>

                <div className="space-y-1">
                    <Label>Notes</Label>
                    <Textarea value={data.notes} onChange={e => setData('notes', e.target.value)} rows={2} />
                </div>

                <div className="flex gap-3 pt-2">
                    <Button type="submit" disabled={processing}>Create Shipment</Button>
                    <Button variant="outline" asChild>
                        <Link href={`/admin/orders/${orderId}/shipments`}>Cancel</Link>
                    </Button>
                </div>
            </form>
        </AdminLayout>
    );
}
