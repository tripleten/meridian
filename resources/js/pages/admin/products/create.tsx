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
interface IdName { id: string; name: string; }

interface Props extends PageProps {
    brands: IdName[];
    attributeSets: IdName[];
    typeOptions: Option[];
    statusOptions: Option[];
}

function slugify(value: string): string {
    return value.toLowerCase().replace(/[^a-z0-9\s-]/g, '').trim().replace(/[\s]+/g, '-');
}

export default function ProductsCreate() {
    const { brands, attributeSets, typeOptions, statusOptions } = usePage<Props>().props;

    const { data, setData, post, processing, errors } = useForm({
        name:              '',
        sku:               '',
        type:              'simple',
        status:            'draft',
        base_price:        '',
        compare_price:     '',
        cost_price:        '',
        brand_id:          '',
        attribute_set_id:  '',
        tax_class_id:      '',
        url_key:           '',
        short_description: '',
        description:       '',
        weight:            '',
        weight_unit:       'kg',
        is_featured:       false as boolean,
    });

    function handleName(value: string) {
        setData((prev) => ({ ...prev, name: value, url_key: slugify(value) }));
    }

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post('/admin/products');
    }

    return (
        <>
            <Head title="New Product" />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center gap-4">
                    <Button variant="ghost" size="sm" asChild>
                        <Link href="/admin/products">← Back</Link>
                    </Button>
                    <h1 className="text-2xl font-bold tracking-tight">New Product</h1>
                </div>

                <form onSubmit={submit} className="flex flex-col gap-6 max-w-2xl">
                    {/* Core */}
                    <Card>
                        <CardHeader><CardTitle>Product details</CardTitle></CardHeader>
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
                                    onChange={(e) => handleName(e.target.value)}
                                    placeholder="e.g. Classic White T-Shirt"
                                    autoFocus
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="sku">SKU</Label>
                                    <Input
                                        id="sku"
                                        value={data.sku}
                                        onChange={(e) => setData('sku', e.target.value.toUpperCase())}
                                        placeholder="e.g. TSH-WHT-001"
                                        className="font-mono"
                                    />
                                    <InputError message={errors.sku} />
                                </div>
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="url_key">URL key</Label>
                                    <Input
                                        id="url_key"
                                        value={data.url_key}
                                        onChange={(e) => setData('url_key', e.target.value)}
                                        placeholder="e.g. classic-white-t-shirt"
                                    />
                                    <InputError message={errors.url_key} />
                                </div>
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="short_description">Short description</Label>
                                <Textarea
                                    id="short_description"
                                    value={data.short_description}
                                    onChange={(e) => setData('short_description', e.target.value)}
                                    rows={2}
                                    placeholder="Brief summary shown in listings…"
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
                                    placeholder="Full product description…"
                                />
                                <InputError message={errors.description} />
                            </div>
                        </CardContent>
                    </Card>

                    {/* Pricing */}
                    <Card>
                        <CardHeader><CardTitle>Pricing</CardTitle></CardHeader>
                        <CardContent className="flex flex-col gap-5">
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
                                        placeholder="0.00"
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
                                        placeholder="0.00"
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
                                        placeholder="0.00"
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
                                        placeholder="0.000"
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
                            {processing ? 'Saving…' : 'Create product'}
                        </Button>
                        <Button type="button" variant="outline" asChild>
                            <Link href="/admin/products">Cancel</Link>
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}
