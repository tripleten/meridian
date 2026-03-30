<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\CmsSeo\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\CmsSeo\Application\Commands;

use Meridian\CmsSeo\Domain\Repositories\CmsBlockRepositoryInterface;
use Meridian\Shared\Domain\Exceptions\DomainException;

final readonly class UpdateCmsBlockHandler
{
    public function __construct(
        private CmsBlockRepositoryInterface $blocks,
    ) {}

    public function handle(UpdateCmsBlockCommand $command): void
    {
        $block = $this->blocks->findById($command->blockId);

        if ($block === null) {
            throw new DomainException("CMS block '{$command->blockId}' not found.");
        }

        $block->channel_id  = $command->channel_id;
        $block->identifier  = $command->identifier;
        $block->title       = $command->title;
        $block->content     = $command->content;
        $block->is_active   = $command->is_active;

        $this->blocks->save($block);
    }
}
