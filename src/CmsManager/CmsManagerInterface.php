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

namespace Sonata\DashboardBundle\CmsManager;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\DashboardBundle\Model\DashboardInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * The CmsManagerInterface class is in charge of retrieving the correct dashboard.
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface CmsManagerInterface
{
    /**
     * @param string              $name
     * @param DashboardInterface  $dashboard
     * @param null|BlockInterface $parentContainer
     *
     * @return bool|null|BlockInterface
     */
    public function findContainer($name, DashboardInterface $dashboard, BlockInterface $parentContainer = null);

    /**
     * Returns a fully loaded dashboard (+ blocks) from a name.
     *
     * @param string $name
     *
     * @return DashboardInterface
     */
    public function getDashboardByName($name);

    /**
     * Returns a fully loaded pag (+ blocks) from a dashboard id.
     *
     * @param int $id
     *
     * @return DashboardInterface
     */
    public function getDashboardById($id);

    /**
     * @param int $id
     *
     * @return DashboardInterface
     */
    public function getBlock($id);

    /**
     * Returns the current dashboard.
     *
     * @return DashboardInterface
     */
    public function getCurrentDashboard();

    /**
     * @param DashboardInterface $dashboard
     */
    public function setCurrentDashboard(DashboardInterface $dashboard);

    /**
     * Returns the list of loaded block from the current http request.
     *
     * @return BlockInterface[]
     */
    public function getBlocks();

    /**
     * @param string $dashboard
     *
     * @return DashboardInterface
     */
    public function getDashboard($dashboard);
}
