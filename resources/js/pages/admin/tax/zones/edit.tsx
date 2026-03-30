import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft } from 'lucide-react';

interface TaxZone { id: string; name: string; code: string; countries: string[]; }
interface Props { zone: TaxZone; }

export default function TaxZoneEdit({ zone }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        name: zone.name, code: zone.code, countries: zone.countries.join(', '),
    });

    return (
        <AdminLayout>
            <Head title={`Edit ${zone.name}`} />
            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild><Link href="/admin/tax/zones"><ArrowLeft className="h-4 w-4" /></Link></Button>
                <h1 className="text-2xl font-semibold">Edit Tax Zone</h1>
            </div>
            <form onSubmit={e => { e.preventDefault(); put(`/admin/tax/zones/${zone.id}`); }} className="max-w-md space-y-4">
                <div className="space-y-1">
                    <Label>Name *</Label>
                    <Input value={data.name} onChange={e => setData('name', e.target.value)} required />
                    {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
                </div>
                <div className="space-y-1">
                    <Label>Code</Label>
                    <Input value={data.code} disabled className="bg-muted font-mono" />
                </div>
                <div className="space-y-1">
                    <Label>Countries *</Label>
                    <Input value={data.countries} onChange={e => setData('countries', e.target.value)} required />
                    <p className="text-xs text-muted-foreground">Comma-separated ISO 3166-1 alpha-2 codes.</p>
                </div>
                <div className="flex gap-3 pt-2">
                    <Button type="submit" disabled={processing}>Save Changes</Button>
                    <Button variant="outline" asChild><Link href="/admin/tax/zones">Cancel</Link></Button>
                </div>
            </form>
        </AdminLayout>
    );
}
