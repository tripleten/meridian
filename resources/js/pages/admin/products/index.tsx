import { Head, Link, router, usePage } from '@inertiajs/react';
import { PlusCircle, Search } from 'lucide-react';
import { useRef } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { PageProps } from '@inertiajs/core';

interface Product {
    id: string;
    name: string;
    sku: string;
    type: string;
    status: string;
    base_price: number;
    brand_name: string | null;
    is_featured: boolean;
}

interface Option {
    value: string;
    label: string;
}

interface Brand {
    id: string;
    name: string;
}

interface Props extends PageProps {
    products: {
        data: Product[];
        total: number;
        last_page: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { search?: string; status?: string; type?: string; brand_id?: string };
    statusOptions: Option[];
    typeOptions: Option[];
    brands: Brand[];
}

function formatPrice(cents: number): string {
    return (cents / 100).toFixed(2);
}

const statusVariant: Record<string, 'default' | 'secondary' | 'outline'> = {
    active:   'default',
    draft:    'secondary',
    archived: 'outline',
};

export default function ProductsIndex() {
    const { products, filters, statusOptions, typeOptions, brands } = usePage<Props>().props;
    const searchRef = useRef<HTMLInputElement>(null);

    function applyFilters(extra: Record<string, string>) {
        router.get('/admin/products', {
            search:   searchRef.current?.value ?? filters.search ?? '',
            status:   filters.status ?? '',
            type:     filters.type ?? '',
            brand_id: filters.brand_id ?? '',
            ...extra,
        }, { preserveState: true, replace: true });
    }

    function search(e: React.FormEvent) {
        e.preventDefault();
        applyFilters({ search: searchRef.current?.value ?? '' });
    }

    return (
        <>
            <Head title="Products" />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Products</h1>
                        <p className="text-muted-foreground text-sm">Manage your product catalogue.</p>
                    </div>
                    <Button asChild>
                        <Link href="/admin/products/create">
                            <PlusCircle className="mr-2 h-4 w-4" />New Product
                        </Link>
                    </Button>
                </div>

                <div className="flex flex-wrap gap-2">
                    <form onSubmit={search} className="flex gap-2">
                        <Input
                            ref={searchRef}
                            defaultValue={filters.search ?? ''}
                            placeholder="Search by name or SKU…"
                            className="w-64"
                        />
                        <Button type="submit" variant="secondary" size="icon"><Search className="h-4 w-4" /></Button>
                    </form>

                    <Select value={filters.status ?? '_all'} onValueChange={(v) => applyFilters({ status: v === '_all' ? '' : v })}>
                        <SelectTrigger className="w-36">
                            <SelectValue placeholder="All statuses" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="_all">All statuses</SelectItem>
                            {statusOptions.map((s) => <SelectItem key={s.value} value={s.value}>{s.label}</SelectItem>)}
                        </SelectContent>
                    </Select>

                    <Select value={filters.type ?? '_all'} onValueChange={(v) => applyFilters({ type: v === '_all' ? '' : v })}>
                        <SelectTrigger className="w-36">
                            <SelectValue placeholder="All types" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="_all">All types</SelectItem>
                            {typeOptions.map((t) => <SelectItem key={t.value} value={t.value}>{t.label}</SelectItem>)}
                        </SelectContent>
                    </Select>

                    <Select value={filters.brand_id ?? '_all'} onValueChange={(v) => applyFilters({ brand_id: v === '_all' ? '' : v })}>
                        <SelectTrigger className="w-40">
                            <SelectValue placeholder="All brands" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="_all">All brands</SelectItem>
                            {brands.map((b) => <SelectItem key={b.id} value={b.id}>{b.name}</SelectItem>)}
                        </SelectContent>
                    </Select>
                </div>

                <Card>
                    <CardHeader className="pb-3">
                        <CardTitle className="text-base">{products.total} product{products.total !== 1 ? 's' : ''}</CardTitle>
                    </CardHeader>
                    <CardContent className="p-0">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b bg-muted/50 text-left">
                                    <th className="px-4 py-3 font-medium">Name</th>
                                    <th className="px-4 py-3 font-medium">SKU</th>
                                    <th className="px-4 py-3 font-medium">Type</th>
                                    <th className="px-4 py-3 font-medium">Brand</th>
                                    <th className="px-4 py-3 font-medium">Price</th>
                                    <th className="px-4 py-3 font-medium">Status</th>
                                    <th className="px-4 py-3 font-medium" />
                                </tr>
                            </thead>
                            <tbody>
                                {products.data.length === 0 && (
                                    <tr>
                                        <td colSpan={7} className="text-muted-foreground px-4 py-8 text-center">No products found.</td>
                                    </tr>
                                )}
                                {products.data.map((product) => (
                                    <tr key={product.id} className="border-b last:border-0 hover:bg-muted/30">
                                        <td className="px-4 py-3 font-medium">
                                            {product.name}
                                            {product.is_featured && <span className="text-muted-foreground ml-1 text-xs">★</span>}
                                        </td>
                                        <td className="text-muted-foreground px-4 py-3 font-mono text-xs">{product.sku}</td>
                                        <td className="text-muted-foreground px-4 py-3 capitalize text-xs">{product.type}</td>
                                        <td className="text-muted-foreground px-4 py-3 text-xs">{product.brand_name ?? '—'}</td>
                                        <td className="px-4 py-3 font-mono text-xs">${formatPrice(product.base_price)}</td>
                                        <td className="px-4 py-3">
                                            <Badge variant={statusVariant[product.status] ?? 'secondary'}>
                                                {product.status}
                                            </Badge>
                                        </td>
                                        <td className="px-4 py-3">
                                            <Button asChild variant="ghost" size="sm">
                                                <Link href={`/admin/products/${product.id}/edit`}>Edit</Link>
                                            </Button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </CardContent>
                </Card>

                {products.last_page > 1 && (
                    <div className="flex gap-1">
                        {products.links.map((link, i) => (
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
