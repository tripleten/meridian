import { Head, Link, usePage } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { PageProps } from '@inertiajs/core';

interface Customer {
    id: string;
    first_name: string;
    last_name: string;
    email: string;
    phone: string | null;
    company: string | null;
    customer_group_name: string | null;
    gender: string | null;
    is_active: boolean;
    is_subscribed_to_newsletter: boolean;
    last_login_at: string | null;
    created_at: string;
}

interface Address {
    id: string;
    label: string | null;
    first_name: string;
    last_name: string;
    company: string | null;
    line1: string;
    line2: string | null;
    city: string;
    county: string | null;
    postcode: string;
    country_code: string;
    phone: string | null;
    is_default_billing: boolean;
    is_default_shipping: boolean;
}

interface Props extends PageProps {
    customer: Customer;
    addresses: Address[];
}

function AddressCard({ address }: { address: Address }) {
    return (
        <div className="rounded-lg border p-4 text-sm">
            <div className="mb-2 flex items-center gap-2">
                <span className="font-medium">{address.label ?? 'Address'}</span>
                {address.is_default_billing && <Badge variant="outline" className="text-xs">Default billing</Badge>}
                {address.is_default_shipping && <Badge variant="outline" className="text-xs">Default shipping</Badge>}
            </div>
            <p>{address.first_name} {address.last_name}</p>
            {address.company && <p className="text-muted-foreground">{address.company}</p>}
            <p>{address.line1}</p>
            {address.line2 && <p>{address.line2}</p>}
            <p>{address.city}{address.county ? `, ${address.county}` : ''} {address.postcode}</p>
            <p className="uppercase">{address.country_code}</p>
            {address.phone && <p className="text-muted-foreground mt-1">{address.phone}</p>}
        </div>
    );
}

export default function CustomersShow() {
    const { customer, addresses } = usePage<Props>().props;

    return (
        <>
            <Head title={`${customer.first_name} ${customer.last_name}`} />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center gap-4">
                    <Button variant="ghost" size="sm" asChild>
                        <Link href="/admin/customers">← Back</Link>
                    </Button>
                    <div className="flex-1">
                        <h1 className="text-2xl font-bold tracking-tight">{customer.first_name} {customer.last_name}</h1>
                        <p className="text-muted-foreground text-sm">{customer.email}</p>
                    </div>
                    <Button asChild variant="outline" size="sm">
                        <Link href={`/admin/customers/${customer.id}/edit`}>Edit</Link>
                    </Button>
                </div>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <Card>
                        <CardHeader><CardTitle>Account details</CardTitle></CardHeader>
                        <CardContent>
                            <dl className="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                                <dt className="text-muted-foreground">Status</dt>
                                <dd>
                                    <Badge variant={customer.is_active ? 'default' : 'secondary'}>
                                        {customer.is_active ? 'Active' : 'Inactive'}
                                    </Badge>
                                </dd>
                                <dt className="text-muted-foreground">Group</dt>
                                <dd>{customer.customer_group_name ?? '—'}</dd>
                                <dt className="text-muted-foreground">Phone</dt>
                                <dd>{customer.phone ?? '—'}</dd>
                                <dt className="text-muted-foreground">Company</dt>
                                <dd>{customer.company ?? '—'}</dd>
                                <dt className="text-muted-foreground">Gender</dt>
                                <dd className="capitalize">{customer.gender?.replace(/_/g, ' ') ?? '—'}</dd>
                                <dt className="text-muted-foreground">Newsletter</dt>
                                <dd>{customer.is_subscribed_to_newsletter ? 'Subscribed' : 'Not subscribed'}</dd>
                                <dt className="text-muted-foreground">Last login</dt>
                                <dd>{customer.last_login_at ? customer.last_login_at.split(' ')[0] : 'Never'}</dd>
                                <dt className="text-muted-foreground">Member since</dt>
                                <dd>{customer.created_at.split(' ')[0]}</dd>
                            </dl>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader><CardTitle>Addresses ({addresses.length})</CardTitle></CardHeader>
                        <CardContent>
                            {addresses.length === 0 && (
                                <p className="text-muted-foreground text-sm">No addresses saved.</p>
                            )}
                            <div className="flex flex-col gap-3">
                                {addresses.map((a) => <AddressCard key={a.id} address={a} />)}
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}
