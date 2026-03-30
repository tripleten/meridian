import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft } from 'lucide-react';

export default function GiftCardCreate() {
    const { data, setData, post, processing, errors } = useForm({
        code: '', initial_balance: '', currency_code: 'GBP',
        customer_id: '', expires_at: '',
    });

    return (
        <AdminLayout>
            <Head title="New Gift Card" />
            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild><Link href="/admin/promotions/gift-cards"><ArrowLeft className="h-4 w-4" /></Link></Button>
                <h1 className="text-2xl font-semibold">New Gift Card</h1>
            </div>
            <form onSubmit={e => { e.preventDefault(); post('/admin/promotions/gift-cards'); }} className="max-w-md space-y-4">
                <div className="space-y-1">
                    <Label>Code</Label>
                    <Input value={data.code} onChange={e => setData('code', e.target.value.toUpperCase())} placeholder="Leave blank to auto-generate" />
                    {errors.code && <p className="text-sm text-destructive">{errors.code}</p>}
                </div>
                <div className="space-y-1">
                    <Label>Initial Balance (pence/cents) *</Label>
                    <Input type="number" min="1" value={data.initial_balance} onChange={e => setData('initial_balance', e.target.value)} placeholder="e.g. 5000 = £50.00" required />
                    {errors.initial_balance && <p className="text-sm text-destructive">{errors.initial_balance}</p>}
                </div>
                <div className="space-y-1">
                    <Label>Currency *</Label>
                    <Input value={data.currency_code} onChange={e => setData('currency_code', e.target.value.toUpperCase())} maxLength={3} className="w-24" required />
                </div>
                <div className="space-y-1">
                    <Label>Expires At</Label>
                    <Input type="date" value={data.expires_at} onChange={e => setData('expires_at', e.target.value)} />
                </div>
                <div className="flex gap-3 pt-2">
                    <Button type="submit" disabled={processing}>Create Gift Card</Button>
                    <Button variant="outline" asChild><Link href="/admin/promotions/gift-cards">Cancel</Link></Button>
                </div>
            </form>
        </AdminLayout>
    );
}
