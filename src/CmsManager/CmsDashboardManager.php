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
use Sonata\DashboardBundle\Exception\DashboardNotFoundException;
use Sonata\DashboardBundle\Model\BlockInteractorInterface;
use Sonata\DashboardBundle\Model\DashboardInterface;
use Sonata\DashboardBundle\Model\DashboardManagerInterface;

/**
 * The CmsDashboardManager class is in charge of retrieving the dashboard.
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
final class CmsDashboardManager extends BaseCmsDashboardManager
{
    /**
     * @var BlockInteractorInterface
     */
    private $blockInteractor;

    /**
     * @var DashboardManagerInterface
     */
    private $dashboardManager;

    /**
     * @var array
     */
    private $dashboardReferences = [];

    /**
     * @var DashboardInterface[]
     */
    private $dashboards = [];

    /**
     * @param DashboardManagerInterface $dashboardManager
     * @param BlockInteractorInterface  $blockInteractor
     */
    public function __construct(DashboardManagerInterface $dashboardManager, BlockInteractorInterface $blockInteractor)
    {
        $this->dashboardManager = $dashboardManager;
        $this->blockInteractor = $blockInteractor;
    }

    /**
     * {@inheritdoc}
     */
    public function getDashboard($dashboard): DashboardInterface
    {
        if (is_string($dashboard)) {
            $dashboard = $this->getDashboardByName($dashboard);
        } elseif (is_numeric($dashboard)) {
            $dashboard = $this->getDashboardById($dashboard);
        } elseif (!$dashboard) { // get the current dashboard
            $dashboard = $this->getCurrentDashboard();
        }

        if (!$dashboard instanceof DashboardInterface) {
            throw new DashboardNotFoundException('Unable to retrieve the dashboard');
        }

        return $dashboard;
    }

    /**
     * {@inheritdoc}
     */
    public function findContainer(string $name, DashboardInterface $dashboard, BlockInterface $parentContainer = null): ?BlockInterface
    {
        $container = null;

        if ($parentContainer) {
            // parent container is set, nothing to find, don't need to loop across the
            // name to find the correct container (main template level)
            $container = $parentContainer;
        }

        // first level blocks are containers
        if (!$container && $dashboard->getBlocks()) {
            foreach ($dashboard->getBlocks() as $block) {
                if ($block->getSetting('code') == $name) {
                    $container = $block;
                    break;
                }
            }
        }

        if (!$container) {
            $container = $this->blockInteractor->createNewContainer([
                'enabled' => true,
                'dashboard' => $dashboard,
                'code' => $name,
                'position' => 1,
                'parent' => $parentContainer,
            ]);
        }

        return $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlock(int $id)
    {
        if (!isset($this->blocks[$id])) {
            $this->blocks[$id] = $this->blockInteractor->getBlock($id);
        }

        return $this->blocks[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function getDashboardBy(string $fieldName, $value): DashboardInterface
    {
        if ('id' === $fieldName) {
            $id = $value;
        } elseif (isset($this->dashboardReferences[$fieldName][$value])) {
            $id = $this->dashboardReferences[$fieldName][$value];
        } else {
            $id = null;
        }

        if (null === $id || !isset($this->dashboards[$id])) {
            $this->dashboards[$id] = false;

            $parameters = [
                $fieldName => $value,
            ];

            $dashboard = $this->dashboardManager->findOneBy($parameters);

            if (!$dashboard) {
                throw new DashboardNotFoundException(sprintf('Unable to find the dashboard : %s = %s', $fieldName, $value));
            }

            $this->loadBlocks($dashboard);
            $id = $dashboard->getId();

            if ('id' !== $fieldName) {
                $this->dashboardReferences[$fieldName][$value] = $id;
            }

            $this->dashboards[$id] = $dashboard;
        }

        return $this->dashboards[$id];
    }

    /**
     * load all the related nested blocks linked to one dashboard.
     *
     * @param DashboardInterface $dashboard
     */
    private function loadBlocks(DashboardInterface $dashboard): void
    {
        $blocks = $this->blockInteractor->loadDashboardBlocks($dashboard);

        // save a local cache
        foreach ($blocks as $block) {
            $this->blocks[$block->getId()] = $block;
        }
    }
}
