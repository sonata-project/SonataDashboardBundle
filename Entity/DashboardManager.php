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

use Sonata\DashboardBundle\Model\DashboardManagerInterface;
use Sonata\CoreBundle\Model\BaseEntityManager;

/**
 * This class manages DashboardInterface persistency with the Doctrine ORM
 *
 * @author Quentin Somazzi <qsomazzi@ekino.com>
 */
class DashboardManager extends BaseEntityManager implements DashboardManagerInterface
{

}
