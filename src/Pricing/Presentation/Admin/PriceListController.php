<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Pricing\Presentation\Admin
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Pricing\Presentation\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Meridian\Customers\Infrastructure\Persistence\EloquentCustomerGroup;
use Meridian\Pricing\Application\Commands\CreatePriceListCommand;
use Meridian\Pricing\Application\Commands\CreatePriceListHandler;
use Meridian\Pricing\Application\Commands\UpdatePriceListCommand;
use Meridian\Pricing\Application\Commands\UpdatePriceListHandler;
use Meridian\Pricing\Application\DTOs\PriceListData;
use Meridian\Pricing\Application\Queries\ListPriceListsHandler;
use Meridian\Pricing\Infrastructure\Persistence\EloquentPriceList;
use Meridian\Shared\Domain\Exceptions\DomainException;

final class PriceListController
{
    public function index(ListPriceListsHandler $handler): Response
    {
        return Inertia::render('admin/pricing/price-lists/index', [
            'priceLists' => $handler->handle(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/pricing/price-lists/create', [
            'currencies'     => DB::table('currencies')->orderBy('code')->get(['code', 'name']),
            'customerGroups' => EloquentCustomerGroup::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request, CreatePriceListHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:100'],
            'code'              => ['required', 'string', 'max:50', 'unique:price_lists,code', 'regex:/^[a-z0-9_-]+$/'],
            'currency_code'     => ['required', 'string', 'size:3'],
            'customer_group_id' => ['nullable', 'string', 'exists:customer_groups,id'],
            'is_default'        => ['nullable', 'boolean'],
            'is_active'         => ['nullable', 'boolean'],
        ]);

        try {
            $handler->handle(new CreatePriceListCommand(
                name:               $validated['name'],
                code:               $validated['code'],
                currency_code:      $validated['currency_code'],
                customer_group_id:  $validated['customer_group_id'] ?? null,
                is_default:         (bool) ($validated['is_default'] ?? false),
                is_active:          (bool) ($validated['is_active'] ?? true),
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        return redirect()->route('admin.pricing.price-lists.index')->with('success', 'Price list created.');
    }

    public function edit(string $priceList): Response
    {
        $model = EloquentPriceList::findOrFail($priceList);

        return Inertia::render('admin/pricing/price-lists/edit', [
            'priceList'      => PriceListData::fromModel($model->loadCount('priceListItems')->load('customerGroup')),
            'currencies'     => DB::table('currencies')->orderBy('code')->get(['code', 'name']),
            'customerGroups' => EloquentCustomerGroup::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(string $priceList, Request $request, UpdatePriceListHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:100'],
            'code'              => ['required', 'string', 'max:50', "unique:price_lists,code,{$priceList}", 'regex:/^[a-z0-9_-]+$/'],
            'currency_code'     => ['required', 'string', 'size:3'],
            'customer_group_id' => ['nullable', 'string', 'exists:customer_groups,id'],
            'is_default'        => ['nullable', 'boolean'],
            'is_active'         => ['nullable', 'boolean'],
        ]);

        try {
            $handler->handle(new UpdatePriceListCommand(
                priceListId:       $priceList,
                name:              $validated['name'],
                code:              $validated['code'],
                currency_code:     $validated['currency_code'],
                channel_id:        null,
                customer_group_id: $validated['customer_group_id'] ?? null,
                is_default:        (bool) ($validated['is_default'] ?? false),
                is_active:         (bool) ($validated['is_active'] ?? true),
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        return redirect()->route('admin.pricing.price-lists.index')->with('success', 'Price list updated.');
    }

    public function destroy(string $priceList): RedirectResponse
    {
        EloquentPriceList::findOrFail($priceList)->delete();

        return redirect()->route('admin.pricing.price-lists.index')->with('success', 'Price list deleted.');
    }
}
