import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft } from 'lucide-react';

interface TaxClass { id: string; name: string; code: string; }
interface Props { taxClass: TaxClass; }

export default function TaxClassEdit({ taxClass }: Props) {
    const { data, setData, put, processing, errors } = useForm({ name: taxClass.name, code: taxClass.code });

    return (
        <AdminLayout>
            <Head title={`Edit ${taxClass.name}`} />
            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild><Link href="/admin/tax/classes"><ArrowLeft className="h-4 w-4" /></Link></Button>
                <h1 className="text-2xl font-semibold">Edit Tax Class</h1>
            </div>
            <form onSubmit={e => { e.preventDefault(); put(`/admin/tax/classes/${taxClass.id}`); }} className="max-w-md space-y-4">
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
                <div className="flex gap-3 pt-2">
                    <Button type="submit" disabled={processing}>Save Changes</Button>
                    <Button variant="outline" asChild><Link href="/admin/tax/classes">Cancel</Link></Button>
                </div>
            </form>
        </AdminLayout>
    );
}
