import { Head, Link, useForm, router, usePage } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import type { PageProps } from '@inertiajs/core';

interface Brand {
    id: string;
    name: string;
    slug: string;
    description: string | null;
    is_active: boolean;
}

interface Props extends PageProps {
    brand: Brand;
}

export default function BrandsEdit() {
    const { brand } = usePage<Props>().props;

    const { data, setData, put, processing, errors } = useForm({
        name:        brand.name,
        slug:        brand.slug,
        description: brand.description ?? '',
        is_active:   brand.is_active,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        put(`/admin/brands/${brand.id}`);
    }

    function destroy() {
        if (!confirm(`Delete brand "${brand.name}"? This cannot be undone.`)) return;
        router.delete(`/admin/brands/${brand.id}`);
    }

    return (
        <>
            <Head title={`Edit ${brand.name}`} />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center gap-4">
                    <Button variant="ghost" size="sm" asChild>
                        <Link href="/admin/brands">← Back</Link>
                    </Button>
                    <h1 className="text-2xl font-bold tracking-tight">Edit Brand</h1>
                </div>

                <Card className="max-w-lg">
                    <CardHeader><CardTitle>{brand.name}</CardTitle></CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="flex flex-col gap-5">
                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    autoFocus
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="slug">Slug</Label>
                                <Input
                                    id="slug"
                                    value={data.slug}
                                    onChange={(e) => setData('slug', e.target.value)}
                                />
                                <p className="text-muted-foreground text-xs">Changing the slug will break existing URLs.</p>
                                <InputError message={errors.slug} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="description">Description</Label>
                                <Textarea
                                    id="description"
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    rows={3}
                                />
                                <InputError message={errors.description} />
                            </div>

                            <div className="flex items-center gap-3">
                                <Switch
                                    id="is_active"
                                    checked={data.is_active}
                                    onCheckedChange={(v) => setData('is_active', v)}
                                />
                                <Label htmlFor="is_active">Active</Label>
                            </div>

                            <div className="flex gap-3 pt-2">
                                <Button type="submit" disabled={processing}>
                                    {processing ? 'Saving…' : 'Save changes'}
                                </Button>
                                <Button type="button" variant="outline" asChild>
                                    <Link href="/admin/brands">Cancel</Link>
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <Card className="max-w-lg border-destructive/50">
                    <CardHeader><CardTitle className="text-destructive text-base">Danger zone</CardTitle></CardHeader>
                    <CardContent>
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm font-medium">Delete this brand</p>
                                <p className="text-muted-foreground text-xs">Products using this brand will have their brand cleared.</p>
                            </div>
                            <Button type="button" variant="destructive" size="sm" onClick={destroy}>
                                Delete
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
