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

use Sonata\DatagridBundle\Pager\PageableInterface;
use Sonata\Doctrine\Model\ManagerInterface;

/**
 * Defines methods to interact with the persistency layer of a DashboardInterface.
 *
 * @author Quentin Somazzi <qsomazzi@ekino.com>
 *
 * @phpstan-implements ManagerInterface<BlockInterface>
 * @phpstan-implements PageableInterface<BlockInterface>
 */
interface DashboardManagerInterface extends ManagerInterface, PageableInterface
{
}
