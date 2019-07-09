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

/**
 * Base class CMS Manager.
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
abstract class BaseCmsDashboardManager implements CmsManagerInterface
{
    /**
     * @var BlockInterface[]
     */
    protected $blocks = [];
    /**
     * @var DashboardInterface|null
     */
    private $currentDashboard;

    public function getCurrentDashboard(): ?DashboardInterface
    {
        return $this->currentDashboard;
    }

    public function setCurrentDashboard(DashboardInterface $dashboard): void
    {
        $this->currentDashboard = $dashboard;
    }

    public function getBlocks(): iterable
    {
        return $this->blocks;
    }

    public function getDashboardByName(string $name): DashboardInterface
    {
        return $this->getDashboardBy('name', $name);
    }

    public function getDashboardById(int $id): DashboardInterface
    {
        return $this->getDashboardBy('id', $id);
    }

    /**
     * @param mixed $value
     */
    abstract protected function getDashboardBy(string $fieldName, $value): DashboardInterface;
}
