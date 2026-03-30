import { Head, Link, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';

function slugify(value: string): string {
    return value.toLowerCase().replace(/[^a-z0-9\s-]/g, '').trim().replace(/[\s]+/g, '-');
}

export default function CmsBlocksCreate() {
    const { data, setData, post, processing, errors } = useForm({
        title:      '',
        identifier: '',
        content:    '',
        is_active:  true as boolean,
    });

    function handleTitle(value: string) {
        setData((prev) => ({ ...prev, title: value, identifier: slugify(value) }));
    }

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post('/admin/cms-blocks');
    }

    return (
        <>
            <Head title="New Block" />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center gap-4">
                    <Button variant="ghost" size="sm" asChild>
                        <Link href="/admin/cms-blocks">← Back</Link>
                    </Button>
                    <h1 className="text-2xl font-bold tracking-tight">New Block</h1>
                </div>

                <form onSubmit={submit} className="flex flex-col gap-6 max-w-2xl">
                    <Card>
                        <CardHeader><CardTitle>Block details</CardTitle></CardHeader>
                        <CardContent className="flex flex-col gap-5">
                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="title">Title</Label>
                                <Input
                                    id="title"
                                    value={data.title}
                                    onChange={(e) => handleTitle(e.target.value)}
                                    placeholder="e.g. Header Promo Banner"
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
                                    placeholder="e.g. header-promo-banner"
                                    className="font-mono"
                                />
                                <p className="text-muted-foreground text-xs">Unique key used to embed this block in templates. Lowercase, hyphens only.</p>
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
                                    placeholder="<div>Block HTML here…</div>"
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
                            {processing ? 'Saving…' : 'Create block'}
                        </Button>
                        <Button type="button" variant="outline" asChild>
                            <Link href="/admin/cms-blocks">Cancel</Link>
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}
