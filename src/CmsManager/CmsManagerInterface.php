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
use Sonata\DashboardBundle\Model\DashboardBlockInterface;
use Sonata\DashboardBundle\Model\DashboardInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * The CmsManagerInterface class is in charge of retrieving the correct dashboard.
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface CmsManagerInterface
{
    public function findContainer(string $name, DashboardInterface $dashboard, ?BlockInterface $parentContainer = null): ?BlockInterface;

    /**
     * Returns a fully loaded dashboard (+ blocks) from a name.
     */
    public function getDashboardByName(string $name): DashboardInterface;

    /**
     * Returns a fully loaded page (+ blocks) from a dashboard id.
     */
    public function getDashboardById(int $id): DashboardInterface;

    public function getBlock(int $id): DashboardBlockInterface;

    public function getCurrentDashboard(): ?DashboardInterface;

    public function setCurrentDashboard(DashboardInterface $dashboard): void;

    /**
     * Returns the list of loaded block from the current http request.
     *
     * @return BlockInterface[]|iterable
     */
    public function getBlocks(): iterable;

    public function getDashboard($dashboard): DashboardInterface;
}
