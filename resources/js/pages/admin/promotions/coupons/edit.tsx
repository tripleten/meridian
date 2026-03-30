import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { ArrowLeft } from 'lucide-react';

interface Coupon { id: string; code: string; description: string | null; type: string; usage_limit: number | null; usage_limit_per_customer: number | null; is_active: boolean; valid_from: string | null; valid_until: string | null; }
interface TypeOption { value: string; label: string; }
interface Props { coupon: Coupon; typeOptions: TypeOption[]; }

export default function CouponEdit({ coupon, typeOptions }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        code: coupon.code,
        description: coupon.description ?? '',
        type: coupon.type,
        usage_limit: coupon.usage_limit ? String(coupon.usage_limit) : '',
        usage_limit_per_customer: coupon.usage_limit_per_customer ? String(coupon.usage_limit_per_customer) : '',
        is_active: coupon.is_active,
        valid_from: coupon.valid_from?.substring(0, 10) ?? '',
        valid_until: coupon.valid_until?.substring(0, 10) ?? '',
    });

    return (
        <AdminLayout>
            <Head title={`Edit ${coupon.code}`} />
            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild><Link href="/admin/promotions/coupons"><ArrowLeft className="h-4 w-4" /></Link></Button>
                <h1 className="text-2xl font-semibold">Edit Coupon</h1>
            </div>
            <form onSubmit={e => { e.preventDefault(); put(`/admin/promotions/coupons/${coupon.id}`); }} className="max-w-md space-y-4">
                <div className="space-y-1">
                    <Label>Code *</Label>
                    <Input value={data.code} onChange={e => setData('code', e.target.value.toUpperCase())} required />
                    {errors.code && <p className="text-sm text-destructive">{errors.code}</p>}
                </div>
                <div className="space-y-1">
                    <Label>Description</Label>
                    <Input value={data.description} onChange={e => setData('description', e.target.value)} />
                </div>
                <div className="space-y-1">
                    <Label>Type *</Label>
                    <Select value={data.type} onValueChange={v => setData('type', v)}>
                        <SelectTrigger><SelectValue /></SelectTrigger>
                        <SelectContent>{typeOptions.map(t => <SelectItem key={t.value} value={t.value}>{t.label}</SelectItem>)}</SelectContent>
                    </Select>
                </div>
                <div className="grid grid-cols-2 gap-4">
                    <div className="space-y-1"><Label>Usage Limit</Label><Input type="number" min="1" value={data.usage_limit} onChange={e => setData('usage_limit', e.target.value)} placeholder="Unlimited" /></div>
                    <div className="space-y-1"><Label>Per Customer</Label><Input type="number" min="1" value={data.usage_limit_per_customer} onChange={e => setData('usage_limit_per_customer', e.target.value)} placeholder="Unlimited" /></div>
                </div>
                <div className="grid grid-cols-2 gap-4">
                    <div className="space-y-1"><Label>Valid From</Label><Input type="date" value={data.valid_from} onChange={e => setData('valid_from', e.target.value)} /></div>
                    <div className="space-y-1"><Label>Valid Until</Label><Input type="date" value={data.valid_until} onChange={e => setData('valid_until', e.target.value)} /></div>
                </div>
                <div className="flex items-center gap-3">
                    <Switch checked={data.is_active} onCheckedChange={v => setData('is_active', v)} id="is_active" />
                    <Label htmlFor="is_active">Active</Label>
                </div>
                <div className="flex gap-3 pt-2">
                    <Button type="submit" disabled={processing}>Save Changes</Button>
                    <Button variant="outline" asChild><Link href="/admin/promotions/coupons">Cancel</Link></Button>
                </div>
            </form>
        </AdminLayout>
    );
}
