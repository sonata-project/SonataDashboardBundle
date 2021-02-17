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

namespace Sonata\DashboardBundle\Tests\Fixtures\Entity;

use Sonata\DashboardBundle\Model\Block as AbstractBlock;

final class CmsBlock extends AbstractBlock
{
    public function setId($id)
    {
    }

    public function getId()
    {
    }
}
