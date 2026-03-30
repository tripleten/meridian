import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { ArrowLeft } from 'lucide-react';

interface PaymentMethod {
    id: string;
    code: string;
    name: string;
    description: string | null;
    is_active: boolean;
    sort_order: number;
    config: Record<string, unknown>;
}

interface Props {
    method: PaymentMethod;
}

export default function PaymentMethodEdit({ method }: Props) {
    const { data, setData, put, processing, errors } = useForm<{
        name: string;
        description: string;
        is_active: boolean;
        sort_order: number;
    }>({
        name:        method.name,
        description: method.description ?? '',
        is_active:   method.is_active,
        sort_order:  method.sort_order,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        put(`/admin/payments/methods/${method.id}`);
    }

    return (
        <AdminLayout>
            <Head title={`Edit ${method.name}`} />

            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild>
                    <Link href="/admin/payments/methods"><ArrowLeft className="h-4 w-4" /></Link>
                </Button>
                <h1 className="text-2xl font-semibold">Edit Payment Method</h1>
            </div>

            <form onSubmit={submit} className="max-w-xl space-y-5">
                <div className="space-y-1">
                    <Label>Code</Label>
                    <Input value={method.code} disabled className="bg-muted font-mono" />
                    <p className="text-xs text-muted-foreground">Code is read-only.</p>
                </div>

                <div className="space-y-1">
                    <Label>Name *</Label>
                    <Input value={data.name} onChange={e => setData('name', e.target.value)} required />
                    {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
                </div>

                <div className="space-y-1">
                    <Label>Description</Label>
                    <Textarea
                        value={data.description}
                        onChange={e => setData('description', e.target.value)}
                        rows={3}
                    />
                </div>

                <div className="space-y-1">
                    <Label>Sort Order</Label>
                    <Input
                        type="number"
                        min={0}
                        value={data.sort_order}
                        onChange={e => setData('sort_order', parseInt(e.target.value, 10) || 0)}
                        className="w-28"
                    />
                </div>

                <div className="flex items-center gap-3">
                    <Switch checked={data.is_active} onCheckedChange={v => setData('is_active', v)} id="is_active" />
                    <Label htmlFor="is_active">Active</Label>
                </div>

                <div className="flex gap-3 pt-2">
                    <Button type="submit" disabled={processing}>Save Changes</Button>
                    <Button variant="outline" asChild>
                        <Link href="/admin/payments/methods">Cancel</Link>
                    </Button>
                </div>
            </form>
        </AdminLayout>
    );
}
