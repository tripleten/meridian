import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import type { PageProps } from '@inertiajs/core';

interface Customer {
    id: string;
    first_name: string;
    last_name: string;
    email: string;
    customer_group_id: string | null;
    phone: string | null;
    company: string | null;
    gender: string | null;
    is_active: boolean;
    is_subscribed_to_newsletter: boolean;
}

interface Group {
    id: string;
    name: string;
}

interface Props extends PageProps {
    customer: Customer;
    groups: Group[];
}

export default function CustomersEdit() {
    const { customer, groups } = usePage<Props>().props;

    const { data, setData, put, processing, errors } = useForm({
        first_name:                    customer.first_name,
        last_name:                     customer.last_name,
        customer_group_id:             customer.customer_group_id ?? '',
        phone:                         customer.phone ?? '',
        company:                       customer.company ?? '',
        gender:                        customer.gender ?? '',
        is_active:                     customer.is_active,
        is_subscribed_to_newsletter:   customer.is_subscribed_to_newsletter,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        put(`/admin/customers/${customer.id}`);
    }

    return (
        <>
            <Head title={`Edit ${customer.first_name} ${customer.last_name}`} />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center gap-4">
                    <Button variant="ghost" size="sm" asChild>
                        <Link href={`/admin/customers/${customer.id}`}>← Back</Link>
                    </Button>
                    <h1 className="text-2xl font-bold tracking-tight">Edit Customer</h1>
                </div>

                <Card className="max-w-lg">
                    <CardHeader>
                        <CardTitle>{customer.first_name} {customer.last_name}</CardTitle>
                        <p className="text-muted-foreground text-sm">{customer.email}</p>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="flex flex-col gap-5">
                            <div className="grid grid-cols-2 gap-4">
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="first_name">First name</Label>
                                    <Input
                                        id="first_name"
                                        value={data.first_name}
                                        onChange={(e) => setData('first_name', e.target.value)}
                                        autoFocus
                                    />
                                    <InputError message={errors.first_name} />
                                </div>
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="last_name">Last name</Label>
                                    <Input
                                        id="last_name"
                                        value={data.last_name}
                                        onChange={(e) => setData('last_name', e.target.value)}
                                    />
                                    <InputError message={errors.last_name} />
                                </div>
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="customer_group_id">Customer group</Label>
                                <Select value={data.customer_group_id || '_none'} onValueChange={(v) => setData('customer_group_id', v === '_none' ? '' : v)}>
                                    <SelectTrigger id="customer_group_id">
                                        <SelectValue placeholder="No group" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="_none">No group</SelectItem>
                                        {groups.map((g) => <SelectItem key={g.id} value={g.id}>{g.name}</SelectItem>)}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.customer_group_id} />
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="phone">Phone</Label>
                                    <Input
                                        id="phone"
                                        value={data.phone}
                                        onChange={(e) => setData('phone', e.target.value)}
                                        placeholder="+44 7000 000000"
                                    />
                                    <InputError message={errors.phone} />
                                </div>
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="company">Company</Label>
                                    <Input
                                        id="company"
                                        value={data.company}
                                        onChange={(e) => setData('company', e.target.value)}
                                    />
                                    <InputError message={errors.company} />
                                </div>
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="gender">Gender</Label>
                                <Select value={data.gender || '_none'} onValueChange={(v) => setData('gender', v === '_none' ? '' : v)}>
                                    <SelectTrigger id="gender"><SelectValue placeholder="Prefer not to say" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="_none">Prefer not to say</SelectItem>
                                        <SelectItem value="male">Male</SelectItem>
                                        <SelectItem value="female">Female</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.gender} />
                            </div>

                            <div className="flex flex-col gap-3">
                                <div className="flex items-center gap-3">
                                    <Switch
                                        id="is_active"
                                        checked={data.is_active}
                                        onCheckedChange={(v) => setData('is_active', v)}
                                    />
                                    <Label htmlFor="is_active">Active account</Label>
                                </div>
                                <div className="flex items-center gap-3">
                                    <Switch
                                        id="newsletter"
                                        checked={data.is_subscribed_to_newsletter}
                                        onCheckedChange={(v) => setData('is_subscribed_to_newsletter', v)}
                                    />
                                    <Label htmlFor="newsletter">Newsletter subscriber</Label>
                                </div>
                            </div>

                            <div className="flex gap-3 pt-2">
                                <Button type="submit" disabled={processing}>
                                    {processing ? 'Saving…' : 'Save changes'}
                                </Button>
                                <Button type="button" variant="outline" asChild>
                                    <Link href={`/admin/customers/${customer.id}`}>Cancel</Link>
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
