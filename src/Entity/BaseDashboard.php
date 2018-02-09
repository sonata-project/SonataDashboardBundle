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

namespace Sonata\DashboardBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sonata\DashboardBundle\Model\Dashboard;

/**
 * The class stores Dashboard information.
 *
 * @author Quentin Somazzi <qsomazzi@ekino.com>
 */
abstract class BaseDashboard extends Dashboard
{
    public function __construct()
    {
        parent::__construct();

        $this->blocks = new ArrayCollection();
    }

    public function prePersist(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }
}
