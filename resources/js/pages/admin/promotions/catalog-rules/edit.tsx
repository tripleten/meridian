import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { ArrowLeft } from 'lucide-react';

interface Rule { id: string; name: string; discount_type: string; discount_amount: number; priority: number; is_active: boolean; stop_further_rules?: boolean; valid_from: string | null; valid_until: string | null; }
interface TypeOption { value: string; label: string; }
interface Props { rule: Rule; discountTypeOptions: TypeOption[]; }

export default function CatalogRuleEdit({ rule, discountTypeOptions }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        name: rule.name, discount_type: rule.discount_type, discount_amount: String(rule.discount_amount),
        priority: String(rule.priority), is_active: rule.is_active,
        stop_further_rules: rule.stop_further_rules ?? false,
        valid_from: rule.valid_from?.substring(0, 10) ?? '',
        valid_until: rule.valid_until?.substring(0, 10) ?? '',
    });

    return (
        <AdminLayout>
            <Head title={`Edit ${rule.name}`} />
            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild><Link href="/admin/promotions/catalog-rules"><ArrowLeft className="h-4 w-4" /></Link></Button>
                <h1 className="text-2xl font-semibold">Edit Catalog Rule</h1>
            </div>
            <form onSubmit={e => { e.preventDefault(); put(`/admin/promotions/catalog-rules/${rule.id}`); }} className="max-w-md space-y-4">
                <div className="space-y-1"><Label>Name *</Label><Input value={data.name} onChange={e => setData('name', e.target.value)} required /></div>
                <div className="space-y-1">
                    <Label>Discount Type *</Label>
                    <Select value={data.discount_type} onValueChange={v => setData('discount_type', v)}>
                        <SelectTrigger><SelectValue /></SelectTrigger>
                        <SelectContent>{discountTypeOptions.map(t => <SelectItem key={t.value} value={t.value}>{t.label}</SelectItem>)}</SelectContent>
                    </Select>
                </div>
                <div className="space-y-1"><Label>Discount Amount *</Label><Input type="number" step="0.01" min="0" value={data.discount_amount} onChange={e => setData('discount_amount', e.target.value)} required /></div>
                <div className="space-y-1"><Label>Priority</Label><Input type="number" value={data.priority} onChange={e => setData('priority', e.target.value)} className="w-28" /></div>
                <div className="grid grid-cols-2 gap-4">
                    <div className="space-y-1"><Label>Valid From</Label><Input type="date" value={data.valid_from} onChange={e => setData('valid_from', e.target.value)} /></div>
                    <div className="space-y-1"><Label>Valid Until</Label><Input type="date" value={data.valid_until} onChange={e => setData('valid_until', e.target.value)} /></div>
                </div>
                <div className="space-y-2">
                    <div className="flex items-center gap-3"><Switch checked={data.stop_further_rules} onCheckedChange={v => setData('stop_further_rules', v)} id="stop" /><Label htmlFor="stop">Stop further rules</Label></div>
                    <div className="flex items-center gap-3"><Switch checked={data.is_active} onCheckedChange={v => setData('is_active', v)} id="is_active" /><Label htmlFor="is_active">Active</Label></div>
                </div>
                <div className="flex gap-3 pt-2">
                    <Button type="submit" disabled={processing}>Save Changes</Button>
                    <Button variant="outline" asChild><Link href="/admin/promotions/catalog-rules">Cancel</Link></Button>
                </div>
            </form>
        </AdminLayout>
    );
}
