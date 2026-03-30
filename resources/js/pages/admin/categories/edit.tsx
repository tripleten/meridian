import { Head, Link, useForm, usePage, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import type { PageProps } from '@inertiajs/core';

interface Category {
    id: string;
    parent_id: string | null;
    name: string;
    url_key: string;
    description: string | null;
    is_active: boolean;
    meta_title: string | null;
    meta_description: string | null;
}

interface CategoryOption {
    id: string;
    name: string;
    depth: number;
}

interface Props extends PageProps {
    category: Category;
    parentOptions: CategoryOption[];
}

export default function CategoriesEdit() {
    const { category, parentOptions } = usePage<Props>().props;

    const { data, setData, put, processing, errors } = useForm({
        name:             category.name,
        url_key:          category.url_key,
        parent_id:        category.parent_id ?? '',
        description:      category.description ?? '',
        is_active:        category.is_active,
        meta_title:       category.meta_title ?? '',
        meta_description: category.meta_description ?? '',
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        put(`/admin/categories/${category.id}`);
    }

    function destroy() {
        if (!confirm(`Delete "${category.name}"? Sub-categories prevent deletion.`)) return;
        router.delete(`/admin/categories/${category.id}`);
    }

    // Exclude the current category from parent options to prevent self-reference
    const filteredOptions = parentOptions.filter((c) => c.id !== category.id);

    return (
        <>
            <Head title={`Edit ${category.name}`} />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center gap-4">
                    <Button variant="ghost" size="sm" asChild>
                        <Link href="/admin/categories">← Back</Link>
                    </Button>
                    <h1 className="text-2xl font-bold tracking-tight">Edit Category</h1>
                </div>

                <form onSubmit={submit} className="flex flex-col gap-6 max-w-lg">
                    <Card>
                        <CardHeader><CardTitle>{category.name}</CardTitle></CardHeader>
                        <CardContent className="flex flex-col gap-5">
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
                                <Label htmlFor="url_key">URL key</Label>
                                <Input
                                    id="url_key"
                                    value={data.url_key}
                                    onChange={(e) => setData('url_key', e.target.value)}
                                />
                                <p className="text-muted-foreground text-xs">Changing the URL key will break existing links.</p>
                                <InputError message={errors.url_key} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="parent_id">Parent category</Label>
                                <Select value={data.parent_id || '_none'} onValueChange={(v) => setData('parent_id', v === '_none' ? '' : v)}>
                                    <SelectTrigger id="parent_id">
                                        <SelectValue placeholder="None (top-level)" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="_none">None (top-level)</SelectItem>
                                        {filteredOptions.map((c) => (
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
                            {processing ? 'Saving…' : 'Save changes'}
                        </Button>
                        <Button type="button" variant="outline" asChild>
                            <Link href="/admin/categories">Cancel</Link>
                        </Button>
                    </div>
                </form>

                <Card className="max-w-lg border-destructive/50">
                    <CardHeader><CardTitle className="text-destructive text-base">Danger zone</CardTitle></CardHeader>
                    <CardContent>
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm font-medium">Delete this category</p>
                                <p className="text-muted-foreground text-xs">Categories with sub-categories cannot be deleted.</p>
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
