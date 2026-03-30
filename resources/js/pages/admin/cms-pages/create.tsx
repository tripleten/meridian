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

interface Option { value: string; label: string; }
interface Props extends PageProps { stateOptions: Option[]; }

function slugify(value: string): string {
    return value.toLowerCase().replace(/[^a-z0-9\/\s-]/g, '').trim().replace(/[\s]+/g, '-');
}

export default function CmsPagesCreate() {
    const { stateOptions } = usePage<Props>().props;

    const { data, setData, post, processing, errors } = useForm({
        title:               '',
        url_key:             '',
        content:             '',
        state:               'draft',
        meta_title:          '',
        meta_description:    '',
        meta_keywords:       '',
        meta_robots_noindex: false as boolean,
    });

    function handleTitle(value: string) {
        setData((prev) => ({ ...prev, title: value, url_key: slugify(value) }));
    }

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post('/admin/cms-pages');
    }

    return (
        <>
            <Head title="New Page" />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center gap-4">
                    <Button variant="ghost" size="sm" asChild>
                        <Link href="/admin/cms-pages">← Back</Link>
                    </Button>
                    <h1 className="text-2xl font-bold tracking-tight">New Page</h1>
                </div>

                <form onSubmit={submit} className="flex flex-col gap-6 max-w-2xl">
                    <Card>
                        <CardHeader><CardTitle>Page content</CardTitle></CardHeader>
                        <CardContent className="flex flex-col gap-5">
                            <div className="grid grid-cols-3 gap-4">
                                <div className="col-span-2 flex flex-col gap-1.5">
                                    <Label htmlFor="title">Title</Label>
                                    <Input
                                        id="title"
                                        value={data.title}
                                        onChange={(e) => handleTitle(e.target.value)}
                                        placeholder="e.g. About Us"
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
                                    placeholder="e.g. about-us"
                                />
                                <p className="text-muted-foreground text-xs">Lowercase letters, numbers, hyphens and slashes.</p>
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
                                    placeholder="<p>Your page content here…</p>"
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
                                    placeholder="Defaults to page title"
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
                                    placeholder="comma, separated, keywords"
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
                            {processing ? 'Saving…' : 'Create page'}
                        </Button>
                        <Button type="button" variant="outline" asChild>
                            <Link href="/admin/cms-pages">Cancel</Link>
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}
