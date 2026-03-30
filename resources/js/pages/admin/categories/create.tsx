import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import type { PageProps } from '@inertiajs/core';

interface CategoryOption {
    id: string;
    parent_id: string | null;
    name: string;
    depth: number;
}

interface Props extends PageProps {
    parentOptions: CategoryOption[];
}

function slugify(value: string): string {
    return value.toLowerCase().replace(/[^a-z0-9\/\s-]/g, '').trim().replace(/[\s]+/g, '-');
}

export default function CategoriesCreate() {
    const { parentOptions } = usePage<Props>().props;

    const { data, setData, post, processing, errors } = useForm({
        name:             '',
        url_key:          '',
        parent_id:        '',
        description:      '',
        is_active:        true as boolean,
        meta_title:       '',
        meta_description: '',
    });

    function handleName(value: string) {
        setData((prev) => ({ ...prev, name: value, url_key: slugify(value) }));
    }

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post('/admin/categories');
    }

    return (
        <>
            <Head title="New Category" />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center gap-4">
                    <Button variant="ghost" size="sm" asChild>
                        <Link href="/admin/categories">← Back</Link>
                    </Button>
                    <h1 className="text-2xl font-bold tracking-tight">New Category</h1>
                </div>

                <form onSubmit={submit} className="flex flex-col gap-6 max-w-lg">
                    <Card>
                        <CardHeader><CardTitle>Category details</CardTitle></CardHeader>
                        <CardContent className="flex flex-col gap-5">
                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    value={data.name}
                                    onChange={(e) => handleName(e.target.value)}
                                    placeholder="e.g. Men's Shoes"
                                    autoFocus
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="url_key">URL key</Label>
                                <Input
                                    id="url_key"
                                    value={data.url_key}
                                    onChange={(e) => setData('url_key', e.target.value)}
                                    placeholder="e.g. mens-shoes"
                                />
                                <p className="text-muted-foreground text-xs">Lowercase letters, numbers, hyphens and slashes.</p>
                                <InputError message={errors.url_key} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="parent_id">Parent category</Label>
                                <Select value={data.parent_id} onValueChange={(v) => setData('parent_id', v === '_none' ? '' : v)}>
                                    <SelectTrigger id="parent_id">
                                        <SelectValue placeholder="None (top-level)" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="_none">None (top-level)</SelectItem>
                                        {parentOptions.map((c) => (
                                            <SelectItem key={c.id} value={c.id}>
                                                {'—'.repeat(c.depth)} {c.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.parent_id} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="description">Description</Label>
                                <Textarea
                                    id="description"
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    rows={3}
                                    placeholder="Optional…"
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
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader><CardTitle>SEO</CardTitle></CardHeader>
                        <CardContent className="flex flex-col gap-5">
                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="meta_title">Meta title</Label>
                                <Input
                                    id="meta_title"
                                    value={data.meta_title}
                                    onChange={(e) => setData('meta_title', e.target.value)}
                                    placeholder="Defaults to category name"
                                />
                                <InputError message={errors.meta_title} />
                            </div>
                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="meta_description">Meta description</Label>
                                <Textarea
                                    id="meta_description"
                                    value={data.meta_description}
                                    onChange={(e) => setData('meta_description', e.target.value)}
                                    rows={2}
                                />
                                <InputError message={errors.meta_description} />
                            </div>
                        </CardContent>
                    </Card>

                    <div className="flex gap-3">
                        <Button type="submit" disabled={processing}>
                            {processing ? 'Saving…' : 'Create category'}
                        </Button>
                        <Button type="button" variant="outline" asChild>
                            <Link href="/admin/categories">Cancel</Link>
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}
