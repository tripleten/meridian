<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Inventory\Presentation\Admin
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Inventory\Presentation\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Meridian\Inventory\Application\Commands\CreateInventorySourceCommand;
use Meridian\Inventory\Application\Commands\CreateInventorySourceHandler;
use Meridian\Inventory\Application\Commands\UpdateInventorySourceCommand;
use Meridian\Inventory\Application\Commands\UpdateInventorySourceHandler;
use Meridian\Inventory\Application\DTOs\InventorySourceData;
use Meridian\Inventory\Application\Queries\ListInventorySourcesHandler;
use Meridian\Inventory\Infrastructure\Persistence\EloquentInventorySource;
use Meridian\Shared\Domain\Exceptions\DomainException;

final class InventorySourceController
{
    public function index(ListInventorySourcesHandler $handler): Response
    {
        return Inertia::render('admin/inventory/sources/index', [
            'sources'     => $handler->handle(),
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/inventory/sources/create', [
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    public function store(Request $request, CreateInventorySourceHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'code'          => ['required', 'string', 'max:50', 'unique:inventory_sources,code', 'regex:/^[a-z0-9_-]+$/'],
            'type'          => ['required', 'string', 'in:warehouse,store,dropship'],
            'address_line1' => ['nullable', 'string', 'max:200'],
            'city'          => ['nullable', 'string', 'max:100'],
            'country_code'  => ['nullable', 'string', 'size:2'],
            'is_active'     => ['nullable', 'boolean'],
            'is_default'    => ['nullable', 'boolean'],
            'priority'      => ['nullable', 'integer', 'min:0'],
        ]);

        try {
            $handler->handle(new CreateInventorySourceCommand(
                name:          $validated['name'],
                code:          $validated['code'],
                type:          $validated['type'],
                address_line1: $validated['address_line1'] ?? null,
                city:          $validated['city'] ?? null,
                country_code:  $validated['country_code'] ?? null,
                is_active:     (bool) ($validated['is_active'] ?? true),
                is_default:    (bool) ($validated['is_default'] ?? false),
                priority:      (int) ($validated['priority'] ?? 0),
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        return redirect()->route('admin.inventory.sources.index')->with('success', 'Source created.');
    }

    public function edit(string $source): Response
    {
        $model = EloquentInventorySource::findOrFail($source);

        return Inertia::render('admin/inventory/sources/edit', [
            'source'      => InventorySourceData::fromModel($model),
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    public function update(string $source, Request $request, UpdateInventorySourceHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'code'          => ['required', 'string', 'max:50', "unique:inventory_sources,code,{$source}", 'regex:/^[a-z0-9_-]+$/'],
            'type'          => ['required', 'string', 'in:warehouse,store,dropship'],
            'address_line1' => ['nullable', 'string', 'max:200'],
            'city'          => ['nullable', 'string', 'max:100'],
            'country_code'  => ['nullable', 'string', 'size:2'],
            'is_active'     => ['nullable', 'boolean'],
            'is_default'    => ['nullable', 'boolean'],
            'priority'      => ['nullable', 'integer', 'min:0'],
        ]);

        try {
            $handler->handle(new UpdateInventorySourceCommand(
                sourceId:      $source,
                name:          $validated['name'],
                code:          $validated['code'],
                type:          $validated['type'],
                address_line1: $validated['address_line1'] ?? null,
                city:          $validated['city'] ?? null,
                country_code:  $validated['country_code'] ?? null,
                is_active:     (bool) ($validated['is_active'] ?? true),
                is_default:    (bool) ($validated['is_default'] ?? false),
                priority:      (int) ($validated['priority'] ?? 0),
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        return redirect()->route('admin.inventory.sources.index')->with('success', 'Source updated.');
    }

    private function typeOptions(): array
    {
        return [
            ['value' => 'warehouse', 'label' => 'Warehouse'],
            ['value' => 'store',     'label' => 'Store'],
            ['value' => 'dropship',  'label' => 'Dropship'],
        ];
    }
}
