import { Head, Link, useForm, usePage, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import type { PageProps } from '@inertiajs/core';

interface CmsBlock {
    id: string;
    identifier: string;
    title: string;
    content: string | null;
    is_active: boolean;
}

interface Props extends PageProps { block: CmsBlock; }

export default function CmsBlocksEdit() {
    const { block } = usePage<Props>().props;

    const { data, setData, put, processing, errors } = useForm({
        title:      block.title,
        identifier: block.identifier,
        content:    block.content ?? '',
        is_active:  block.is_active,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        put(`/admin/cms-blocks/${block.id}`);
    }

    function destroy() {
        if (!confirm(`Delete block "${block.identifier}"?`)) return;
        router.delete(`/admin/cms-blocks/${block.id}`);
    }

    return (
        <>
            <Head title={`Edit ${block.title}`} />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center gap-4">
                    <Button variant="ghost" size="sm" asChild>
                        <Link href="/admin/cms-blocks">← Back</Link>
                    </Button>
                    <h1 className="text-2xl font-bold tracking-tight">Edit Block</h1>
                </div>

                <form onSubmit={submit} className="flex flex-col gap-6 max-w-2xl">
                    <Card>
                        <CardHeader><CardTitle>{block.title}</CardTitle></CardHeader>
                        <CardContent className="flex flex-col gap-5">
                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="title">Title</Label>
                                <Input
                                    id="title"
                                    value={data.title}
                                    onChange={(e) => setData('title', e.target.value)}
                                    autoFocus
                                />
                                <InputError message={errors.title} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="identifier">Identifier</Label>
                                <Input
                                    id="identifier"
                                    value={data.identifier}
                                    onChange={(e) => setData('identifier', e.target.value)}
                                    className="font-mono"
                                />
                                <p className="text-muted-foreground text-xs">Changing the identifier will break any template that references it.</p>
                                <InputError message={errors.identifier} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="content">Content (HTML)</Label>
                                <Textarea
                                    id="content"
                                    value={data.content}
                                    onChange={(e) => setData('content', e.target.value)}
                                    rows={10}
                                    className="font-mono text-xs"
                                />
                                <InputError message={errors.content} />
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

                    <div className="flex gap-3">
                        <Button type="submit" disabled={processing}>
                            {processing ? 'Saving…' : 'Save changes'}
                        </Button>
                        <Button type="button" variant="outline" asChild>
                            <Link href="/admin/cms-blocks">Cancel</Link>
                        </Button>
                    </div>
                </form>

                <Card className="max-w-2xl border-destructive/50">
                    <CardHeader><CardTitle className="text-destructive text-base">Danger zone</CardTitle></CardHeader>
                    <CardContent>
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm font-medium">Delete this block</p>
                                <p className="text-muted-foreground text-xs">Templates referencing this identifier will break.</p>
                            </div>
                            <Button type="button" variant="destructive" size="sm" onClick={destroy}>Delete</Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
