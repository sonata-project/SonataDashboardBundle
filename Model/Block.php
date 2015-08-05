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

use Sonata\BlockBundle\Model\BaseBlock;
use Sonata\BlockBundle\Model\BlockInterface;

/**
 * Block.
 *
 * @author Quentin Somazzi <qsomazzi@ekino.com>
 */
abstract class Block extends BaseBlock implements DashboardBlockInterface
{
    /**
     * @var DashboardInterface
     */
    protected $dashboard;

    /**
     * {@inheritdoc}
     */
    public function addChildren(BlockInterface $child)
    {
        $this->children[] = $child;

        $child->setParent($this);

        if ($child instanceof DashboardBlockInterface) {
            $child->setDashboard($this->getDashboard());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDashboard(DashboardInterface $dashboard = null)
    {
        $this->dashboard = $dashboard;
    }

    /**
     * {@inheritdoc}
     */
    public function getDashboard()
    {
        return $this->dashboard;
    }
}
