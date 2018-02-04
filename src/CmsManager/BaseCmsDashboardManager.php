<?php

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
     * @var DashboardInterface
     */
    protected $currentDashboard;

    /**
     * @var BlockInterface[]
     */
    protected $blocks = array();

    /**
     * {@inheritdoc}
     */
    public function getCurrentDashboard()
    {
        return $this->currentDashboard;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentDashboard(DashboardInterface $dashboard)
    {
        $this->currentDashboard = $dashboard;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * {@inheritdoc}
     */
    public function getDashboardByName($name)
    {
        return $this->getDashboardBy('name', $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getDashboardById($id)
    {
        return $this->getDashboardBy('id', $id);
    }

    /**
     * @param string $fieldName
     * @param mixed  $value
     *
     * @return DashboardInterface
     */
    abstract protected function getDashboardBy($fieldName, $value);
}
