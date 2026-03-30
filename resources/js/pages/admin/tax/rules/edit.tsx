import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { ArrowLeft } from 'lucide-react';

interface TaxRule { id: string; name: string; priority: number; is_active: boolean; tax_class_ids: string[]; tax_zone_ids: string[]; tax_rate_ids: string[]; }
interface Option { id: string; name: string; }
interface Props { rule: TaxRule; classes: Option[]; zones: Option[]; rates: Option[]; }

export default function TaxRuleEdit({ rule, classes, zones, rates }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        name: rule.name, priority: rule.priority, is_active: rule.is_active,
        tax_class_ids: rule.tax_class_ids,
        tax_zone_ids:  rule.tax_zone_ids,
        tax_rate_ids:  rule.tax_rate_ids,
    });

    function toggle(field: 'tax_class_ids' | 'tax_zone_ids' | 'tax_rate_ids', id: string) {
        const arr = data[field];
        setData(field, arr.includes(id) ? arr.filter(x => x !== id) : [...arr, id]);
    }

    function MultiSelect({ label, field, options }: { label: string; field: 'tax_class_ids' | 'tax_zone_ids' | 'tax_rate_ids'; options: Option[] }) {
        return (
            <div className="space-y-1">
                <Label>{label}</Label>
                <div className="border rounded-md divide-y max-h-48 overflow-y-auto">
                    {options.map(o => (
                        <label key={o.id} className="flex items-center gap-3 px-3 py-2 cursor-pointer hover:bg-muted/50">
                            <input type="checkbox" checked={data[field].includes(o.id)} onChange={() => toggle(field, o.id)} className="h-4 w-4" />
                            <span className="text-sm">{o.name}</span>
                        </label>
                    ))}
                    {options.length === 0 && <p className="px-3 py-2 text-sm text-muted-foreground">None available.</p>}
                </div>
            </div>
        );
    }

    return (
        <AdminLayout>
            <Head title={`Edit ${rule.name}`} />
            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild><Link href="/admin/tax/rules"><ArrowLeft className="h-4 w-4" /></Link></Button>
                <h1 className="text-2xl font-semibold">Edit Tax Rule</h1>
            </div>
            <form onSubmit={e => { e.preventDefault(); put(`/admin/tax/rules/${rule.id}`); }} className="max-w-lg space-y-5">
                <div className="space-y-1">
                    <Label>Name *</Label>
                    <Input value={data.name} onChange={e => setData('name', e.target.value)} required />
                    {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
                </div>
                <div className="space-y-1">
                    <Label>Priority</Label>
                    <Input type="number" value={data.priority} onChange={e => setData('priority', parseInt(e.target.value, 10) || 0)} className="w-28" />
                </div>
                <MultiSelect label="Tax Classes" field="tax_class_ids" options={classes} />
                <MultiSelect label="Tax Zones" field="tax_zone_ids" options={zones} />
                <MultiSelect label="Tax Rates" field="tax_rate_ids" options={rates} />
                <div className="flex items-center gap-3">
                    <Switch checked={data.is_active} onCheckedChange={v => setData('is_active', v)} id="is_active" />
                    <Label htmlFor="is_active">Active</Label>
                </div>
                <div className="flex gap-3 pt-2">
                    <Button type="submit" disabled={processing}>Save Changes</Button>
                    <Button variant="outline" asChild><Link href="/admin/tax/rules">Cancel</Link></Button>
                </div>
            </form>
        </AdminLayout>
    );
}
