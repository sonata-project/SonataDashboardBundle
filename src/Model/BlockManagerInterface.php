<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundle\Model;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Model\ManagerInterface;
use Sonata\CoreBundle\Model\PageableManagerInterface;

interface BlockManagerInterface extends ManagerInterface, PageableManagerInterface
{
    /**
     * Updates position for given block.
     *
     * @param int  $id          Block Id
     * @param int  $position    New Position
     * @param int  $parentId    Parent block Id (needed when partial = true)
     * @param int  $dashboardId Dashboard Id (needed when partial = true)
     * @param bool $partial     Should we use partial references? (Better for performance, but can lead to query issues.)
     *
     * @return BlockInterface
     */
    public function updatePosition(int $id, int $position, ?int $parentId = null, ?int $dashboardId = null, bool $partial = true): ?BlockInterface;
}
