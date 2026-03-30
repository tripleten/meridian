import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import type { PageProps } from '@inertiajs/core';

interface Source { id: string; name: string; code: string; type: string; address_line1: string | null; city: string | null; country_code: string | null; is_active: boolean; is_default: boolean; priority: number; }
interface Option { value: string; label: string; }
interface Props extends PageProps { source: Source; typeOptions: Option[]; }

export default function InventorySourcesEdit() {
    const { source, typeOptions } = usePage<Props>().props;

    const { data, setData, put, processing, errors } = useForm({
        name:          source.name,
        code:          source.code,
        type:          source.type,
        address_line1: source.address_line1 ?? '',
        city:          source.city ?? '',
        country_code:  source.country_code ?? '',
        is_active:     source.is_active,
        is_default:    source.is_default,
        priority:      String(source.priority),
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        put(`/admin/inventory/sources/${source.id}`);
    }

    return (
        <>
            <Head title={`Edit ${source.name}`} />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center gap-4">
                    <Button variant="ghost" size="sm" asChild><Link href="/admin/inventory/sources">← Back</Link></Button>
                    <h1 className="text-2xl font-bold tracking-tight">Edit Source</h1>
                </div>

                <Card className="max-w-lg">
                    <CardHeader><CardTitle>{source.name}</CardTitle></CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="flex flex-col gap-5">
                            <div className="grid grid-cols-2 gap-4">
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="name">Name</Label>
                                    <Input id="name" value={data.name} onChange={(e) => setData('name', e.target.value)} autoFocus />
                                    <InputError message={errors.name} />
                                </div>
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="code">Code</Label>
                                    <Input id="code" value={data.code} onChange={(e) => setData('code', e.target.value)} className="font-mono" />
                                    <InputError message={errors.code} />
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="type">Type</Label>
                                    <Select value={data.type} onValueChange={(v) => setData('type', v)}>
                                        <SelectTrigger id="type"><SelectValue /></SelectTrigger>
                                        <SelectContent>
                                            {typeOptions.map((t) => <SelectItem key={t.value} value={t.value}>{t.label}</SelectItem>)}
                                        </SelectContent>
                                    </Select>
                                    <InputError message={errors.type} />
                                </div>
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="priority">Priority</Label>
                                    <Input id="priority" type="number" min="0" value={data.priority} onChange={(e) => setData('priority', e.target.value)} />
                                    <InputError message={errors.priority} />
                                </div>
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="address_line1">Address</Label>
                                <Input id="address_line1" value={data.address_line1} onChange={(e) => setData('address_line1', e.target.value)} />
                                <InputError message={errors.address_line1} />
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="city">City</Label>
                                    <Input id="city" value={data.city} onChange={(e) => setData('city', e.target.value)} />
                                    <InputError message={errors.city} />
                                </div>
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="country_code">Country (ISO)</Label>
                                    <Input id="country_code" value={data.country_code} onChange={(e) => setData('country_code', e.target.value.toUpperCase())} maxLength={2} className="uppercase" />
                                    <InputError message={errors.country_code} />
                                </div>
                            </div>

                            <div className="flex flex-col gap-3">
                                <div className="flex items-center gap-3">
                                    <Switch id="is_active" checked={data.is_active} onCheckedChange={(v) => setData('is_active', v)} />
                                    <Label htmlFor="is_active">Active</Label>
                                </div>
                                <div className="flex items-center gap-3">
                                    <Switch id="is_default" checked={data.is_default} onCheckedChange={(v) => setData('is_default', v)} />
                                    <Label htmlFor="is_default">Default source</Label>
                                </div>
                            </div>

                            <div className="flex gap-3 pt-2">
                                <Button type="submit" disabled={processing}>{processing ? 'Saving…' : 'Save changes'}</Button>
                                <Button type="button" variant="outline" asChild><Link href="/admin/inventory/sources">Cancel</Link></Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
