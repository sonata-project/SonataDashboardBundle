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
     * @return mixed|null
     */
    public function getId();

    /**
     * @param mixed $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled);

    /**
     * Get enabled.
     *
     * @return bool $enabled
     */
    public function getEnabled();

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Get name.
     *
     * @return string|null $name
     */
    public function getName();

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * Get createdAt.
     *
     * @return \DateTime|null $createdAt
     */
    public function getCreatedAt();

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * Get updatedAt.
     *
     * @return \DateTime|null $updatedAt
     */
    public function getUpdatedAt();

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
    public function getEdited();

    /**
     * @param bool $edited
     *
     * @return $this
     */
    public function setEdited($edited);
}
