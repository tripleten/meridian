import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { ArrowLeft } from 'lucide-react';

interface Currency {
    code: string;
    name: string;
}

interface CustomerGroup {
    id: string;
    name: string;
}

interface Props {
    currencies: Currency[];
    customerGroups: CustomerGroup[];
}

export default function PriceListCreate({ currencies, customerGroups }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        name:              '',
        code:              '',
        currency_code:     '',
        customer_group_id: '',
        is_default:        false as boolean,
        is_active:         true  as boolean,
    });

    function slugify(v: string): string {
        return v.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
    }

    function handleNameChange(v: string) {
        setData(prev => ({ ...prev, name: v, code: slugify(v) }));
    }

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post('/admin/pricing/price-lists');
    }

    return (
        <AdminLayout>
            <Head title="New Price List" />

            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild>
                    <Link href="/admin/pricing/price-lists"><ArrowLeft className="h-4 w-4" /></Link>
                </Button>
                <h1 className="text-2xl font-semibold">New Price List</h1>
            </div>

            <form onSubmit={submit} className="max-w-xl space-y-5">
                <div className="space-y-1">
                    <Label>Name *</Label>
                    <Input value={data.name} onChange={e => handleNameChange(e.target.value)} required />
                    {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
                </div>

                <div className="space-y-1">
                    <Label>Code *</Label>
                    <Input
                        value={data.code}
                        onChange={e => setData('code', e.target.value)}
                        placeholder="e.g. retail_usd"
                        required
                    />
                    <p className="text-xs text-muted-foreground">Unique identifier, lowercase with underscores.</p>
                    {errors.code && <p className="text-sm text-destructive">{errors.code}</p>}
                </div>

                <div className="space-y-1">
                    <Label>Currency *</Label>
                    <Select value={data.currency_code} onValueChange={v => setData('currency_code', v)}>
                        <SelectTrigger>
                            <SelectValue placeholder="Select currency" />
                        </SelectTrigger>
                        <SelectContent>
                            {currencies.map(c => (
                                <SelectItem key={c.code} value={c.code}>{c.code} — {c.name}</SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    {errors.currency_code && <p className="text-sm text-destructive">{errors.currency_code}</p>}
                </div>

                <div className="space-y-1">
                    <Label>Customer Group</Label>
                    <Select
                        value={data.customer_group_id}
                        onValueChange={v => setData('customer_group_id', v === '_none' ? '' : v)}
                    >
                        <SelectTrigger>
                            <SelectValue placeholder="All customers" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="_none">All customers</SelectItem>
                            {customerGroups.map(g => (
                                <SelectItem key={g.id} value={g.id}>{g.name}</SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>

                <div className="flex items-center gap-3">
                    <Switch checked={data.is_default} onCheckedChange={v => setData('is_default', v)} id="is_default" />
                    <Label htmlFor="is_default">Default price list</Label>
                </div>

                <div className="flex items-center gap-3">
                    <Switch checked={data.is_active} onCheckedChange={v => setData('is_active', v)} id="is_active" />
                    <Label htmlFor="is_active">Active</Label>
                </div>

                <div className="flex gap-3 pt-2">
                    <Button type="submit" disabled={processing}>Create Price List</Button>
                    <Button variant="outline" asChild>
                        <Link href="/admin/pricing/price-lists">Cancel</Link>
                    </Button>
                </div>
            </form>
        </AdminLayout>
    );
}
