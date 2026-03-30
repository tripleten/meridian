import { Head, Link, router, usePage } from '@inertiajs/react';
import { Search } from 'lucide-react';
import { useRef } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Label } from '@/components/ui/label';
import type { PageProps } from '@inertiajs/core';

interface StockItem {
    id: string;
    product_name: string;
    variant_sku: string;
    source_name: string;
    qty_available: number;
    qty_reserved: number;
    qty_incoming: number;
    qty_saleable: number;
    low_stock_threshold: number;
    backorders_allowed: boolean;
    manage_stock: boolean;
}

interface Source { id: string; name: string; }
interface Props extends PageProps {
    items: { data: StockItem[]; total: number; last_page: number; links: { url: string | null; label: string; active: boolean }[] };
    filters: { search?: string; source_id?: string; low_stock_only?: string };
    sources: Source[];
}

export default function StockIndex() {
    const { items, filters, sources } = usePage<Props>().props;
    const searchRef = useRef<HTMLInputElement>(null);

    function applyFilters(extra: Record<string, string>) {
        router.get('/admin/inventory/stock', {
            search:        searchRef.current?.value ?? filters.search ?? '',
            source_id:     filters.source_id ?? '',
            low_stock_only: filters.low_stock_only ?? '',
            ...extra,
        }, { preserveState: true, replace: true });
    }

    function search(e: React.FormEvent) {
        e.preventDefault();
        applyFilters({ search: searchRef.current?.value ?? '' });
    }

    return (
        <>
            <Head title="Stock Levels" />
            <div className="flex flex-col gap-6 p-6">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Stock Levels</h1>
                    <p className="text-muted-foreground text-sm">View and adjust inventory quantities.</p>
                </div>

                <div className="flex flex-wrap items-center gap-2">
                    <form onSubmit={search} className="flex gap-2">
                        <Input ref={searchRef} defaultValue={filters.search ?? ''} placeholder="Search by SKU or product…" className="w-64" />
                        <Button type="submit" variant="secondary" size="icon"><Search className="h-4 w-4" /></Button>
                    </form>
                    <Select value={filters.source_id ?? '_all'} onValueChange={(v) => applyFilters({ source_id: v === '_all' ? '' : v })}>
                        <SelectTrigger className="w-44"><SelectValue placeholder="All sources" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="_all">All sources</SelectItem>
                            {sources.map((s) => <SelectItem key={s.id} value={s.id}>{s.name}</SelectItem>)}
                        </SelectContent>
                    </Select>
                    <div className="flex items-center gap-2">
                        <Switch
                            id="low_stock"
                            checked={filters.low_stock_only === '1'}
                            onCheckedChange={(v) => applyFilters({ low_stock_only: v ? '1' : '' })}
                        />
                        <Label htmlFor="low_stock" className="text-sm">Low stock only</Label>
                    </div>
                </div>

                <Card>
                    <CardHeader className="pb-3">
                        <CardTitle className="text-base">{items.total} item{items.total !== 1 ? 's' : ''}</CardTitle>
                    </CardHeader>
                    <CardContent className="p-0">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b bg-muted/50 text-left">
                                    <th className="px-4 py-3 font-medium">Product / SKU</th>
                                    <th className="px-4 py-3 font-medium">Source</th>
                                    <th className="px-4 py-3 font-medium text-right">Available</th>
                                    <th className="px-4 py-3 font-medium text-right">Reserved</th>
                                    <th className="px-4 py-3 font-medium text-right">Saleable</th>
                                    <th className="px-4 py-3 font-medium text-right">Incoming</th>
                                    <th className="px-4 py-3 font-medium" />
                                </tr>
                            </thead>
                            <tbody>
                                {items.data.length === 0 && (
                                    <tr><td colSpan={7} className="text-muted-foreground px-4 py-8 text-center">No stock records found.</td></tr>
                                )}
                                {items.data.map((item) => {
                                    const isLow = item.qty_saleable <= item.low_stock_threshold;
                                    return (
                                        <tr key={item.id} className="border-b last:border-0 hover:bg-muted/30">
                                            <td className="px-4 py-3">
                                                <div className="font-medium">{item.product_name}</div>
                                                <div className="text-muted-foreground font-mono text-xs">{item.variant_sku}</div>
                                            </td>
                                            <td className="text-muted-foreground px-4 py-3 text-xs">{item.source_name}</td>
                                            <td className="px-4 py-3 text-right">{item.qty_available}</td>
                                            <td className="text-muted-foreground px-4 py-3 text-right">{item.qty_reserved}</td>
                                            <td className="px-4 py-3 text-right">
                                                <span className={isLow ? 'text-destructive font-semibold' : ''}>
                                                    {item.qty_saleable}
                                                </span>
                                                {isLow && <Badge variant="destructive" className="ml-2 text-xs">Low</Badge>}
                                            </td>
                                            <td className="text-muted-foreground px-4 py-3 text-right">{item.qty_incoming}</td>
                                            <td className="px-4 py-3">
                                                <Button asChild variant="ghost" size="sm">
                                                    <Link href={`/admin/inventory/stock/${item.id}/edit`}>Adjust</Link>
                                                </Button>
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </CardContent>
                </Card>

                {items.last_page > 1 && (
                    <div className="flex gap-1">
                        {items.links.map((link, i) => (
                            <Button key={i} variant={link.active ? 'default' : 'outline'} size="sm" disabled={!link.url}
                                onClick={() => link.url && router.get(link.url, {}, { preserveState: true })}
                                dangerouslySetInnerHTML={{ __html: link.label }} />
                        ))}
                    </div>
                )}
            </div>
        </>
    );
}
