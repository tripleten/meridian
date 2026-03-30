<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog\Presentation\Admin\Products
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Catalog\Presentation\Admin\Products;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Meridian\Catalog\Application\Commands\ChangeProductStatusCommand;
use Meridian\Catalog\Application\Commands\ChangeProductStatusHandler;
use Meridian\Catalog\Application\Commands\CreateProductCommand;
use Meridian\Catalog\Application\Commands\CreateProductHandler;
use Meridian\Catalog\Application\Commands\UpdateProductCommand;
use Meridian\Catalog\Application\Commands\UpdateProductHandler;
use Meridian\Catalog\Application\Queries\GetProductHandler;
use Meridian\Catalog\Application\Queries\GetProductQuery;
use Meridian\Catalog\Application\Queries\ListProductsHandler;
use Meridian\Catalog\Application\Queries\ListProductsQuery;
use Meridian\Catalog\Domain\Product\ProductStatus;
use Meridian\Catalog\Domain\Product\ProductType;
use Meridian\Catalog\Infrastructure\Persistence\EloquentAttributeSet;
use Meridian\Catalog\Infrastructure\Persistence\EloquentBrand;
use Meridian\Catalog\Infrastructure\Persistence\EloquentBrandRepository;
use Meridian\Catalog\Infrastructure\Persistence\EloquentProduct;
use Meridian\Shared\Domain\Exceptions\DomainException;
use Meridian\Shared\Domain\Exceptions\InvalidStateTransition;

final class ProductController
{
    public function index(Request $request, ListProductsHandler $handler): Response
    {
        $brands = EloquentBrand::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('admin/products/index', [
            'products'    => $handler->handle(new ListProductsQuery(
                search:   $request->string('search')->trim()->value(),
                status:   $request->string('status')->value(),
                type:     $request->string('type')->value(),
                brand_id: $request->string('brand_id')->value() ?: null,
                perPage:  20,
            )),
            'filters'      => $request->only('search', 'status', 'type', 'brand_id'),
            'statusOptions' => $this->statusOptions(),
            'typeOptions'   => $this->typeOptions(),
            'brands'        => $brands,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/products/create', [
            'brands'        => EloquentBrand::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'attributeSets' => EloquentAttributeSet::orderBy('name')->get(['id', 'name']),
            'typeOptions'   => $this->typeOptions(),
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function store(Request $request, CreateProductHandler $handler): RedirectResponse
    {
        $validated = $request->validate($this->productRules());

        try {
            $handler->handle(new CreateProductCommand(
                name:             $validated['name'],
                sku:              $validated['sku'],
                type:             $validated['type'],
                status:           $validated['status'] ?? 'draft',
                base_price:       (int) round($validated['base_price'] * 100),
                compare_price:    isset($validated['compare_price']) ? (int) round($validated['compare_price'] * 100) : null,
                cost_price:       isset($validated['cost_price']) ? (int) round($validated['cost_price'] * 100) : null,
                brand_id:         $validated['brand_id'] ?? null,
                attribute_set_id: $validated['attribute_set_id'] ?? null,
                tax_class_id:     $validated['tax_class_id'] ?? null,
                url_key:          $validated['url_key'],
                short_description: $validated['short_description'] ?? null,
                description:      $validated['description'] ?? null,
                weight:           isset($validated['weight']) ? (float) $validated['weight'] : null,
                weight_unit:      $validated['weight_unit'] ?? 'kg',
                is_featured:      $validated['is_featured'] ?? false,
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['sku' => $e->getMessage()]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(string $product, GetProductHandler $handler): Response
    {
        return Inertia::render('admin/products/edit', [
            'product'       => $handler->handle(new GetProductQuery($product)),
            'brands'        => EloquentBrand::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'attributeSets' => EloquentAttributeSet::orderBy('name')->get(['id', 'name']),
            'typeOptions'   => $this->typeOptions(),
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function update(string $product, Request $request, UpdateProductHandler $handler): RedirectResponse
    {
        $rules = $this->productRules();
        // SKU is immutable after creation — remove unique rule
        unset($rules['sku']);

        $validated = $request->validate($rules);

        try {
            $handler->handle(new UpdateProductCommand(
                productId:        $product,
                name:             $validated['name'],
                type:             $validated['type'],
                status:           $validated['status'] ?? 'draft',
                base_price:       (int) round($validated['base_price'] * 100),
                compare_price:    isset($validated['compare_price']) ? (int) round($validated['compare_price'] * 100) : null,
                cost_price:       isset($validated['cost_price']) ? (int) round($validated['cost_price'] * 100) : null,
                brand_id:         $validated['brand_id'] ?? null,
                attribute_set_id: $validated['attribute_set_id'] ?? null,
                tax_class_id:     $validated['tax_class_id'] ?? null,
                url_key:          $validated['url_key'],
                short_description: $validated['short_description'] ?? null,
                description:      $validated['description'] ?? null,
                weight:           isset($validated['weight']) ? (float) $validated['weight'] : null,
                weight_unit:      $validated['weight_unit'] ?? 'kg',
                is_featured:      $validated['is_featured'] ?? false,
            ));
        } catch (DomainException | InvalidStateTransition $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(string $product): RedirectResponse
    {
        EloquentProduct::findOrFail($product)->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    private function productRules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:250'],
            'sku'               => ['required', 'string', 'max:100', 'unique:products,sku'],
            'type'              => ['required', 'string', 'in:' . implode(',', array_column(ProductType::cases(), 'value'))],
            'status'            => ['nullable', 'string', 'in:' . implode(',', array_column(ProductStatus::cases(), 'value'))],
            'base_price'        => ['required', 'numeric', 'min:0'],
            'compare_price'     => ['nullable', 'numeric', 'min:0'],
            'cost_price'        => ['nullable', 'numeric', 'min:0'],
            'brand_id'          => ['nullable', 'string', 'exists:brands,id'],
            'attribute_set_id'  => ['nullable', 'string', 'exists:attribute_sets,id'],
            'tax_class_id'      => ['nullable', 'string', 'exists:tax_classes,id'],
            'url_key'           => ['required', 'string', 'max:270', 'regex:/^[a-z0-9-]+$/'],
            'short_description' => ['nullable', 'string'],
            'description'       => ['nullable', 'string'],
            'weight'            => ['nullable', 'numeric', 'min:0'],
            'weight_unit'       => ['nullable', 'string', 'in:kg,g,lb,oz'],
            'is_featured'       => ['nullable', 'boolean'],
        ];
    }

    private function statusOptions(): array
    {
        return array_map(
            fn (ProductStatus $s) => ['value' => $s->value, 'label' => $s->label()],
            ProductStatus::cases(),
        );
    }

    private function typeOptions(): array
    {
        return array_map(
            fn (ProductType $t) => ['value' => $t->value, 'label' => $t->label()],
            ProductType::cases(),
        );
    }
}
