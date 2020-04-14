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

/**
 * BlockInteractorInterface.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
interface BlockInteractorInterface
{
    public function getBlock(int $id): ?DashboardBlockInterface;

    /**
     * return a flat list if dashboard's blocks.
     *
     * @return DashboardBlockInterface[]
     */
    public function getBlocksById(DashboardInterface $dashboard);

    /**
     * load blocks attached the given dashboard.
     *
     * @return DashboardBlockInterface[] $blocks
     */
    public function loadDashboardBlocks(DashboardInterface $dashboard);

    /**
     * save the blocks positions.
     *
     * @param bool $partial Should we use partial references? (Better for performance, but can lead to query issues.)
     */
    public function saveBlocksPosition(array $data = [], bool $partial = true): void;

    /**
     * @param array    $values An array of values for container creation
     * @param \Closure $alter  A closure to alter container created
     */
    public function createNewContainer(array $values = [], ?\Closure $alter = null): DashboardBlockInterface;
}
