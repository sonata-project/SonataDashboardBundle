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
 * @author Quentin Somazzi <qsomazzi@ekino.com>
 */
interface DashboardInterface
{
    public function getId(): ?int;

    public function setId(?int $id);

    public function setEnabled(bool $enabled);

    public function getEnabled(): bool;

    public function setName(?string $name);

    public function getName(): ?string;

    public function setCreatedAt(?\DateTime $createdAt);

    public function getCreatedAt(): ?\DateTime;

    public function setUpdatedAt(?\DateTime $updatedAt);

    public function getUpdatedAt(): ?\DateTime;

    public function isDefault(): bool;

    /**
     * @param bool $default
     */
    public function setDefault($default);

    public function addBlocks(DashboardBlockInterface $block);

    /**
     * @return DashboardBlockInterface[]
     */
    public function getBlocks();

    public function getEdited(): bool;

    public function setEdited(bool $edited);
}
