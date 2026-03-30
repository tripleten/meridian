import { Head, Link, router, usePage } from '@inertiajs/react';
import { Search, Eye } from 'lucide-react';
import { useRef } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { PageProps } from '@inertiajs/core';

interface Customer {
    id: string;
    first_name: string;
    last_name: string;
    email: string;
    customer_group_name: string | null;
    is_active: boolean;
    is_subscribed_to_newsletter: boolean;
    last_login_at: string | null;
    created_at: string;
}

interface Group {
    id: string;
    name: string;
}

interface Props extends PageProps {
    customers: {
        data: Customer[];
        total: number;
        last_page: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { search?: string; group_id?: string; is_active?: string };
    groups: Group[];
}

export default function CustomersIndex() {
    const { customers, filters, groups } = usePage<Props>().props;
    const searchRef = useRef<HTMLInputElement>(null);

    function applyFilters(extra: Record<string, string>) {
        router.get('/admin/customers', {
            search:    searchRef.current?.value ?? filters.search ?? '',
            group_id:  filters.group_id ?? '',
            is_active: filters.is_active ?? '',
            ...extra,
        }, { preserveState: true, replace: true });
    }

    function search(e: React.FormEvent) {
        e.preventDefault();
        applyFilters({ search: searchRef.current?.value ?? '' });
    }

    return (
        <>
            <Head title="Customers" />
            <div className="flex flex-col gap-6 p-6">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Customers</h1>
                    <p className="text-muted-foreground text-sm">View and manage customer accounts.</p>
                </div>

                <div className="flex flex-wrap gap-2">
                    <form onSubmit={search} className="flex gap-2">
                        <Input
                            ref={searchRef}
                            defaultValue={filters.search ?? ''}
                            placeholder="Search by name or email…"
                            className="w-64"
                        />
                        <Button type="submit" variant="secondary" size="icon"><Search className="h-4 w-4" /></Button>
                    </form>

                    <Select value={filters.group_id ?? '_all'} onValueChange={(v) => applyFilters({ group_id: v === '_all' ? '' : v })}>
                        <SelectTrigger className="w-44">
                            <SelectValue placeholder="All groups" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="_all">All groups</SelectItem>
                            {groups.map((g) => <SelectItem key={g.id} value={g.id}>{g.name}</SelectItem>)}
                        </SelectContent>
                    </Select>

                    <Select value={filters.is_active ?? '_all'} onValueChange={(v) => applyFilters({ is_active: v === '_all' ? '' : v })}>
                        <SelectTrigger className="w-36">
                            <SelectValue placeholder="Any status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="_all">Any status</SelectItem>
                            <SelectItem value="1">Active</SelectItem>
                            <SelectItem value="0">Inactive</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <Card>
                    <CardHeader className="pb-3">
                        <CardTitle className="text-base">{customers.total} customer{customers.total !== 1 ? 's' : ''}</CardTitle>
                    </CardHeader>
                    <CardContent className="p-0">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b bg-muted/50 text-left">
                                    <th className="px-4 py-3 font-medium">Name</th>
                                    <th className="px-4 py-3 font-medium">Email</th>
                                    <th className="px-4 py-3 font-medium">Group</th>
                                    <th className="px-4 py-3 font-medium">Status</th>
                                    <th className="px-4 py-3 font-medium">Joined</th>
                                    <th className="px-4 py-3 font-medium" />
                                </tr>
                            </thead>
                            <tbody>
                                {customers.data.length === 0 && (
                                    <tr>
                                        <td colSpan={6} className="text-muted-foreground px-4 py-8 text-center">No customers found.</td>
                                    </tr>
                                )}
                                {customers.data.map((c) => (
                                    <tr key={c.id} className="border-b last:border-0 hover:bg-muted/30">
                                        <td className="px-4 py-3 font-medium">{c.first_name} {c.last_name}</td>
                                        <td className="text-muted-foreground px-4 py-3 text-xs">{c.email}</td>
                                        <td className="text-muted-foreground px-4 py-3 text-xs">{c.customer_group_name ?? '—'}</td>
                                        <td className="px-4 py-3">
                                            <Badge variant={c.is_active ? 'default' : 'secondary'}>
                                                {c.is_active ? 'Active' : 'Inactive'}
                                            </Badge>
                                        </td>
                                        <td className="text-muted-foreground px-4 py-3 text-xs">{c.created_at.split(' ')[0]}</td>
                                        <td className="px-4 py-3">
                                            <Button asChild variant="ghost" size="sm">
                                                <Link href={`/admin/customers/${c.id}`}><Eye className="mr-1 h-3 w-3" />View</Link>
                                            </Button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </CardContent>
                </Card>

                {customers.last_page > 1 && (
                    <div className="flex gap-1">
                        {customers.links.map((link, i) => (
                            <Button
                                key={i}
                                variant={link.active ? 'default' : 'outline'}
                                size="sm"
                                disabled={!link.url}
                                onClick={() => link.url && router.get(link.url, {}, { preserveState: true })}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                )}
            </div>
        </>
    );
}
