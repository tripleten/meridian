import { Head, Link, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';

function slugify(value: string): string {
    return value
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/[\s]+/g, '-');
}

export default function BrandsCreate() {
    const { data, setData, post, processing, errors } = useForm({
        name:        '',
        slug:        '',
        description: '',
        is_active:   true as boolean,
    });

    function handleName(value: string) {
        setData((prev) => ({ ...prev, name: value, slug: slugify(value) }));
    }

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post('/admin/brands');
    }

    return (
        <>
            <Head title="New Brand" />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center gap-4">
                    <Button variant="ghost" size="sm" asChild>
                        <Link href="/admin/brands">← Back</Link>
                    </Button>
                    <h1 className="text-2xl font-bold tracking-tight">New Brand</h1>
                </div>

                <Card className="max-w-lg">
                    <CardHeader><CardTitle>Brand details</CardTitle></CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="flex flex-col gap-5">
                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    value={data.name}
                                    onChange={(e) => handleName(e.target.value)}
                                    placeholder="e.g. Nike"
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
                                    placeholder="e.g. nike"
                                />
                                <p className="text-muted-foreground text-xs">Used in URLs. Lowercase letters, numbers, and hyphens only.</p>
                                <InputError message={errors.slug} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="description">Description</Label>
                                <Textarea
                                    id="description"
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    rows={3}
                                    placeholder="Optional brand description…"
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
                                    {processing ? 'Saving…' : 'Create brand'}
                                </Button>
                                <Button type="button" variant="outline" asChild>
                                    <Link href="/admin/brands">Cancel</Link>
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
