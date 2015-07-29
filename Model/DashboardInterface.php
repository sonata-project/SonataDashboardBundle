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
 * DashboardInterface
 *
 * @author Quentin Somazzi <qsomazzi@ekino.com>
 */
interface DashboardInterface
{
    /**
     * Returns the id
     *
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     */
    public function setId($id);

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled);

    /**
     * Get enabled
     *
     * @return boolean $enabled
     */
    public function getEnabled();

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName();

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * Get createdAt
     *
     * @return \DateTime $createdAt
     */
    public function getCreatedAt();

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * Get updatedAt
     *
     * @return \DateTime $updatedAt
     */
    public function getUpdatedAt();

    /**
     * Add blocs
     *
     * @param DashboardBlockInterface $block
     */
    public function addBlocks(DashboardBlockInterface $block);

    /**
     * Get blocs
     *
     * @return array
     */
    public function getBlocks();

    /**
     * @return boolean
     */
    public function getEdited();

    /**
     * @param boolean $edited
     */
    public function setEdited($edited);
}
