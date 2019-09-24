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
     * @var bool|null
     */
    protected $enabled;

    /**
     * @var DashboardBlockInterface[]
     */
    protected $blocks;

    /**
     * @var bool|null
     */
    protected $edited;

    /**
     * @var bool|null
     */
    protected $default;

    public function __construct()
    {
        $this->blocks = [];
        $this->edited = true;
    }

    public function __toString()
    {
        return $this->getName() ?: '-';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id)
    {
        $this->id = $id;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->enabled ?? false;
    }

    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getEdited(): bool
    {
        return $this->edited ?? false;
    }

    public function setEdited(bool $edited)
    {
        $this->edited = $edited;

        return $this;
    }

    public function setDefault(bool $default)
    {
        $this->default = $default;
    }

    public function isDefault(): bool
    {
        return $this->default ?? false;
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
