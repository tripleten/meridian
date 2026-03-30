import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowLeft, Plus, Truck } from 'lucide-react';

interface ShipmentItem {
    id: string;
    sku: string;
    name: string;
    quantity_shipped: number;
}

interface Shipment {
    id: string;
    order_id: string;
    state: string;
    state_label: string;
    state_badge: string;
    carrier: string | null;
    tracking_number: string | null;
    tracking_url: string | null;
    shipped_at: string | null;
    delivered_at: string | null;
    notes: string | null;
    created_at: string;
    items: ShipmentItem[];
}

interface Props {
    orderId: string;
    shipments: Shipment[];
}

const BADGE: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    default:     'default',
    secondary:   'secondary',
    destructive: 'destructive',
    outline:     'outline',
};

export default function ShipmentsIndex({ orderId, shipments }: Props) {
    return (
        <AdminLayout>
            <Head title="Shipments" />

            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild>
                    <Link href={`/admin/orders/${orderId}`}><ArrowLeft className="h-4 w-4" /></Link>
                </Button>
                <h1 className="text-2xl font-semibold">Shipments</h1>
                <div className="ml-auto">
                    <Button asChild>
                        <Link href={`/admin/orders/${orderId}/shipments/create`}>
                            <Plus className="h-4 w-4 mr-1" /> New Shipment
                        </Link>
                    </Button>
                </div>
            </div>

            {shipments.length === 0 ? (
                <div className="text-center py-20 text-muted-foreground">
                    <Truck className="h-12 w-12 mx-auto mb-3 opacity-30" />
                    <p>No shipments yet.</p>
                </div>
            ) : (
                <div className="space-y-4">
                    {shipments.map(s => (
                        <Card key={s.id}>
                            <CardHeader>
                                <div className="flex items-center justify-between">
                                    <CardTitle className="text-base font-medium flex items-center gap-2">
                                        <Truck className="h-4 w-4" />
                                        Shipment
                                        <Badge variant={BADGE[s.state_badge] ?? 'secondary'}>{s.state_label}</Badge>
                                    </CardTitle>
                                    <Button variant="outline" size="sm" asChild>
                                        <Link href={`/admin/orders/${orderId}/shipments/${s.id}/edit`}>
                                            Edit
                                        </Link>
                                    </Button>
                                </div>
                            </CardHeader>
                            <CardContent className="text-sm space-y-3">
                                {(s.carrier || s.tracking_number) && (
                                    <div className="flex gap-6">
                                        {s.carrier && (
                                            <div>
                                                <p className="text-xs text-muted-foreground">Carrier</p>
                                                <p>{s.carrier}</p>
                                            </div>
                                        )}
                                        {s.tracking_number && (
                                            <div>
                                                <p className="text-xs text-muted-foreground">Tracking</p>
                                                {s.tracking_url ? (
                                                    <a href={s.tracking_url} target="_blank" rel="noreferrer" className="underline text-primary">
                                                        {s.tracking_number}
                                                    </a>
                                                ) : <p>{s.tracking_number}</p>}
                                            </div>
                                        )}
                                        {s.shipped_at && (
                                            <div>
                                                <p className="text-xs text-muted-foreground">Shipped</p>
                                                <p>{new Date(s.shipped_at).toLocaleDateString()}</p>
                                            </div>
                                        )}
                                    </div>
                                )}
                                <table className="w-full text-sm">
                                    <thead>
                                        <tr className="border-b">
                                            <th className="text-left py-1">SKU</th>
                                            <th className="text-left py-1">Product</th>
                                            <th className="text-right py-1">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {s.items.map(item => (
                                            <tr key={item.id} className="border-b last:border-0">
                                                <td className="py-1 font-mono text-xs">{item.sku}</td>
                                                <td className="py-1">{item.name}</td>
                                                <td className="py-1 text-right">{item.quantity_shipped}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                                {s.notes && <p className="text-muted-foreground italic">{s.notes}</p>}
                            </CardContent>
                        </Card>
                    ))}
                </div>
            )}
        </AdminLayout>
    );
}
