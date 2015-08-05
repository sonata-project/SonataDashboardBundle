<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sonata\DashboardBundle\Model\Block;

/**
 * The class stores block information.
 *
 * @author Quentin Somazzi <qsomazzi@ekino.com>
 */
abstract class BaseBlock extends Block
{
    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();

        parent::__construct();
    }

    /**
     * Updates dates before creating/updating entity.
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Updates dates before updating entity.
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function setChildren($children)
    {
        $this->children = new ArrayCollection();

        foreach ($children as $child) {
            $this->addChildren($child);
        }
    }
}
