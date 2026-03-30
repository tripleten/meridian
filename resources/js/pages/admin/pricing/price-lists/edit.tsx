import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { ArrowLeft } from 'lucide-react';

interface PriceList {
    id: string;
    name: string;
    code: string;
    currency_code: string;
    customer_group_id: string | null;
    is_default: boolean;
    is_active: boolean;
}

interface Currency {
    code: string;
    name: string;
}

interface CustomerGroup {
    id: string;
    name: string;
}

interface Props {
    priceList: PriceList;
    currencies: Currency[];
    customerGroups: CustomerGroup[];
}

export default function PriceListEdit({ priceList, currencies, customerGroups }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        name:              priceList.name,
        currency_code:     priceList.currency_code,
        customer_group_id: priceList.customer_group_id ?? '',
        is_default:        priceList.is_default,
        is_active:         priceList.is_active,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        put(`/admin/pricing/price-lists/${priceList.id}`);
    }

    return (
        <AdminLayout>
            <Head title={`Edit ${priceList.name}`} />

            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild>
                    <Link href="/admin/pricing/price-lists"><ArrowLeft className="h-4 w-4" /></Link>
                </Button>
                <h1 className="text-2xl font-semibold">Edit Price List</h1>
            </div>

            <form onSubmit={submit} className="max-w-xl space-y-5">
                <div className="space-y-1">
                    <Label>Name *</Label>
                    <Input value={data.name} onChange={e => setData('name', e.target.value)} required />
                    {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
                </div>

                <div className="space-y-1">
                    <Label>Code</Label>
                    <Input value={priceList.code} disabled className="bg-muted font-mono" />
                    <p className="text-xs text-muted-foreground">Code cannot be changed after creation.</p>
                </div>

                <div className="space-y-1">
                    <Label>Currency *</Label>
                    <Select value={data.currency_code} onValueChange={v => setData('currency_code', v)}>
                        <SelectTrigger>
                            <SelectValue />
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
                    <Button type="submit" disabled={processing}>Save Changes</Button>
                    <Button variant="outline" asChild>
                        <Link href="/admin/pricing/price-lists">Cancel</Link>
                    </Button>
                </div>
            </form>
        </AdminLayout>
    );
}
