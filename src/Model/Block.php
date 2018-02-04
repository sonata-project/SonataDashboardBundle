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
     * @var mixed|null
     */
    protected $id;

    /**
     * @var DashboardInterface|null
     */
    protected $dashboard;

    public function addChildren(BlockInterface $child): void
    {
        $this->children[] = $child;

        $child->setParent($this);

        if ($child instanceof DashboardBlockInterface) {
            $child->setDashboard($this->getDashboard());
        }
    }

    public function setDashboard(?DashboardInterface $dashboard): void
    {
        $this->dashboard = $dashboard;
    }

    public function getDashboard(): ?DashboardInterface
    {
        return $this->dashboard;
    }
}
