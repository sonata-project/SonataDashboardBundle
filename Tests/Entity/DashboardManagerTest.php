<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundle\Tests\Entity;

use Sonata\DashboardBundle\Entity\DashboardManager;

/**
 * Class DashboardManagerTest.
 */
class DashboardManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPager()
    {
        $self = $this;
        $this
            ->getDashboardManager(function ($qb) use ($self) {
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

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Invalid sort field 'invalid' in 'Sonata\DashboardBundle\Entity\BaseDashboard' class
     */
    public function testGetPagerWithInvalidSort()
    {
        $self = $this;
        $this
            ->getDashboardManager(function ($qb) use ($self) {
            })
            ->getPager([], 1, 10, ['invalid' => 'ASC']);
    }

    public function testGetPagerWithMultipleSort()
    {
        $self = $this;
        $this
            ->getDashboardManager(function ($qb) use ($self) {
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

    public function testGetPagerWithEnabledPages()
    {
        $self = $this;
        $this
            ->getDashboardManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('d.enabled = :enabled'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['enabled' => true]));
                $qb->expects($this->any())->method('getRootAliases')->will($this->returnValue([]));
            })
            ->getPager(['enabled' => true], 1);
    }

    public function testGetPagerWithDisabledPages()
    {
        $self = $this;
        $this
            ->getDashboardManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('d.enabled = :enabled'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['enabled' => false]));
                $qb->expects($this->any())->method('getRootAliases')->will($this->returnValue([]));
            })
            ->getPager(['enabled' => false], 1);
    }

    public function testGetPagerWithEditedPages()
    {
        $self = $this;
        $this
            ->getDashboardManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('d.edited = :edited'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['edited' => true]));
                $qb->expects($this->any())->method('getRootAliases')->will($this->returnValue([]));
            })
            ->getPager(['edited' => true], 1);
    }

    public function testGetPagerWithNonEditedPages()
    {
        $self = $this;
        $this
            ->getDashboardManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('d.edited = :edited'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['edited' => false]));
                $qb->expects($this->any())->method('getRootAliases')->will($this->returnValue([]));
            })
            ->getPager(['edited' => false], 1);
    }

    protected function getDashboardManager($qbCallback)
    {
        $query = $this->getMockForAbstractClass('Doctrine\ORM\AbstractQuery', [], '', false, true, true, ['execute']);
        $query->expects($this->any())->method('execute')->will($this->returnValue(true));

        $qb = $this->getMock('Doctrine\ORM\QueryBuilder', [], [
            $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock(),
        ]);

        $qb->expects($this->any())->method('select')->will($this->returnValue($qb));
        $qb->expects($this->any())->method('getQuery')->will($this->returnValue($query));

        $qbCallback($qb);

        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($this->any())->method('createQueryBuilder')->will($this->returnValue($qb));

        $metadata = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $metadata->expects($this->any())->method('getFieldNames')->will($this->returnValue([
            'name',
            'routeName',
        ]));

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('getRepository')->will($this->returnValue($repository));
        $em->expects($this->any())->method('getClassMetadata')->will($this->returnValue($metadata));

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        return new DashboardManager('Sonata\DashboardBundle\Entity\BaseDashboard', $registry);
    }
}
