<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog\Presentation\Admin\Brands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Catalog\Presentation\Admin\Brands;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Meridian\Catalog\Application\Commands\CreateBrandCommand;
use Meridian\Catalog\Application\Commands\CreateBrandHandler;
use Meridian\Catalog\Application\Commands\UpdateBrandCommand;
use Meridian\Catalog\Application\Commands\UpdateBrandHandler;
use Meridian\Catalog\Application\Queries\ListBrandsHandler;
use Meridian\Catalog\Application\Queries\ListBrandsQuery;
use Meridian\Catalog\Infrastructure\Persistence\EloquentBrand;
use Meridian\Shared\Domain\Exceptions\DomainException;

final class BrandController
{
    public function index(Request $request, ListBrandsHandler $handler): Response
    {
        return Inertia::render('admin/brands/index', [
            'brands'  => $handler->handle(new ListBrandsQuery(
                search:  $request->string('search')->trim()->value(),
                perPage: 20,
            )),
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/brands/create');
    }

    public function store(Request $request, CreateBrandHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'slug'        => ['required', 'string', 'max:160', 'unique:brands,slug', 'regex:/^[a-z0-9-]+$/'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_active'   => ['boolean'],
        ]);

        try {
            $handler->handle(new CreateBrandCommand(
                name:        $validated['name'],
                slug:        $validated['slug'],
                description: $validated['description'] ?? null,
                is_active:   $validated['is_active'] ?? true,
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['name' => $e->getMessage()]);
        }

        return redirect()->route('admin.brands.index')->with('success', 'Brand created.');
    }

    public function edit(string $brand): Response
    {
        $model = EloquentBrand::findOrFail($brand);

        return Inertia::render('admin/brands/edit', [
            'brand' => [
                'id'          => $model->id,
                'name'        => $model->name,
                'slug'        => $model->slug,
                'description' => $model->description,
                'is_active'   => $model->is_active,
            ],
        ]);
    }

    public function update(string $brand, Request $request, UpdateBrandHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'slug'        => ['required', 'string', 'max:160', "unique:brands,slug,{$brand}", 'regex:/^[a-z0-9-]+$/'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_active'   => ['boolean'],
        ]);

        try {
            $handler->handle(new UpdateBrandCommand(
                brandId:     $brand,
                name:        $validated['name'],
                slug:        $validated['slug'],
                description: $validated['description'] ?? null,
                is_active:   $validated['is_active'] ?? true,
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['name' => $e->getMessage()]);
        }

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated.');
    }

    public function destroy(string $brand): RedirectResponse
    {
        EloquentBrand::findOrFail($brand)->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted.');
    }
}
