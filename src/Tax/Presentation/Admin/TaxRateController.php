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
use Meridian\Tax\Application\Commands\CreateTaxRateCommand;
use Meridian\Tax\Application\Commands\CreateTaxRateHandler;
use Meridian\Tax\Application\Commands\UpdateTaxRateCommand;
use Meridian\Tax\Application\Commands\UpdateTaxRateHandler;
use Meridian\Tax\Application\DTOs\TaxRateData;
use Meridian\Tax\Infrastructure\Persistence\EloquentTaxRate;
use Meridian\Tax\Infrastructure\Persistence\EloquentTaxZone;

final class TaxRateController
{
    public function index(): Response
    {
        $rates = EloquentTaxRate::with('taxZone')
            ->orderBy('name')
            ->get()
            ->map(fn (EloquentTaxRate $m) => TaxRateData::fromModel($m));

        $zones = EloquentTaxZone::orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('admin/tax/rates/index', [
            'rates' => $rates,
            'zones' => $zones,
        ]);
    }

    public function create(): Response
    {
        $zones = EloquentTaxZone::orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('admin/tax/rates/create', [
            'zones' => $zones,
        ]);
    }

    public function store(Request $request, CreateTaxRateHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'tax_zone_id'         => ['required', 'string', 'exists:tax_zones,id'],
            'name'                => ['required', 'string', 'max:100'],
            'code'                => ['required', 'string', 'max:50', 'unique:tax_rates,code'],
            'rate'                => ['required', 'numeric', 'min:0', 'max:1'],
            'type'                => ['required', 'in:inclusive,exclusive'],
            'is_compound'         => ['boolean'],
            'is_shipping_taxable' => ['boolean'],
        ]);

        try {
            $handler->handle(new CreateTaxRateCommand(
                tax_zone_id:         $validated['tax_zone_id'],
                name:                $validated['name'],
                code:                $validated['code'],
                rate:                (float) $validated['rate'],
                type:                $validated['type'],
                is_compound:         (bool) ($validated['is_compound'] ?? false),
                is_shipping_taxable: (bool) ($validated['is_shipping_taxable'] ?? false),
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        return redirect()->route('admin.tax.rates.index')->with('success', 'Tax rate created.');
    }

    public function edit(string $taxRate): Response
    {
        $model = EloquentTaxRate::with('taxZone')->findOrFail($taxRate);

        $zones = EloquentTaxZone::orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('admin/tax/rates/edit', [
            'rate'  => TaxRateData::fromModel($model),
            'zones' => $zones,
        ]);
    }

    public function update(string $taxRate, Request $request, UpdateTaxRateHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:100'],
            'code'                => ['required', 'string', 'max:50', "unique:tax_rates,code,{$taxRate}"],
            'rate'                => ['required', 'numeric', 'min:0', 'max:1'],
            'type'                => ['required', 'in:inclusive,exclusive'],
            'is_compound'         => ['boolean'],
            'is_shipping_taxable' => ['boolean'],
        ]);

        try {
            $handler->handle(new UpdateTaxRateCommand(
                id:                  $taxRate,
                name:                $validated['name'],
                code:                $validated['code'],
                rate:                (float) $validated['rate'],
                type:                $validated['type'],
                is_compound:         (bool) ($validated['is_compound'] ?? false),
                is_shipping_taxable: (bool) ($validated['is_shipping_taxable'] ?? false),
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        return redirect()->route('admin.tax.rates.index')->with('success', 'Tax rate updated.');
    }
}
