import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { ArrowLeft } from 'lucide-react';

interface TypeOption { value: string; label: string; }
interface Props { discountTypeOptions: TypeOption[]; }

export default function CartRuleCreate({ discountTypeOptions }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        name: '', description: '', discount_type: 'percentage', discount_amount: '',
        apply_to_shipping: false as boolean, stop_rules_processing: false as boolean,
        is_active: true as boolean, valid_from: '', valid_until: '', sort_order: '0',
    });

    return (
        <AdminLayout>
            <Head title="New Cart Rule" />
            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild><Link href="/admin/promotions/cart-rules"><ArrowLeft className="h-4 w-4" /></Link></Button>
                <h1 className="text-2xl font-semibold">New Cart Rule</h1>
            </div>
            <form onSubmit={e => { e.preventDefault(); post('/admin/promotions/cart-rules'); }} className="max-w-md space-y-4">
                <div className="space-y-1">
                    <Label>Name *</Label>
                    <Input value={data.name} onChange={e => setData('name', e.target.value)} required />
                    {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
                </div>
                <div className="space-y-1">
                    <Label>Discount Type *</Label>
                    <Select value={data.discount_type} onValueChange={v => setData('discount_type', v)}>
                        <SelectTrigger><SelectValue /></SelectTrigger>
                        <SelectContent>{discountTypeOptions.map(t => <SelectItem key={t.value} value={t.value}>{t.label}</SelectItem>)}</SelectContent>
                    </Select>
                </div>
                <div className="space-y-1">
                    <Label>Discount Amount *</Label>
                    <Input type="number" step="0.01" min="0" value={data.discount_amount} onChange={e => setData('discount_amount', e.target.value)} required />
                    {errors.discount_amount && <p className="text-sm text-destructive">{errors.discount_amount}</p>}
                </div>
                <div className="grid grid-cols-2 gap-4">
                    <div className="space-y-1"><Label>Valid From</Label><Input type="date" value={data.valid_from} onChange={e => setData('valid_from', e.target.value)} /></div>
                    <div className="space-y-1"><Label>Valid Until</Label><Input type="date" value={data.valid_until} onChange={e => setData('valid_until', e.target.value)} /></div>
                </div>
                <div className="space-y-1">
                    <Label>Sort Order</Label>
                    <Input type="number" value={data.sort_order} onChange={e => setData('sort_order', e.target.value)} className="w-28" />
                </div>
                <div className="space-y-2">
                    <div className="flex items-center gap-3">
                        <Switch checked={data.apply_to_shipping} onCheckedChange={v => setData('apply_to_shipping', v)} id="apply_shipping" />
                        <Label htmlFor="apply_shipping">Apply to shipping</Label>
                    </div>
                    <div className="flex items-center gap-3">
                        <Switch checked={data.stop_rules_processing} onCheckedChange={v => setData('stop_rules_processing', v)} id="stop_rules" />
                        <Label htmlFor="stop_rules">Stop further rules</Label>
                    </div>
                    <div className="flex items-center gap-3">
                        <Switch checked={data.is_active} onCheckedChange={v => setData('is_active', v)} id="is_active" />
                        <Label htmlFor="is_active">Active</Label>
                    </div>
                </div>
                <div className="flex gap-3 pt-2">
                    <Button type="submit" disabled={processing}>Create Rule</Button>
                    <Button variant="outline" asChild><Link href="/admin/promotions/cart-rules">Cancel</Link></Button>
                </div>
            </form>
        </AdminLayout>
    );
}
