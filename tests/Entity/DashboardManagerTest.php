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
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Sonata\DashboardBundle\Entity\BaseDashboard;
use Sonata\DashboardBundle\Entity\DashboardManager;

/**
 * Class DashboardManagerTest.
 */
final class DashboardManagerTest extends TestCase
{
    public function testGetPager(): void
    {
        $self = $this;
        $this
            ->getDashboardManager(function ($qb) use ($self): void {
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('d.name'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo([]));
                $qb->expects($this->any())->method('getRootAliases')->will($this->returnValue([]));
            })
            ->getPager([], 1);
    }

    public function testGetPagerWithInvalidSort(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Invalid sort field \'invalid\' in \'Sonata\\DashboardBundle\\Entity\\BaseDashboard\' class'
        );

        $self = $this;
        $this
            ->getDashboardManager(static function ($qb) use ($self): void {
            })
            ->getPager([], 1, 10, ['invalid' => 'ASC']);
    }

    public function testGetPagerWithMultipleSort(): void
    {
        $self = $this;
        $this
            ->getDashboardManager(function ($qb) use ($self): void {
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->exactly(2))->method('orderBy')->with(
                    $self->logicalOr(
                        $self->equalTo('d.name'),
                        $self->equalTo('d.routeName')
                    ),
                    $self->logicalOr(
                        $self->equalTo('ASC'),
                        $self->equalTo('DESC')
                    )
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo([]));
                $qb->expects($this->any())->method('getRootAliases')->will($this->returnValue([]));
            })
            ->getPager([], 1, 10, [
                'name' => 'ASC',
                'routeName' => 'DESC',
            ]);
    }

    public function testGetPagerWithEnabledPages(): void
    {
        $self = $this;
        $this
            ->getDashboardManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('d.enabled = :enabled'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['enabled' => true]));
                $qb->expects($this->any())->method('getRootAliases')->will($this->returnValue([]));
            })
            ->getPager(['enabled' => true], 1);
    }

    public function testGetPagerWithDisabledPages(): void
    {
        $self = $this;
        $this
            ->getDashboardManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('d.enabled = :enabled'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['enabled' => false]));
                $qb->expects($this->any())->method('getRootAliases')->will($this->returnValue([]));
            })
            ->getPager(['enabled' => false], 1);
    }

    public function testGetPagerWithEditedPages(): void
    {
        $self = $this;
        $this
            ->getDashboardManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('d.edited = :edited'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['edited' => true]));
                $qb->expects($this->any())->method('getRootAliases')->will($this->returnValue([]));
            })
            ->getPager(['edited' => true], 1);
    }

    public function testGetPagerWithNonEditedPages(): void
    {
        $self = $this;
        $this
            ->getDashboardManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('d.edited = :edited'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['edited' => false]));
                $qb->expects($this->any())->method('getRootAliases')->will($this->returnValue([]));
            })
            ->getPager(['edited' => false], 1);
    }

    private function getDashboardManager($qbCallback)
    {
        $query = $this->getMockForAbstractClass(AbstractQuery::class, [], '', false, true, true, ['execute']);
        $query->expects($this->any())->method('execute')->will($this->returnValue(true));

        $qb = $this->createMock(QueryBuilder::class, [], [
            $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock(),
        ]);

        $qb->expects($this->any())->method('select')->will($this->returnValue($qb));
        $qb->expects($this->any())->method('getQuery')->will($this->returnValue($query));

        $qbCallback($qb);

        $repository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $repository->expects($this->any())->method('createQueryBuilder')->will($this->returnValue($qb));

        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->expects($this->any())->method('getFieldNames')->will($this->returnValue([
            'name',
            'routeName',
        ]));

        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('getRepository')->will($this->returnValue($repository));
        $em->expects($this->any())->method('getClassMetadata')->will($this->returnValue($metadata));

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        return new DashboardManager(BaseDashboard::class, $registry);
    }
}
