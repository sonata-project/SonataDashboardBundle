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

namespace Sonata\DashboardBundle\Tests\Entity;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Sonata\DashboardBundle\Entity\BaseDashboard;
use Sonata\DashboardBundle\Entity\BlockManager;

final class BlockManagerTest extends TestCase
{
    public function testGetPager(): void
    {
        $self = $this;
        $this
            ->getBlockManager(static function ($qb) use ($self): void {
                $qb->expects($self->never())->method('join');
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo([]));
            })
            ->getPager(['root' => true], 1);
    }

    private function getBlockManager($qbCallback)
    {
        $query = $this->getMockForAbstractClass(AbstractQuery::class, [], '', false, true, true, ['execute']);
        $query->expects($this->any())->method('execute')->willReturn(true);

        $qb = $this->createMock(QueryBuilder::class, [], [
            $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock(),
        ]);

        $qb->expects($this->any())->method('select')->willReturn($qb);
        $qb->expects($this->any())->method('getQuery')->willReturn($query);
        $qb->expects($this->any())->method('getRootAliases')->willReturn([]);

        $qbCallback($qb);

        $repository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $repository->expects($this->any())->method('createQueryBuilder')->willReturn($qb);

        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('getRepository')->willReturn($repository);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->willReturn($em);

        return new BlockManager(BaseDashboard::class, $registry);
    }
}
