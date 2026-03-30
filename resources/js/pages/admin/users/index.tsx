import { Head, Link, router, usePage } from '@inertiajs/react';
import { PlusCircle, Search, ShieldCheck, ShieldOff } from 'lucide-react';
import { useRef } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { PageProps } from '@inertiajs/core';

interface AdminUser {
    id: number;
    name: string;
    email: string;
    roles: string[];
    has_two_factor: boolean;
    email_verified_at: string | null;
    created_at: string;
}

interface RoleOption {
    value: string;
    label: string;
}

interface PaginatedUsers {
    data: AdminUser[];
    current_page: number;
    last_page: number;
    from: number;
    to: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

interface Props extends PageProps {
    users: PaginatedUsers;
    filters: { search?: string; role?: string };
    adminRoles: RoleOption[];
}

export default function AdminUsersIndex() {
    const { users, filters, adminRoles } = usePage<Props>().props;
    const searchRef = useRef<HTMLInputElement>(null);

    function search(e: React.FormEvent) {
        e.preventDefault();
        router.get('/admin/users', { search: searchRef.current?.value ?? '', role: filters.role ?? '' }, { preserveState: true, replace: true });
    }

    function filterRole(role: string) {
        router.get('/admin/users', { search: filters.search ?? '', role: role === 'all' ? '' : role }, { preserveState: true, replace: true });
    }

    return (
        <>
            <Head title="Admin Users" />

            <div className="flex flex-col gap-6 p-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Admin Users</h1>
                        <p className="text-muted-foreground text-sm">Manage staff access to the admin panel.</p>
                    </div>
                    <Button asChild>
                        <Link href="/admin/users/create">
                            <PlusCircle className="mr-2 h-4 w-4" />
                            Invite User
                        </Link>
                    </Button>
                </div>

                {/* Filters */}
                <div className="flex gap-3">
                    <form onSubmit={search} className="flex flex-1 gap-2">
                        <Input
                            ref={searchRef}
                            defaultValue={filters.search ?? ''}
                            placeholder="Search by name or email…"
                            className="max-w-sm"
                        />
                        <Button type="submit" variant="secondary" size="icon">
                            <Search className="h-4 w-4" />
                        </Button>
                    </form>

                    <Select defaultValue={filters.role || 'all'} onValueChange={filterRole}>
                        <SelectTrigger className="w-48">
                            <SelectValue placeholder="All roles" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All roles</SelectItem>
                            {adminRoles.map((r) => (
                                <SelectItem key={r.value} value={r.value}>{r.label}</SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>

                {/* Table */}
                <Card>
                    <CardHeader className="pb-3">
                        <CardTitle className="text-base">
                            {users.total} user{users.total !== 1 ? 's' : ''}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-0">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b bg-muted/50 text-left">
                                    <th className="px-4 py-3 font-medium">Name</th>
                                    <th className="px-4 py-3 font-medium">Email</th>
                                    <th className="px-4 py-3 font-medium">Role</th>
                                    <th className="px-4 py-3 font-medium">2FA</th>
                                    <th className="px-4 py-3 font-medium">Joined</th>
                                    <th className="px-4 py-3 font-medium" />
                                </tr>
                            </thead>
                            <tbody>
                                {users.data.length === 0 && (
                                    <tr>
                                        <td colSpan={6} className="text-muted-foreground px-4 py-8 text-center">
                                            No users found.
                                        </td>
                                    </tr>
                                )}
                                {users.data.map((user) => (
                                    <tr key={user.id} className="border-b last:border-0 hover:bg-muted/30">
                                        <td className="px-4 py-3 font-medium">{user.name}</td>
                                        <td className="text-muted-foreground px-4 py-3">{user.email}</td>
                                        <td className="px-4 py-3">
                                            {user.roles.map((r) => (
                                                <Badge key={r} variant="secondary" className="mr-1">{r}</Badge>
                                            ))}
                                        </td>
                                        <td className="px-4 py-3">
                                            {user.has_two_factor
                                                ? <ShieldCheck className="h-4 w-4 text-green-600" />
                                                : <ShieldOff className="text-muted-foreground h-4 w-4" />
                                            }
                                        </td>
                                        <td className="text-muted-foreground px-4 py-3">
                                            {new Date(user.created_at).toLocaleDateString()}
                                        </td>
                                        <td className="px-4 py-3">
                                            <Button asChild variant="ghost" size="sm">
                                                <Link href={`/admin/users/${user.id}/edit`}>Edit</Link>
                                            </Button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </CardContent>
                </Card>

                {/* Pagination */}
                {users.last_page > 1 && (
                    <div className="flex justify-center gap-1">
                        {users.links.map((link, i) => (
                            link.url ? (
                                <Link
                                    key={i}
                                    href={link.url}
                                    className={`rounded border px-3 py-1 text-sm ${link.active ? 'bg-primary text-primary-foreground' : 'hover:bg-muted'}`}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ) : (
                                <span
                                    key={i}
                                    className="text-muted-foreground rounded border px-3 py-1 text-sm opacity-50"
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            )
                        ))}
                    </div>
                )}
            </div>
        </>
    );
}
