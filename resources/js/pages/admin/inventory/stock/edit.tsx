import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import type { PageProps } from '@inertiajs/core';

interface StockItem {
    id: string;
    product_name: string;
    variant_sku: string;
    source_name: string;
    qty_available: number;
    qty_reserved: number;
    qty_incoming: number;
    low_stock_threshold: number;
    backorders_allowed: boolean;
    manage_stock: boolean;
}

interface Props extends PageProps { item: StockItem; }

export default function StockEdit() {
    const { item } = usePage<Props>().props;

    const { data, setData, put, processing, errors } = useForm({
        qty_available:       String(item.qty_available),
        qty_incoming:        String(item.qty_incoming),
        low_stock_threshold: String(item.low_stock_threshold),
        backorders_allowed:  item.backorders_allowed,
        manage_stock:        item.manage_stock,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        put(`/admin/inventory/stock/${item.id}`);
    }

    return (
        <>
            <Head title={`Adjust Stock — ${item.variant_sku}`} />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center gap-4">
                    <Button variant="ghost" size="sm" asChild><Link href="/admin/inventory/stock">← Back</Link></Button>
                    <h1 className="text-2xl font-bold tracking-tight">Adjust Stock</h1>
                </div>

                <Card className="max-w-lg">
                    <CardHeader>
                        <CardTitle>{item.product_name}</CardTitle>
                        <p className="text-muted-foreground font-mono text-xs">{item.variant_sku} · {item.source_name}</p>
                        <p className="text-muted-foreground text-xs">Reserved: {item.qty_reserved} (managed by orders — not editable here)</p>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="flex flex-col gap-5">
                            <div className="grid grid-cols-2 gap-4">
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="qty_available">Qty available</Label>
                                    <Input id="qty_available" type="number" min="0" value={data.qty_available} onChange={(e) => setData('qty_available', e.target.value)} autoFocus />
                                    <InputError message={errors.qty_available} />
                                </div>
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="qty_incoming">Qty incoming</Label>
                                    <Input id="qty_incoming" type="number" min="0" value={data.qty_incoming} onChange={(e) => setData('qty_incoming', e.target.value)} />
                                    <InputError message={errors.qty_incoming} />
                                </div>
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="low_stock_threshold">Low stock threshold</Label>
                                <Input id="low_stock_threshold" type="number" min="0" value={data.low_stock_threshold} onChange={(e) => setData('low_stock_threshold', e.target.value)} />
                                <p className="text-muted-foreground text-xs">Alert when saleable qty falls at or below this number.</p>
                                <InputError message={errors.low_stock_threshold} />
                            </div>

                            <div className="flex flex-col gap-3">
                                <div className="flex items-center gap-3">
                                    <Switch id="manage_stock" checked={data.manage_stock} onCheckedChange={(v) => setData('manage_stock', v)} />
                                    <Label htmlFor="manage_stock">Manage stock (uncheck = always in stock)</Label>
                                </div>
                                <div className="flex items-center gap-3">
                                    <Switch id="backorders_allowed" checked={data.backorders_allowed} onCheckedChange={(v) => setData('backorders_allowed', v)} />
                                    <Label htmlFor="backorders_allowed">Allow backorders</Label>
                                </div>
                            </div>

                            <div className="flex gap-3 pt-2">
                                <Button type="submit" disabled={processing}>{processing ? 'Saving…' : 'Save adjustments'}</Button>
                                <Button type="button" variant="outline" asChild><Link href="/admin/inventory/stock">Cancel</Link></Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
