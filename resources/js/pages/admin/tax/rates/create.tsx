import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { ArrowLeft } from 'lucide-react';

interface Zone { id: string; name: string; }
interface Props { zones: Zone[]; }

function slugify(v: string) { return v.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, ''); }

export default function TaxRateCreate({ zones }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        tax_zone_id: '', name: '', code: '',
        rate: '', type: 'inclusive',
        is_compound: false as boolean,
        is_shipping_taxable: false as boolean,
    });

    function handleName(v: string) { setData(p => ({ ...p, name: v, code: slugify(v) })); }

    return (
        <AdminLayout>
            <Head title="New Tax Rate" />
            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild><Link href="/admin/tax/rates"><ArrowLeft className="h-4 w-4" /></Link></Button>
                <h1 className="text-2xl font-semibold">New Tax Rate</h1>
            </div>
            <form onSubmit={e => { e.preventDefault(); post('/admin/tax/rates'); }} className="max-w-md space-y-4">
                <div className="space-y-1">
                    <Label>Tax Zone *</Label>
                    <Select value={data.tax_zone_id} onValueChange={v => setData('tax_zone_id', v)}>
                        <SelectTrigger><SelectValue placeholder="Select zone" /></SelectTrigger>
                        <SelectContent>{zones.map(z => <SelectItem key={z.id} value={z.id}>{z.name}</SelectItem>)}</SelectContent>
                    </Select>
                    {errors.tax_zone_id && <p className="text-sm text-destructive">{errors.tax_zone_id}</p>}
                </div>
                <div className="space-y-1">
                    <Label>Name *</Label>
                    <Input value={data.name} onChange={e => handleName(e.target.value)} required />
                    {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
                </div>
                <div className="space-y-1">
                    <Label>Code *</Label>
                    <Input value={data.code} onChange={e => setData('code', e.target.value)} required />
                    {errors.code && <p className="text-sm text-destructive">{errors.code}</p>}
                </div>
                <div className="space-y-1">
                    <Label>Rate (0–1) *</Label>
                    <Input type="number" step="0.0001" min="0" max="1" value={data.rate} onChange={e => setData('rate', e.target.value)} placeholder="0.2000" required />
                    <p className="text-xs text-muted-foreground">e.g. 0.2 = 20%, 0.05 = 5%</p>
                    {errors.rate && <p className="text-sm text-destructive">{errors.rate}</p>}
                </div>
                <div className="space-y-1">
                    <Label>Type</Label>
                    <Select value={data.type} onValueChange={v => setData('type', v)}>
                        <SelectTrigger><SelectValue /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="inclusive">Inclusive (tax in price)</SelectItem>
                            <SelectItem value="exclusive">Exclusive (tax on top)</SelectItem>
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
                    <Button type="submit" disabled={processing}>Create</Button>
                    <Button variant="outline" asChild><Link href="/admin/tax/rates">Cancel</Link></Button>
                </div>
            </form>
        </AdminLayout>
    );
}
