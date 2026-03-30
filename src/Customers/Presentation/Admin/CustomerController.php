<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Customers\Presentation\Admin
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Customers\Presentation\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Meridian\Customers\Application\Commands\UpdateCustomerCommand;
use Meridian\Customers\Application\Commands\UpdateCustomerHandler;
use Meridian\Customers\Application\DTOs\CustomerAddressData;
use Meridian\Customers\Application\DTOs\CustomerData;
use Meridian\Customers\Application\DTOs\CustomerGroupData;
use Meridian\Customers\Application\Queries\GetCustomerHandler;
use Meridian\Customers\Application\Queries\GetCustomerQuery;
use Meridian\Customers\Application\Queries\ListCustomersHandler;
use Meridian\Customers\Application\Queries\ListCustomersQuery;
use Meridian\Customers\Infrastructure\Persistence\EloquentCustomerGroup;
use Meridian\Customers\Domain\Repositories\CustomerGroupRepositoryInterface;
use Meridian\Shared\Domain\Exceptions\DomainException;

final class CustomerController
{
    public function index(Request $request, ListCustomersHandler $handler): Response
    {
        $isActive = null;
        if ($request->filled('is_active')) {
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        $customers = $handler->handle(new ListCustomersQuery(
            search:    $request->string('search')->trim()->value(),
            group_id:  $request->string('group_id')->value(),
            is_active: $isActive,
            perPage:   20,
        ));

        return Inertia::render('admin/customers/index', [
            'customers' => $customers,
            'filters'   => $request->only('search', 'group_id', 'is_active'),
            'groups'    => EloquentCustomerGroup::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(string $customer, GetCustomerHandler $handler): Response
    {
        $model = $handler->handle(new GetCustomerQuery($customer));

        return Inertia::render('admin/customers/show', [
            'customer'  => CustomerData::fromModel($model),
            'addresses' => $model->addresses
                ->map(fn ($address) => CustomerAddressData::fromModel($address))
                ->values()
                ->all(),
        ]);
    }

    public function edit(
        string $customer,
        GetCustomerHandler $handler,
        CustomerGroupRepositoryInterface $groups,
    ): Response {
        $model = $handler->handle(new GetCustomerQuery($customer));

        return Inertia::render('admin/customers/edit', [
            'customer' => CustomerData::fromModel($model),
            'groups'   => array_map(
                fn ($group) => CustomerGroupData::fromModel($group),
                $groups->all(),
            ),
        ]);
    }

    public function update(
        string $customer,
        Request $request,
        UpdateCustomerHandler $handler,
    ): RedirectResponse {
        $validated = $request->validate([
            'customer_group_id'           => ['nullable', 'string'],
            'first_name'                  => ['required', 'string', 'max:255'],
            'last_name'                   => ['required', 'string', 'max:255'],
            'phone'                       => ['nullable', 'string', 'max:50'],
            'company'                     => ['nullable', 'string', 'max:255'],
            'gender'                      => ['nullable', 'string', 'in:male,female,prefer_not_to_say'],
            'is_active'                   => ['boolean'],
            'is_subscribed_to_newsletter' => ['boolean'],
        ]);

        try {
            $handler->handle(new UpdateCustomerCommand(
                customerId:                  $customer,
                customer_group_id:           $validated['customer_group_id'] ?? null,
                first_name:                  $validated['first_name'],
                last_name:                   $validated['last_name'],
                phone:                       $validated['phone'] ?? null,
                company:                     $validated['company'] ?? null,
                gender:                      $validated['gender'] ?? null,
                is_active:                   (bool) ($validated['is_active'] ?? true),
                is_subscribed_to_newsletter: (bool) ($validated['is_subscribed_to_newsletter'] ?? false),
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['customer' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Customer updated.');
    }
}
