import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { ArrowLeft } from 'lucide-react';

interface TaxRate { id: string; name: string; code: string; tax_zone_id: string; rate: number; type: string; is_compound: boolean; is_shipping_taxable: boolean; }
interface Zone { id: string; name: string; }
interface Props { rate: TaxRate; zones: Zone[]; }

export default function TaxRateEdit({ rate, zones }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        name: rate.name, code: rate.code,
        rate: String(rate.rate), type: rate.type,
        is_compound: rate.is_compound,
        is_shipping_taxable: rate.is_shipping_taxable,
    });

    return (
        <AdminLayout>
            <Head title={`Edit ${rate.name}`} />
            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild><Link href="/admin/tax/rates"><ArrowLeft className="h-4 w-4" /></Link></Button>
                <h1 className="text-2xl font-semibold">Edit Tax Rate</h1>
            </div>
            <form onSubmit={e => { e.preventDefault(); put(`/admin/tax/rates/${rate.id}`); }} className="max-w-md space-y-4">
                <div className="space-y-1">
                    <Label>Tax Zone</Label>
                    <Input value={zones.find(z => z.id === rate.tax_zone_id)?.name ?? rate.tax_zone_id} disabled className="bg-muted" />
                </div>
                <div className="space-y-1">
                    <Label>Name *</Label>
                    <Input value={data.name} onChange={e => setData('name', e.target.value)} required />
                    {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
                </div>
                <div className="space-y-1">
                    <Label>Code *</Label>
                    <Input value={data.code} onChange={e => setData('code', e.target.value)} required />
                    {errors.code && <p className="text-sm text-destructive">{errors.code}</p>}
                </div>
                <div className="space-y-1">
                    <Label>Rate (0–1) *</Label>
                    <Input type="number" step="0.0001" min="0" max="1" value={data.rate} onChange={e => setData('rate', e.target.value)} required />
                    {errors.rate && <p className="text-sm text-destructive">{errors.rate}</p>}
                </div>
                <div className="space-y-1">
                    <Label>Type</Label>
                    <Select value={data.type} onValueChange={v => setData('type', v)}>
                        <SelectTrigger><SelectValue /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="inclusive">Inclusive</SelectItem>
                            <SelectItem value="exclusive">Exclusive</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div className="flex items-center gap-3">
                    <Switch checked={data.is_shipping_taxable} onCheckedChange={v => setData('is_shipping_taxable', v)} id="ship_tax" />
                    <Label htmlFor="ship_tax">Apply to shipping</Label>
                </div>
                <div className="flex items-center gap-3">
                    <Switch checked={data.is_compound} onCheckedChange={v => setData('is_compound', v)} id="compound" />
                    <Label htmlFor="compound">Compound tax</Label>
                </div>
                <div className="flex gap-3 pt-2">
                    <Button type="submit" disabled={processing}>Save Changes</Button>
                    <Button variant="outline" asChild><Link href="/admin/tax/rates">Cancel</Link></Button>
                </div>
            </form>
        </AdminLayout>
    );
}
