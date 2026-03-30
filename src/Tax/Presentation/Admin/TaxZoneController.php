<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Tax\Presentation\Admin
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Tax\Presentation\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Meridian\Shared\Domain\Exceptions\DomainException;
use Meridian\Tax\Application\Commands\CreateTaxZoneCommand;
use Meridian\Tax\Application\Commands\CreateTaxZoneHandler;
use Meridian\Tax\Application\Commands\UpdateTaxZoneCommand;
use Meridian\Tax\Application\Commands\UpdateTaxZoneHandler;
use Meridian\Tax\Application\DTOs\TaxZoneData;
use Meridian\Tax\Infrastructure\Persistence\EloquentTaxZone;

final class TaxZoneController
{
    public function index(): Response
    {
        $zones = EloquentTaxZone::withCount('taxRates')
            ->orderBy('name')
            ->get()
            ->map(fn (EloquentTaxZone $m) => TaxZoneData::fromModel($m));

        return Inertia::render('admin/tax/zones/index', [
            'zones' => $zones,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/tax/zones/create');
    }

    public function store(Request $request, CreateTaxZoneHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'code'      => ['required', 'string', 'max:50', 'unique:tax_zones,code'],
            'countries' => ['required', 'array'],
            'regions'   => ['nullable', 'array'],
        ]);

        try {
            $handler->handle(new CreateTaxZoneCommand(
                name:      $validated['name'],
                code:      $validated['code'],
                countries: $validated['countries'],
                regions:   $validated['regions'] ?? null,
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        return redirect()->route('admin.tax.zones.index')->with('success', 'Tax zone created.');
    }

    public function edit(string $zone): Response
    {
        $model = EloquentTaxZone::findOrFail($zone);

        return Inertia::render('admin/tax/zones/edit', [
            'zone' => TaxZoneData::fromModel($model),
        ]);
    }

    public function update(string $zone, Request $request, UpdateTaxZoneHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'code'      => ['required', 'string', 'max:50', "unique:tax_zones,code,{$zone}"],
            'countries' => ['required', 'array'],
            'regions'   => ['nullable', 'array'],
        ]);

        try {
            $handler->handle(new UpdateTaxZoneCommand(
                id:        $zone,
                name:      $validated['name'],
                code:      $validated['code'],
                countries: $validated['countries'],
                regions:   $validated['regions'] ?? null,
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        return redirect()->route('admin.tax.zones.index')->with('success', 'Tax zone updated.');
    }
}
