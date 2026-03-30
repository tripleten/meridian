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

interface CmsPage {
    id: string;
    title: string;
    url_key: string;
    content: string | null;
    state: string;
    meta_title: string | null;
    meta_description: string | null;
    meta_keywords: string | null;
    meta_robots_noindex: boolean;
}

interface Option { value: string; label: string; }
interface Props extends PageProps { page: CmsPage; stateOptions: Option[]; }

export default function CmsPagesEdit() {
    const { page, stateOptions } = usePage<Props>().props;

    const { data, setData, put, processing, errors } = useForm({
        title:               page.title,
        url_key:             page.url_key,
        content:             page.content ?? '',
        state:               page.state,
        meta_title:          page.meta_title ?? '',
        meta_description:    page.meta_description ?? '',
        meta_keywords:       page.meta_keywords ?? '',
        meta_robots_noindex: page.meta_robots_noindex,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        put(`/admin/cms-pages/${page.id}`);
    }

    function destroy() {
        if (!confirm(`Delete page "${page.title}"?`)) return;
        router.delete(`/admin/cms-pages/${page.id}`);
    }

    return (
        <>
            <Head title={`Edit ${page.title}`} />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center gap-4">
                    <Button variant="ghost" size="sm" asChild>
                        <Link href="/admin/cms-pages">← Back</Link>
                    </Button>
                    <h1 className="text-2xl font-bold tracking-tight">Edit Page</h1>
                </div>

                <form onSubmit={submit} className="flex flex-col gap-6 max-w-2xl">
                    <Card>
                        <CardHeader><CardTitle>{page.title}</CardTitle></CardHeader>
                        <CardContent className="flex flex-col gap-5">
                            <div className="grid grid-cols-3 gap-4">
                                <div className="col-span-2 flex flex-col gap-1.5">
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
                                    <Label htmlFor="state">State</Label>
                                    <Select value={data.state} onValueChange={(v) => setData('state', v)}>
                                        <SelectTrigger id="state"><SelectValue /></SelectTrigger>
                                        <SelectContent>
                                            {stateOptions.map((s) => <SelectItem key={s.value} value={s.value}>{s.label}</SelectItem>)}
                                        </SelectContent>
                                    </Select>
                                    <InputError message={errors.state} />
                                </div>
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
                                <Label htmlFor="content">Content (HTML)</Label>
                                <Textarea
                                    id="content"
                                    value={data.content}
                                    onChange={(e) => setData('content', e.target.value)}
                                    rows={12}
                                    className="font-mono text-xs"
                                />
                                <InputError message={errors.content} />
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
                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="meta_keywords">Meta keywords</Label>
                                <Input
                                    id="meta_keywords"
                                    value={data.meta_keywords}
                                    onChange={(e) => setData('meta_keywords', e.target.value)}
                                />
                                <InputError message={errors.meta_keywords} />
                            </div>
                            <div className="flex items-center gap-3">
                                <Switch
                                    id="noindex"
                                    checked={data.meta_robots_noindex}
                                    onCheckedChange={(v) => setData('meta_robots_noindex', v)}
                                />
                                <Label htmlFor="noindex">No-index (hide from search engines)</Label>
                            </div>
                        </CardContent>
                    </Card>

                    <div className="flex gap-3">
                        <Button type="submit" disabled={processing}>
                            {processing ? 'Saving…' : 'Save changes'}
                        </Button>
                        <Button type="button" variant="outline" asChild>
                            <Link href="/admin/cms-pages">Cancel</Link>
                        </Button>
                    </div>
                </form>

                <Card className="max-w-2xl border-destructive/50">
                    <CardHeader><CardTitle className="text-destructive text-base">Danger zone</CardTitle></CardHeader>
                    <CardContent>
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm font-medium">Delete this page</p>
                                <p className="text-muted-foreground text-xs">Permanently removes the page.</p>
                            </div>
                            <Button type="button" variant="destructive" size="sm" onClick={destroy}>Delete</Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
