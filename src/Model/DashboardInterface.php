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
 * DashboardInterface.
 *
 * @author Quentin Somazzi <qsomazzi@ekino.com>
 */
interface DashboardInterface
{
    /**
     * Returns the id.
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(?int $id);

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled(bool $enabled);

    /**
     * Get enabled.
     *
     * @return bool $enabled
     */
    public function getEnabled(): bool;

    /**
     * Set name.
     *
     * @param string|null $name
     *
     * @return $this
     */
    public function setName(?string $name);

    /**
     * Get name.
     *
     * @return string|null $name
     */
    public function getName(): ?string;

    /**
     * Set createdAt.
     *
     * @param \DateTime|null $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(?\DateTime $createdAt);

    /**
     * Get createdAt.
     *
     * @return \DateTime|null $createdAt
     */
    public function getCreatedAt(): ?\DateTime;

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(?\DateTime $updatedAt);

    /**
     * Get updatedAt.
     *
     * @return \DateTime|null $updatedAt
     */
    public function getUpdatedAt(): ?\DateTime;

    /**
     * @return bool
     */
    public function isDefault(): bool;

    /**
     * @param bool $default
     *
     * @return DashboardInterface
     */
    public function setDefault($default);

    /**
     * Add blocs.
     *
     * @param DashboardBlockInterface $block
     *
     * @return $this
     */
    public function addBlocks(DashboardBlockInterface $block);

    /**
     * Get blocs.
     *
     * @return DashboardBlockInterface[]
     */
    public function getBlocks();

    /**
     * @return bool
     */
    public function getEdited(): bool;

    /**
     * @param bool $edited
     *
     * @return $this
     */
    public function setEdited(bool $edited);
}
