<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundle\Model;

use Sonata\BlockBundle\Model\BlockInterface;

interface DashboardBlockInterface extends BlockInterface
{
    /**
     * @return DashboardInterface
     */
    public function getDashboard();

    /**
     * @param DashboardInterface $dashboard The related dashboard
     *
     * @return mixed
     */
    public function setDashboard(DashboardInterface $dashboard = null);
}
