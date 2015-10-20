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
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     *
     * @return DashboardInterface
     */
    public function setId($id);

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return DashboardInterface
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
     * @return DashboardInterface
     */
    public function setName($name);

    /**
     * Get name.
     *
     * @return string $name
     */
    public function getName();

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return DashboardInterface
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * Get createdAt.
     *
     * @return \DateTime $createdAt
     */
    public function getCreatedAt();

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return DashboardInterface
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * Get updatedAt.
     *
     * @return \DateTime $updatedAt
     */
    public function getUpdatedAt();

    /**
     * Add blocs.
     *
     * @param DashboardBlockInterface $block
     *
     * @return DashboardInterface
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
     * @return DashboardInterface
     */
    public function setEdited($edited);
}
