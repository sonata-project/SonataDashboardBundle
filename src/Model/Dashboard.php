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
 * Dashboard.
 *
 * @author Quentin Somazzi <qsomazzi@ekino.com>
 */
abstract class Dashboard implements DashboardInterface
{
    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var \DateTime|null
     */
    protected $createdAt;

    /**
     * @var \DateTime|null
     */
    protected $updatedAt;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var DashboardBlockInterface[]
     */
    protected $blocks;

    /**
     * @var bool
     */
    protected $edited;

    public function __construct()
    {
        $this->blocks = [];
        $this->edited = true;
    }

    public function __toString()
    {
        return $this->getName() ?: '-';
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getEdited()
    {
        return $this->edited;
    }

    public function setEdited($edited)
    {
        $this->edited = $edited;

        return $this;
    }

    public function addBlocks(DashboardBlockInterface $block)
    {
        $block->setDashboard($this);

        $this->blocks[] = $block;

        return $this;
    }

    public function getBlocks()
    {
        return $this->blocks;
    }
}
