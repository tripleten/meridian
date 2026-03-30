import { Head, Link, router, usePage } from '@inertiajs/react';
import { PlusCircle, Search } from 'lucide-react';
import { useRef } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import type { PageProps } from '@inertiajs/core';

interface Brand {
    id: string;
    name: string;
    slug: string;
    is_active: boolean;
    product_count: number;
    created_at: string;
}

interface Props extends PageProps {
    brands: { data: Brand[]; total: number; last_page: number; links: { url: string | null; label: string; active: boolean }[] };
    filters: { search?: string };
}

export default function BrandsIndex() {
    const { brands, filters } = usePage<Props>().props;
    const searchRef = useRef<HTMLInputElement>(null);

    function search(e: React.FormEvent) {
        e.preventDefault();
        router.get('/admin/brands', { search: searchRef.current?.value ?? '' }, { preserveState: true, replace: true });
    }

    return (
        <>
            <Head title="Brands" />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Brands</h1>
                        <p className="text-muted-foreground text-sm">Manage product brands.</p>
                    </div>
                    <Button asChild><Link href="/admin/brands/create"><PlusCircle className="mr-2 h-4 w-4" />New Brand</Link></Button>
                </div>

                <form onSubmit={search} className="flex gap-2">
                    <Input ref={searchRef} defaultValue={filters.search ?? ''} placeholder="Search brands…" className="max-w-sm" />
                    <Button type="submit" variant="secondary" size="icon"><Search className="h-4 w-4" /></Button>
                </form>

                <Card>
                    <CardHeader className="pb-3"><CardTitle className="text-base">{brands.total} brand{brands.total !== 1 ? 's' : ''}</CardTitle></CardHeader>
                    <CardContent className="p-0">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b bg-muted/50 text-left">
                                    <th className="px-4 py-3 font-medium">Name</th>
                                    <th className="px-4 py-3 font-medium">Slug</th>
                                    <th className="px-4 py-3 font-medium">Products</th>
                                    <th className="px-4 py-3 font-medium">Status</th>
                                    <th className="px-4 py-3 font-medium" />
                                </tr>
                            </thead>
                            <tbody>
                                {brands.data.length === 0 && (
                                    <tr><td colSpan={5} className="text-muted-foreground px-4 py-8 text-center">No brands yet.</td></tr>
                                )}
                                {brands.data.map((brand) => (
                                    <tr key={brand.id} className="border-b last:border-0 hover:bg-muted/30">
                                        <td className="px-4 py-3 font-medium">{brand.name}</td>
                                        <td className="text-muted-foreground px-4 py-3 font-mono text-xs">{brand.slug}</td>
                                        <td className="px-4 py-3">{brand.product_count}</td>
                                        <td className="px-4 py-3">
                                            <Badge variant={brand.is_active ? 'default' : 'secondary'}>
                                                {brand.is_active ? 'Active' : 'Inactive'}
                                            </Badge>
                                        </td>
                                        <td className="px-4 py-3">
                                            <Button asChild variant="ghost" size="sm"><Link href={`/admin/brands/${brand.id}/edit`}>Edit</Link></Button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
