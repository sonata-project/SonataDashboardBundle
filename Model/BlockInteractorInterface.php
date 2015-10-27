<?php

/*
 * This file is part of the Sonata project.
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
    /**
     * return a block with the given id.
     *
     * @param mixed $id
     *
     * @return DashboardBlockInterface
     */
    public function getBlock($id);

    /**
     * return a flat list if dashboard's blocks.
     *
     * @param DashboardInterface $dashboard
     *
     * @return DashboardBlockInterface[]
     */
    public function getBlocksById(DashboardInterface $dashboard);

    /**
     * load blocks attached the given dashboard.
     *
     * @param DashboardInterface $dashboard
     *
     * @return DashboardBlockInterface[] $blocks
     */
    public function loadDashboardBlocks(DashboardInterface $dashboard);

    /**
     * save the blocks positions.
     *
     * @param array $data
     * @param bool  $partial Should we use partial references? (Better for performance, but can lead to query issues.)
     *
     * @return bool
     */
    public function saveBlocksPosition(array $data = array(), $partial = true);

    /**
     * @param array    $values An array of values for container creation
     * @param \Closure $alter  A closure to alter container created
     *
     * @return BlockInterface
     */
    public function createNewContainer(array $values = array(), \Closure $alter = null);
}
