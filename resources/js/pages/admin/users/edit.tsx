import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';
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

interface Props extends PageProps {
    user: AdminUser;
    adminRoles: RoleOption[];
}

export default function AdminUsersEdit() {
    const { user, adminRoles } = usePage<Props>().props;

    const { data, setData, put, processing, errors } = useForm({
        name:  user.name,
        email: user.email,
        role:  user.roles[0] ?? '',
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        put(`/admin/users/${user.id}`);
    }

    function deactivate() {
        if (confirm(`Remove admin access for ${user.name}? They will become a regular customer.`)) {
            // Uses Inertia delete
            import('@inertiajs/react').then(({ router }) => {
                router.delete(`/admin/users/${user.id}`);
            });
        }
    }

    return (
        <>
            <Head title={`Edit ${user.name}`} />

            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center gap-4">
                    <Button variant="ghost" size="sm" asChild>
                        <Link href="/admin/users">← Back</Link>
                    </Button>
                    <h1 className="text-2xl font-bold tracking-tight">Edit {user.name}</h1>
                </div>

                <div className="flex max-w-lg flex-col gap-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>User details</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={submit} className="flex flex-col gap-5">
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="name">Full name</Label>
                                    <Input
                                        id="name"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        autoFocus
                                    />
                                    <InputError message={errors.name} />
                                </div>

                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="email">Email address</Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                    />
                                    <InputError message={errors.email} />
                                </div>

                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="role">Role</Label>
                                    <Select value={data.role} onValueChange={(v) => setData('role', v)}>
                                        <SelectTrigger id="role">
                                            <SelectValue />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {adminRoles.map((r) => (
                                                <SelectItem key={r.value} value={r.value}>{r.label}</SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    <InputError message={errors.role} />
                                </div>

                                <div className="flex gap-3 pt-2">
                                    <Button type="submit" disabled={processing}>
                                        {processing ? 'Saving…' : 'Save changes'}
                                    </Button>
                                    <Button type="button" variant="outline" asChild>
                                        <Link href="/admin/users">Cancel</Link>
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    <Card className="border-destructive/30">
                        <CardHeader>
                            <CardTitle className="text-destructive text-base">Danger zone</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-muted-foreground mb-4 text-sm">
                                Removing admin access will revoke all admin roles. The user's account is not deleted.
                            </p>
                            <Button type="button" variant="destructive" size="sm" onClick={deactivate}>
                                Remove admin access
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}
