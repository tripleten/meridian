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

use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Meridian\Catalog\Application\Commands\CreateProductVariantCommand;
use Meridian\Catalog\Application\Commands\CreateProductVariantHandler;
use Meridian\Catalog\Application\Commands\UpdateProductVariantCommand;
use Meridian\Catalog\Application\Commands\UpdateProductVariantHandler;
use Meridian\Catalog\Infrastructure\Persistence\EloquentProduct;
use Meridian\Catalog\Infrastructure\Persistence\EloquentProductVariant;

final class ProductVariantController
{
    public function store(
        string $product,
        Request $request,
        CreateProductVariantHandler $handler,
    ): RedirectResponse {
        $validated = $request->validate($this->rules());

        try {
            $handler->handle(new CreateProductVariantCommand(
                productId:     $product,
                sku:           $validated['sku'],
                name:          $validated['name'] ?: null,
                price:         isset($validated['price']) ? (int) round($validated['price'] * 100) : null,
                compare_price: isset($validated['compare_price']) ? (int) round($validated['compare_price'] * 100) : null,
                cost_price:    isset($validated['cost_price']) ? (int) round($validated['cost_price'] * 100) : null,
                weight:        isset($validated['weight']) ? (float) $validated['weight'] : null,
                is_active:     $validated['is_active'] ?? true,
                sort_order:    (int) ($validated['sort_order'] ?? 0),
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['sku' => $e->getMessage()]);
        }

        return redirect()->to("/admin/products/{$product}/edit")->with('success', 'Variant added.');
    }

    public function update(
        string $product,
        string $variant,
        Request $request,
        UpdateProductVariantHandler $handler,
    ): RedirectResponse {
        $validated = $request->validate($this->rules());

        try {
            $handler->handle(new UpdateProductVariantCommand(
                variantId:     $variant,
                sku:           $validated['sku'],
                name:          $validated['name'] ?: null,
                price:         isset($validated['price']) ? (int) round($validated['price'] * 100) : null,
                compare_price: isset($validated['compare_price']) ? (int) round($validated['compare_price'] * 100) : null,
                cost_price:    isset($validated['cost_price']) ? (int) round($validated['cost_price'] * 100) : null,
                weight:        isset($validated['weight']) ? (float) $validated['weight'] : null,
                is_active:     $validated['is_active'] ?? true,
                sort_order:    (int) ($validated['sort_order'] ?? 0),
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['sku' => $e->getMessage()]);
        }

        return redirect()->to("/admin/products/{$product}/edit")->with('success', 'Variant updated.');
    }

    public function destroy(string $product, string $variant): RedirectResponse
    {
        EloquentProductVariant::where('id', $variant)
            ->where('product_id', $product)
            ->delete();

        return redirect()->to("/admin/products/{$product}/edit")->with('success', 'Variant deleted.');
    }

    private function rules(): array
    {
        return [
            'sku'           => ['required', 'string', 'max:100'],
            'name'          => ['nullable', 'string', 'max:250'],
            'price'         => ['nullable', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0'],
            'cost_price'    => ['nullable', 'numeric', 'min:0'],
            'weight'        => ['nullable', 'numeric', 'min:0'],
            'is_active'     => ['nullable', 'boolean'],
            'sort_order'    => ['nullable', 'integer', 'min:0'],
        ];
    }
}
