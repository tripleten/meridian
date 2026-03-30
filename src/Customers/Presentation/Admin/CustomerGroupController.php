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

use Inertia\Inertia;
use Inertia\Response;
use Meridian\Customers\Application\DTOs\CustomerGroupData;
use Meridian\Customers\Domain\Repositories\CustomerGroupRepositoryInterface;

final class CustomerGroupController
{
    public function index(CustomerGroupRepositoryInterface $groups): Response
    {
        return Inertia::render('admin/customer-groups/index', [
            'groups' => array_map(
                fn ($group) => CustomerGroupData::fromModel($group),
                $groups->all(),
            ),
        ]);
    }
}
