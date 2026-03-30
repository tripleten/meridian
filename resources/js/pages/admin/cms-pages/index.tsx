import { Head, Link, router, usePage } from '@inertiajs/react';
import { PlusCircle, Search } from 'lucide-react';
import { useRef } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { PageProps } from '@inertiajs/core';

interface CmsPage {
    id: string;
    title: string;
    url_key: string;
    state: string;
    published_at: string | null;
    created_at: string;
}

interface Option { value: string; label: string; }

interface Props extends PageProps {
    pages: {
        data: CmsPage[];
        total: number;
        last_page: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { search?: string; state?: string };
    stateOptions: Option[];
}

const stateVariant: Record<string, 'default' | 'secondary' | 'outline'> = {
    published: 'default',
    draft:     'secondary',
    archived:  'outline',
};

export default function CmsPagesIndex() {
    const { pages, filters, stateOptions } = usePage<Props>().props;
    const searchRef = useRef<HTMLInputElement>(null);

    function applyFilters(extra: Record<string, string>) {
        router.get('/admin/cms-pages', {
            search: searchRef.current?.value ?? filters.search ?? '',
            state:  filters.state ?? '',
            ...extra,
        }, { preserveState: true, replace: true });
    }

    function search(e: React.FormEvent) {
        e.preventDefault();
        applyFilters({ search: searchRef.current?.value ?? '' });
    }

    return (
        <>
            <Head title="CMS Pages" />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Pages</h1>
                        <p className="text-muted-foreground text-sm">Manage static content pages.</p>
                    </div>
                    <Button asChild>
                        <Link href="/admin/cms-pages/create">
                            <PlusCircle className="mr-2 h-4 w-4" />New Page
                        </Link>
                    </Button>
                </div>

                <div className="flex flex-wrap gap-2">
                    <form onSubmit={search} className="flex gap-2">
                        <Input
                            ref={searchRef}
                            defaultValue={filters.search ?? ''}
                            placeholder="Search pages…"
                            className="w-64"
                        />
                        <Button type="submit" variant="secondary" size="icon"><Search className="h-4 w-4" /></Button>
                    </form>

                    <Select value={filters.state ?? '_all'} onValueChange={(v) => applyFilters({ state: v === '_all' ? '' : v })}>
                        <SelectTrigger className="w-36">
                            <SelectValue placeholder="All states" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="_all">All states</SelectItem>
                            {stateOptions.map((s) => <SelectItem key={s.value} value={s.value}>{s.label}</SelectItem>)}
                        </SelectContent>
                    </Select>
                </div>

                <Card>
                    <CardHeader className="pb-3">
                        <CardTitle className="text-base">{pages.total} page{pages.total !== 1 ? 's' : ''}</CardTitle>
                    </CardHeader>
                    <CardContent className="p-0">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b bg-muted/50 text-left">
                                    <th className="px-4 py-3 font-medium">Title</th>
                                    <th className="px-4 py-3 font-medium">URL</th>
                                    <th className="px-4 py-3 font-medium">State</th>
                                    <th className="px-4 py-3 font-medium">Published</th>
                                    <th className="px-4 py-3 font-medium" />
                                </tr>
                            </thead>
                            <tbody>
                                {pages.data.length === 0 && (
                                    <tr>
                                        <td colSpan={5} className="text-muted-foreground px-4 py-8 text-center">No pages yet.</td>
                                    </tr>
                                )}
                                {pages.data.map((page) => (
                                    <tr key={page.id} className="border-b last:border-0 hover:bg-muted/30">
                                        <td className="px-4 py-3 font-medium">{page.title}</td>
                                        <td className="text-muted-foreground px-4 py-3 font-mono text-xs">/{page.url_key}</td>
                                        <td className="px-4 py-3">
                                            <Badge variant={stateVariant[page.state] ?? 'secondary'}>
                                                {page.state}
                                            </Badge>
                                        </td>
                                        <td className="text-muted-foreground px-4 py-3 text-xs">
                                            {page.published_at ? page.published_at.split(' ')[0] : '—'}
                                        </td>
                                        <td className="px-4 py-3">
                                            <Button asChild variant="ghost" size="sm">
                                                <Link href={`/admin/cms-pages/${page.id}/edit`}>Edit</Link>
                                            </Button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </CardContent>
                </Card>

                {pages.last_page > 1 && (
                    <div className="flex gap-1">
                        {pages.links.map((link, i) => (
                            <Button
                                key={i}
                                variant={link.active ? 'default' : 'outline'}
                                size="sm"
                                disabled={!link.url}
                                onClick={() => link.url && router.get(link.url, {}, { preserveState: true })}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                )}
            </div>
        </>
    );
}
