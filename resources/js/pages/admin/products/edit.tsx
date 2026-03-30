import { Head, Link, useForm, usePage, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { PageProps } from '@inertiajs/core';
import { useRef, useState } from 'react';
import { ImagePlus, X, Plus, Pencil, Trash2, Check } from 'lucide-react';

interface Option { value: string; label: string; }
interface IdName { id: string; name: string; }

interface Product {
    id: string; name: string; sku: string; type: string; status: string; visibility: string;
    base_price: number; compare_price: number | null; cost_price: number | null;
    brand_id: string | null; attribute_set_id: string | null; tax_class_id: string | null;
    url_key: string; short_description: string | null; description: string | null;
    weight: number | null; weight_unit: string; is_featured: boolean; main_image: string | null;
}

interface Variant {
    id: string; product_id: string; sku: string; name: string | null;
    price: number | null; compare_price: number | null; cost_price: number | null;
    weight: number | null; is_active: boolean; sort_order: number;
}

interface CategoryOption { id: string; label: string; depth: number; }

interface Props extends PageProps {
    product: Product; variants: Variant[];
    brands: IdName[]; attributeSets: IdName[];
    categories: CategoryOption[]; assignedCategories: string[];
    typeOptions: Option[]; statusOptions: Option[]; visibilityOptions: Option[];
}

function centsToDecimal(cents: number | null): string {
    if (cents === null || cents === undefined) return '';
    return (cents / 100).toFixed(2);
}

const NO_WEIGHT_TYPES = ['virtual', 'downloadable'];

// ── Variant row component (inline edit) ────────────────────────────────────
function VariantRow({ variant, productId }: { variant: Variant; productId: string }) {
    const [editing, setEditing] = useState(false);
    const { data, setData, put, processing, errors, reset } = useForm({
        sku:           variant.sku,
        name:          variant.name ?? '',
        price:         centsToDecimal(variant.price),
        compare_price: centsToDecimal(variant.compare_price),
        cost_price:    centsToDecimal(variant.cost_price),
        weight:        variant.weight !== null ? String(variant.weight) : '',
        is_active:     variant.is_active,
        sort_order:    variant.sort_order,
    });

    function save(e: React.FormEvent) {
        e.preventDefault();
        put(`/admin/products/${productId}/variants/${variant.id}`, {
            onSuccess: () => setEditing(false),
        });
    }

    function destroy() {
        if (!confirm(`Delete variant "${variant.sku}"?`)) return;
        router.delete(`/admin/products/${productId}/variants/${variant.id}`);
    }

    if (editing) {
        return (
            <TableRow className="bg-muted/30">
                <TableCell colSpan={6} className="p-3">
                    <form onSubmit={save}>
                        <div className="grid grid-cols-6 gap-3 items-end">
                            <div className="flex flex-col gap-1">
                                <Label className="text-xs">SKU *</Label>
                                <Input value={data.sku} onChange={e => setData('sku', e.target.value.toUpperCase())} className="h-8 font-mono text-xs" />
                                {errors.sku && <p className="text-xs text-destructive">{errors.sku}</p>}
                            </div>
                            <div className="flex flex-col gap-1">
                                <Label className="text-xs">Name</Label>
                                <Input value={data.name} onChange={e => setData('name', e.target.value)} placeholder="e.g. Red / L" className="h-8 text-xs" />
                            </div>
                            <div className="flex flex-col gap-1">
                                <Label className="text-xs">Price</Label>
                                <Input type="number" step="0.01" min="0" value={data.price} onChange={e => setData('price', e.target.value)} placeholder="Inherit" className="h-8 text-xs" />
                            </div>
                            <div className="flex flex-col gap-1">
                                <Label className="text-xs">Cost</Label>
                                <Input type="number" step="0.01" min="0" value={data.cost_price} onChange={e => setData('cost_price', e.target.value)} placeholder="Inherit" className="h-8 text-xs" />
                            </div>
                            <div className="flex flex-col gap-1">
                                <Label className="text-xs">Weight</Label>
                                <Input type="number" step="0.001" min="0" value={data.weight} onChange={e => setData('weight', e.target.value)} placeholder="Inherit" className="h-8 text-xs" />
                            </div>
                            <div className="flex items-end gap-2">
                                <Button type="submit" size="sm" disabled={processing} className="h-8">
                                    <Check className="h-3.5 w-3.5 mr-1" /> Save
                                </Button>
                                <Button type="button" variant="ghost" size="sm" className="h-8" onClick={() => { reset(); setEditing(false); }}>
                                    Cancel
                                </Button>
                            </div>
                        </div>
                        <div className="mt-2 flex items-center gap-3">
                            <Switch id={`active-${variant.id}`} checked={data.is_active} onCheckedChange={v => setData('is_active', v)} />
                            <Label htmlFor={`active-${variant.id}`} className="text-xs">Active</Label>
                            <div className="flex items-center gap-1 ml-4">
                                <Label className="text-xs">Sort</Label>
                                <Input type="number" min="0" value={data.sort_order} onChange={e => setData('sort_order', parseInt(e.target.value) || 0)} className="h-7 w-16 text-xs" />
                            </div>
                        </div>
                    </form>
                </TableCell>
            </TableRow>
        );
    }

    return (
        <TableRow>
            <TableCell className="font-mono text-sm">{variant.sku}</TableCell>
            <TableCell className="text-sm text-muted-foreground">{variant.name ?? <span className="italic">Inherit</span>}</TableCell>
            <TableCell className="text-sm text-right">
                {variant.price !== null ? `$${centsToDecimal(variant.price)}` : <span className="italic text-muted-foreground">Inherit</span>}
            </TableCell>
            <TableCell className="text-sm text-right">
                {variant.weight !== null ? `${variant.weight}` : <span className="italic text-muted-foreground">Inherit</span>}
            </TableCell>
            <TableCell>
                <Badge variant={variant.is_active ? 'default' : 'secondary'}>
                    {variant.is_active ? 'Active' : 'Inactive'}
                </Badge>
            </TableCell>
            <TableCell className="text-right">
                <Button variant="ghost" size="icon" className="h-7 w-7" onClick={() => setEditing(true)}>
                    <Pencil className="h-3.5 w-3.5" />
                </Button>
                <Button variant="ghost" size="icon" className="h-7 w-7 text-destructive hover:text-destructive" onClick={destroy}>
                    <Trash2 className="h-3.5 w-3.5" />
                </Button>
            </TableCell>
        </TableRow>
    );
}

// ── Add variant form ────────────────────────────────────────────────────────
function AddVariantForm({ productId, onDone }: { productId: string; onDone: () => void }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        sku: '', name: '', price: '', compare_price: '', cost_price: '',
        weight: '', is_active: true as boolean, sort_order: 0 as number,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post(`/admin/products/${productId}/variants`, {
            onSuccess: () => { reset(); onDone(); },
        });
    }

    return (
        <form onSubmit={submit} className="mt-4 rounded-md border border-dashed p-4 bg-muted/20">
            <p className="text-sm font-medium mb-3">New variant</p>
            <div className="grid grid-cols-3 gap-3">
                <div className="flex flex-col gap-1">
                    <Label className="text-xs">SKU *</Label>
                    <Input value={data.sku} onChange={e => setData('sku', e.target.value.toUpperCase())} placeholder="TSH-RED-L" className="h-8 font-mono text-xs" />
                    {errors.sku && <p className="text-xs text-destructive">{errors.sku}</p>}
                </div>
                <div className="flex flex-col gap-1">
                    <Label className="text-xs">Name / options</Label>
                    <Input value={data.name} onChange={e => setData('name', e.target.value)} placeholder="e.g. Red / Large" className="h-8 text-xs" />
                </div>
                <div className="flex flex-col gap-1">
                    <Label className="text-xs">Price override</Label>
                    <Input type="number" step="0.01" min="0" value={data.price} onChange={e => setData('price', e.target.value)} placeholder="Blank = inherit" className="h-8 text-xs" />
                </div>
                <div className="flex flex-col gap-1">
                    <Label className="text-xs">Compare at</Label>
                    <Input type="number" step="0.01" min="0" value={data.compare_price} onChange={e => setData('compare_price', e.target.value)} placeholder="Blank = inherit" className="h-8 text-xs" />
                </div>
                <div className="flex flex-col gap-1">
                    <Label className="text-xs">Cost override</Label>
                    <Input type="number" step="0.01" min="0" value={data.cost_price} onChange={e => setData('cost_price', e.target.value)} placeholder="Blank = inherit" className="h-8 text-xs" />
                </div>
                <div className="flex flex-col gap-1">
                    <Label className="text-xs">Weight override</Label>
                    <Input type="number" step="0.001" min="0" value={data.weight} onChange={e => setData('weight', e.target.value)} placeholder="Blank = inherit" className="h-8 text-xs" />
                </div>
            </div>
            <div className="mt-3 flex items-center gap-4">
                <div className="flex items-center gap-2">
                    <Switch id="new-active" checked={data.is_active} onCheckedChange={v => setData('is_active', v)} />
                    <Label htmlFor="new-active" className="text-xs">Active</Label>
                </div>
                <div className="flex items-center gap-2">
                    <Label className="text-xs">Sort order</Label>
                    <Input type="number" min="0" value={data.sort_order} onChange={e => setData('sort_order', parseInt(e.target.value) || 0)} className="h-7 w-16 text-xs" />
                </div>
                <div className="ml-auto flex gap-2">
                    <Button type="submit" size="sm" disabled={processing}>Add variant</Button>
                    <Button type="button" variant="ghost" size="sm" onClick={onDone}>Cancel</Button>
                </div>
            </div>
        </form>
    );
}

// ── Main edit page ──────────────────────────────────────────────────────────
export default function ProductsEdit() {
    const { product, variants, brands, attributeSets, categories, assignedCategories, typeOptions, statusOptions, visibilityOptions } = usePage<Props>().props;

    const [showAddVariant, setShowAddVariant] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        _method:           'PUT',
        name:              product.name,
        type:              product.type,
        status:            product.status,
        visibility:        product.visibility ?? 'catalog_search',
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
        main_image:        null as File | null,
        category_ids:      assignedCategories as string[],
    });

    const [imagePreview, setImagePreview] = useState<string | null>(null);
    const fileInputRef = useRef<HTMLInputElement>(null);

    function handleImage(e: React.ChangeEvent<HTMLInputElement>) {
        const file = e.target.files?.[0] ?? null;
        setData('main_image', file);
        if (file) {
            const reader = new FileReader();
            reader.onload = (ev) => setImagePreview(ev.target?.result as string);
            reader.readAsDataURL(file);
        } else {
            setImagePreview(null);
        }
    }

    function removeNewImage() {
        setData('main_image', null);
        setImagePreview(null);
        if (fileInputRef.current) fileInputRef.current.value = '';
    }

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post(`/admin/products/${product.id}`, { forceFormData: true });
    }

    function destroy() {
        if (!confirm(`Delete "${product.name}"? This cannot be undone.`)) return;
        router.delete(`/admin/products/${product.id}`);
    }

    const showWeight = !NO_WEIGHT_TYPES.includes(data.type);
    const currentImageUrl = product.main_image ? `/storage/${product.main_image}` : null;

    function toggleCategory(id: string) {
        setData('category_ids', data.category_ids.includes(id)
            ? data.category_ids.filter(c => c !== id)
            : [...data.category_ids, id]);
    }
    const displayImage = imagePreview ?? currentImageUrl;

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
                                <Input id="name" value={data.name} onChange={(e) => setData('name', e.target.value)} autoFocus />
                                <InputError message={errors.name} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="url_key">URL key</Label>
                                <Input id="url_key" value={data.url_key} onChange={(e) => setData('url_key', e.target.value)} />
                                <p className="text-muted-foreground text-xs">Changing the URL key will break existing links.</p>
                                <InputError message={errors.url_key} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="short_description">Short description</Label>
                                <Textarea id="short_description" value={data.short_description} onChange={(e) => setData('short_description', e.target.value)} rows={2} />
                                <InputError message={errors.short_description} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="description">Description</Label>
                                <Textarea id="description" value={data.description} onChange={(e) => setData('description', e.target.value)} rows={5} />
                                <InputError message={errors.description} />
                            </div>
                        </CardContent>
                    </Card>

                    {/* Image */}
                    <Card>
                        <CardHeader><CardTitle>Product image</CardTitle></CardHeader>
                        <CardContent>
                            {displayImage ? (
                                <div className="relative w-40 h-40">
                                    <img src={displayImage} alt="Product" className="w-full h-full object-cover rounded-md border" />
                                    {imagePreview && (
                                        <button type="button" onClick={removeNewImage} className="absolute -top-2 -right-2 rounded-full bg-destructive text-destructive-foreground p-0.5 shadow">
                                            <X className="h-3.5 w-3.5" />
                                        </button>
                                    )}
                                    {!imagePreview && (
                                        <label htmlFor="main_image" className="absolute inset-0 flex items-center justify-center bg-black/40 rounded-md opacity-0 hover:opacity-100 cursor-pointer transition-opacity">
                                            <span className="text-white text-xs font-medium">Replace</span>
                                        </label>
                                    )}
                                </div>
                            ) : (
                                <label htmlFor="main_image" className="flex flex-col items-center justify-center w-40 h-40 border-2 border-dashed rounded-md cursor-pointer hover:bg-muted/50 transition-colors">
                                    <ImagePlus className="h-8 w-8 text-muted-foreground mb-2" />
                                    <span className="text-xs text-muted-foreground">Upload image</span>
                                </label>
                            )}
                            <input ref={fileInputRef} id="main_image" type="file" accept="image/*" className="sr-only" onChange={handleImage} />
                            <InputError message={errors.main_image} />
                            <p className="text-xs text-muted-foreground mt-2">JPEG, PNG, WebP — max 5 MB</p>
                        </CardContent>
                    </Card>

                    {/* Pricing */}
                    <Card>
                        <CardHeader><CardTitle>Pricing</CardTitle></CardHeader>
                        <CardContent>
                            <div className="grid grid-cols-3 gap-4">
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="base_price">Price</Label>
                                    <Input id="base_price" type="number" step="0.01" min="0" value={data.base_price} onChange={(e) => setData('base_price', e.target.value)} />
                                    <InputError message={errors.base_price} />
                                </div>
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="compare_price">Compare at</Label>
                                    <Input id="compare_price" type="number" step="0.01" min="0" value={data.compare_price} onChange={(e) => setData('compare_price', e.target.value)} />
                                    <InputError message={errors.compare_price} />
                                </div>
                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="cost_price">Cost</Label>
                                    <Input id="cost_price" type="number" step="0.01" min="0" value={data.cost_price} onChange={(e) => setData('cost_price', e.target.value)} />
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

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="visibility">Visibility</Label>
                                <Select value={data.visibility} onValueChange={(v) => setData('visibility', v)}>
                                    <SelectTrigger id="visibility"><SelectValue /></SelectTrigger>
                                    <SelectContent>
                                        {visibilityOptions.map((v) => <SelectItem key={v.value} value={v.value}>{v.label}</SelectItem>)}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.visibility} />
                            </div>

                            {showWeight && (
                                <div className="grid grid-cols-3 gap-4">
                                    <div className="col-span-2 flex flex-col gap-1.5">
                                        <Label htmlFor="weight">Weight</Label>
                                        <Input id="weight" type="number" step="0.001" min="0" value={data.weight} onChange={(e) => setData('weight', e.target.value)} />
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
                            )}

                            <div className="flex items-center gap-3">
                                <Switch id="is_featured" checked={data.is_featured} onCheckedChange={(v) => setData('is_featured', v)} />
                                <Label htmlFor="is_featured">Featured product</Label>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Categories */}
                    {categories.length > 0 && (
                        <Card>
                            <CardHeader><CardTitle>Categories</CardTitle></CardHeader>
                            <CardContent>
                                <div className="border rounded-md divide-y max-h-56 overflow-y-auto">
                                    {categories.map(cat => (
                                        <label key={cat.id} className="flex items-center gap-3 px-3 py-2 cursor-pointer hover:bg-muted/50">
                                            <input
                                                type="checkbox"
                                                checked={data.category_ids.includes(cat.id)}
                                                onChange={() => toggleCategory(cat.id)}
                                                className="h-4 w-4"
                                            />
                                            <span className="text-sm">{cat.label}</span>
                                        </label>
                                    ))}
                                </div>
                            </CardContent>
                        </Card>
                    )}

                    <div className="flex gap-3">
                        <Button type="submit" disabled={processing}>
                            {processing ? 'Saving…' : 'Save changes'}
                        </Button>
                        <Button type="button" variant="outline" asChild>
                            <Link href="/admin/products">Cancel</Link>
                        </Button>
                    </div>
                </form>

                {/* Variants (configurable only) */}
                {product.type === 'configurable' && (
                    <Card className="max-w-2xl">
                        <CardHeader className="flex flex-row items-center justify-between space-y-0">
                            <div>
                                <CardTitle>Variants</CardTitle>
                                <p className="text-xs text-muted-foreground mt-1">
                                    Each variant inherits the parent price/weight unless overridden.
                                </p>
                            </div>
                            <Button variant="outline" size="sm" onClick={() => setShowAddVariant(v => !v)}>
                                <Plus className="h-4 w-4 mr-1" />
                                Add variant
                            </Button>
                        </CardHeader>
                        <CardContent>
                            {variants.length > 0 ? (
                                <div className="rounded-md border">
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>SKU</TableHead>
                                                <TableHead>Name / Options</TableHead>
                                                <TableHead className="text-right">Price</TableHead>
                                                <TableHead className="text-right">Weight</TableHead>
                                                <TableHead>Status</TableHead>
                                                <TableHead className="text-right">Actions</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {variants.map(v => (
                                                <VariantRow key={v.id} variant={v} productId={product.id} />
                                            ))}
                                        </TableBody>
                                    </Table>
                                </div>
                            ) : (
                                <p className="text-sm text-muted-foreground py-4 text-center">No variants yet. Add your first variant above.</p>
                            )}

                            {showAddVariant && (
                                <AddVariantForm productId={product.id} onDone={() => setShowAddVariant(false)} />
                            )}
                        </CardContent>
                    </Card>
                )}

                {/* Danger zone */}
                <Card className="max-w-2xl border-destructive/50">
                    <CardHeader><CardTitle className="text-destructive text-base">Danger zone</CardTitle></CardHeader>
                    <CardContent>
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm font-medium">Delete this product</p>
                                <p className="text-muted-foreground text-xs">Permanently removes the product and all its variants.</p>
                            </div>
                            <Button type="button" variant="destructive" size="sm" onClick={destroy}>Delete</Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
