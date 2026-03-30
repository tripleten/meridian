import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft } from 'lucide-react';

function slugify(v: string) { return v.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, ''); }

export default function TaxZoneCreate() {
    const { data, setData, post, processing, errors } = useForm({ name: '', code: '', countries: '' });

    function handleName(v: string) { setData(p => ({ ...p, name: v, code: slugify(v) })); }

    return (
        <AdminLayout>
            <Head title="New Tax Zone" />
            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild><Link href="/admin/tax/zones"><ArrowLeft className="h-4 w-4" /></Link></Button>
                <h1 className="text-2xl font-semibold">New Tax Zone</h1>
            </div>
            <form onSubmit={e => { e.preventDefault(); post('/admin/tax/zones'); }} className="max-w-md space-y-4">
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
                    <Label>Countries *</Label>
                    <Input value={data.countries} onChange={e => setData('countries', e.target.value)} placeholder="GB, US, CA" required />
                    <p className="text-xs text-muted-foreground">Comma-separated ISO 3166-1 alpha-2 codes.</p>
                    {errors.countries && <p className="text-sm text-destructive">{errors.countries as unknown as string}</p>}
                </div>
                <div className="flex gap-3 pt-2">
                    <Button type="submit" disabled={processing}>Create</Button>
                    <Button variant="outline" asChild><Link href="/admin/tax/zones">Cancel</Link></Button>
                </div>
            </form>
        </AdminLayout>
    );
}
