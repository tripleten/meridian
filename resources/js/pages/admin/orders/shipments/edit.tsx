import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft } from 'lucide-react';

interface Shipment {
    id: string;
    order_id: string;
    state: string;
    state_label: string;
    carrier: string | null;
    tracking_number: string | null;
    tracking_url: string | null;
    notes: string | null;
}

interface StatusOption {
    value: string;
    label: string;
}

interface Props {
    orderId: string;
    shipment: Shipment;
    allowedTransitions: StatusOption[];
}

export default function ShipmentEdit({ orderId, shipment, allowedTransitions }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        state:           shipment.state,
        carrier:         shipment.carrier ?? '',
        tracking_number: shipment.tracking_number ?? '',
        tracking_url:    shipment.tracking_url ?? '',
        notes:           shipment.notes ?? '',
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        put(`/admin/orders/${orderId}/shipments/${shipment.id}`);
    }

    return (
        <AdminLayout>
            <Head title="Edit Shipment" />

            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild>
                    <Link href={`/admin/orders/${orderId}/shipments`}><ArrowLeft className="h-4 w-4" /></Link>
                </Button>
                <h1 className="text-2xl font-semibold">Edit Shipment</h1>
            </div>

            <form onSubmit={submit} className="max-w-xl space-y-5">
                {allowedTransitions.length > 0 && (
                    <div className="space-y-1">
                        <Label>Change State</Label>
                        <Select value={data.state} onValueChange={v => setData('state', v)}>
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value={shipment.state}>{shipment.state_label} (current)</SelectItem>
                                {allowedTransitions.map(t => (
                                    <SelectItem key={t.value} value={t.value}>{t.label}</SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        {errors.state && <p className="text-sm text-destructive">{errors.state}</p>}
                    </div>
                )}

                <div className="grid grid-cols-2 gap-4">
                    <div className="space-y-1">
                        <Label>Carrier</Label>
                        <Input value={data.carrier} onChange={e => setData('carrier', e.target.value)} />
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
                    <Textarea value={data.notes} onChange={e => setData('notes', e.target.value)} rows={3} />
                </div>

                <div className="flex gap-3 pt-2">
                    <Button type="submit" disabled={processing}>Save Changes</Button>
                    <Button variant="outline" asChild>
                        <Link href={`/admin/orders/${orderId}/shipments`}>Cancel</Link>
                    </Button>
                </div>
            </form>
        </AdminLayout>
    );
}
