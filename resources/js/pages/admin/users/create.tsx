import { Head, Link, useForm } from '@inertiajs/react';
import { usePage } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { PageProps } from '@inertiajs/core';

interface RoleOption {
    value: string;
    label: string;
}

interface Props extends PageProps {
    adminRoles: RoleOption[];
}

export default function AdminUsersCreate() {
    const { adminRoles } = usePage<Props>().props;

    const { data, setData, post, processing, errors } = useForm({
        name:  '',
        email: '',
        role:  '',
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post('/admin/users');
    }

    return (
        <>
            <Head title="Invite Admin User" />

            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center gap-4">
                    <Button variant="ghost" size="sm" asChild>
                        <Link href="/admin/users">← Back</Link>
                    </Button>
                    <h1 className="text-2xl font-bold tracking-tight">Invite Admin User</h1>
                </div>

                <Card className="max-w-lg">
                    <CardHeader>
                        <CardTitle>New admin user</CardTitle>
                        <p className="text-muted-foreground text-sm">
                            A temporary password will be generated. The user must use "Forgot password" to set their own.
                        </p>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="flex flex-col gap-5">
                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="name">Full name</Label>
                                <Input
                                    id="name"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    placeholder="Jane Smith"
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
                                    placeholder="jane@example.com"
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="role">Role</Label>
                                <Select value={data.role} onValueChange={(v) => setData('role', v)}>
                                    <SelectTrigger id="role">
                                        <SelectValue placeholder="Select a role…" />
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
                                    {processing ? 'Inviting…' : 'Invite user'}
                                </Button>
                                <Button type="button" variant="outline" asChild>
                                    <Link href="/admin/users">Cancel</Link>
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
