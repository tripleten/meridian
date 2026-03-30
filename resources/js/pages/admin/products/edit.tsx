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

interface Option { value: string; label: string; }
interface IdName { id: string; name: string; }

interface Product {
    id: string;
    name: string;
    sku: string;
    type: string;
    status: string;
    base_price: number;
    compare_price: number | null;
    cost_price: number | null;
    brand_id: string | null;
    attribute_set_id: string | null;
    tax_class_id: string | null;
    url_key: string;
    short_description: string | null;
    description: string | null;
    weight: number | null;
    weight_unit: string;
    is_featured: boolean;
}

interface Props extends PageProps {
    product: Product;
    brands: IdName[];
    attributeSets: IdName[];
    typeOptions: Option[];
    statusOptions: Option[];
}

function centsToDecimal(cents: number | null): string {
    if (cents === null) return '';
    return (cents / 100).toFixed(2);
}

export default function ProductsEdit() {
    const { product, brands, attributeSets, typeOptions, statusOptions } = usePage<Props>().props;

    const { data, setData, put, processing, errors } = useForm({
        name:              product.name,
        type:              product.type,
        status:            product.status,
        base_price:        centsToDecimal(product.base_price),
        compare_price:     centsToDecimal(product.compare_price),
        cost_price:        centsToDecimal(product.cost_price),
        brand_id:          product.brand_id ?? '',
        attribute_set_id:  product.attribute_set_id ?? '',
        tax_class_id:      product.tax_class_id ?? '',
        url_key:           product.url_key,
        short_description: product.short_description ?? '',
        description:       product.description ?? '',
        weight:            product.weight !== null ? String(product.weight) : '',
        weight_unit:       product.weight_unit,
        is_featured:       product.is_featured,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        put(`/admin/products/${product.id}`);
    }

    function destroy() {
        if (!confirm(`Delete "${product.name}"? This cannot be undone.`)) return;
        router.delete(`/admin/products/${product.id}`);
    }

    return (
        <>
            <Head title={`Edit ${product.name}`} />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center gap-4">
                    <Button variant="ghost" size="sm" asChild>
                        <Link href="/admin/products">← Back</Link>
                    </Button>
                    <h1 className="text-2xl font-bold tracking-tight">Edit Product</h1>
                </div>

                <form onSubmit={submit} className="flex flex-col gap-6 max-w-2xl">
                    {/* Core */}
                    <Card>
                        <CardHeader>
                            <CardTitle>{product.name}</CardTitle>
                            <p className="text-muted-foreground font-mono text-xs">{product.sku}</p>
                        </CardHeader>
                        <CardContent className="flex flex-col gap-5">
                            <div className="grid grid-cols-2 gap-4">
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="type">Type</Label>
                                    <Select value={data.type} onValueChange={(v) => setData('type', v)}>
                                        <SelectTrigger id="type"><SelectValue /></SelectTrigger>
                                        <SelectContent>
                                            {typeOptions.map((t) => <SelectItem key={t.value} value={t.value}>{t.label}</SelectItem>)}
                                        </SelectContent>
                                    </Select>
                                    <InputError message={errors.type} />
                                </div>
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="status">Status</Label>
                                    <Select value={data.status} onValueChange={(v) => setData('status', v)}>
                                        <SelectTrigger id="status"><SelectValue /></SelectTrigger>
                                        <SelectContent>
                                            {statusOptions.map((s) => <SelectItem key={s.value} value={s.value}>{s.label}</SelectItem>)}
                                        </SelectContent>
                                    </Select>
                                    <InputError message={errors.status} />
                                </div>
                            </div>

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
                                <Label htmlFor="short_description">Short description</Label>
                                <Textarea
                                    id="short_description"
                                    value={data.short_description}
                                    onChange={(e) => setData('short_description', e.target.value)}
                                    rows={2}
                                />
                                <InputError message={errors.short_description} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="description">Description</Label>
                                <Textarea
                                    id="description"
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    rows={5}
                                />
                                <InputError message={errors.description} />
                            </div>
                        </CardContent>
                    </Card>

                    {/* Pricing */}
                    <Card>
                        <CardHeader><CardTitle>Pricing</CardTitle></CardHeader>
                        <CardContent>
                            <div className="grid grid-cols-3 gap-4">
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="base_price">Price</Label>
                                    <Input
                                        id="base_price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value={data.base_price}
                                        onChange={(e) => setData('base_price', e.target.value)}
                                    />
                                    <InputError message={errors.base_price} />
                                </div>
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="compare_price">Compare at</Label>
                                    <Input
                                        id="compare_price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value={data.compare_price}
                                        onChange={(e) => setData('compare_price', e.target.value)}
                                    />
                                    <InputError message={errors.compare_price} />
                                </div>
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="cost_price">Cost</Label>
                                    <Input
                                        id="cost_price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value={data.cost_price}
                                        onChange={(e) => setData('cost_price', e.target.value)}
                                    />
                                    <InputError message={errors.cost_price} />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Organisation */}
                    <Card>
                        <CardHeader><CardTitle>Organisation</CardTitle></CardHeader>
                        <CardContent className="flex flex-col gap-5">
                            <div className="grid grid-cols-2 gap-4">
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="brand_id">Brand</Label>
                                    <Select value={data.brand_id || '_none'} onValueChange={(v) => setData('brand_id', v === '_none' ? '' : v)}>
                                        <SelectTrigger id="brand_id"><SelectValue placeholder="No brand" /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="_none">No brand</SelectItem>
                                            {brands.map((b) => <SelectItem key={b.id} value={b.id}>{b.name}</SelectItem>)}
                                        </SelectContent>
                                    </Select>
                                    <InputError message={errors.brand_id} />
                                </div>
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="attribute_set_id">Attribute set</Label>
                                    <Select value={data.attribute_set_id || '_none'} onValueChange={(v) => setData('attribute_set_id', v === '_none' ? '' : v)}>
                                        <SelectTrigger id="attribute_set_id"><SelectValue placeholder="None" /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="_none">None</SelectItem>
                                            {attributeSets.map((a) => <SelectItem key={a.id} value={a.id}>{a.name}</SelectItem>)}
                                        </SelectContent>
                                    </Select>
                                    <InputError message={errors.attribute_set_id} />
                                </div>
                            </div>

                            <div className="grid grid-cols-3 gap-4">
                                <div className="col-span-2 flex flex-col gap-1.5">
                                    <Label htmlFor="weight">Weight</Label>
                                    <Input
                                        id="weight"
                                        type="number"
                                        step="0.001"
                                        min="0"
                                        value={data.weight}
                                        onChange={(e) => setData('weight', e.target.value)}
                                    />
                                    <InputError message={errors.weight} />
                                </div>
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="weight_unit">Unit</Label>
                                    <Select value={data.weight_unit} onValueChange={(v) => setData('weight_unit', v)}>
                                        <SelectTrigger id="weight_unit"><SelectValue /></SelectTrigger>
                                        <SelectContent>
                                            {['kg', 'g', 'lb', 'oz'].map((u) => <SelectItem key={u} value={u}>{u}</SelectItem>)}
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>

                            <div className="flex items-center gap-3">
                                <Switch
                                    id="is_featured"
                                    checked={data.is_featured}
                                    onCheckedChange={(v) => setData('is_featured', v)}
                                />
                                <Label htmlFor="is_featured">Featured product</Label>
                            </div>
                        </CardContent>
                    </Card>

                    <div className="flex gap-3">
                        <Button type="submit" disabled={processing}>
                            {processing ? 'Saving…' : 'Save changes'}
                        </Button>
                        <Button type="button" variant="outline" asChild>
                            <Link href="/admin/products">Cancel</Link>
                        </Button>
                    </div>
                </form>

                <Card className="max-w-2xl border-destructive/50">
                    <CardHeader><CardTitle className="text-destructive text-base">Danger zone</CardTitle></CardHeader>
                    <CardContent>
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm font-medium">Delete this product</p>
                                <p className="text-muted-foreground text-xs">Permanently removes the product and all its variants.</p>
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
