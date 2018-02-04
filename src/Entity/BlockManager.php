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

use Sonata\CoreBundle\Model\BaseEntityManager;
use Sonata\DashboardBundle\Model\BlockManagerInterface;
use Sonata\DashboardBundle\Model\DashboardBlockInterface;
use Sonata\DashboardBundle\Model\DashboardInterface;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;

/**
 * This class manages BlockInterface persistency with the Doctrine ORM.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class BlockManager extends BaseEntityManager implements BlockManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function save($dashboard, $andFlush = true)
    {
        parent::save($dashboard, $andFlush);

        return $dashboard;
    }

    /**
     * {@inheritdoc}
     */
    public function updatePosition($id, $position, $parentId = null, $dashboardId = null, $partial = true)
    {
        if ($partial) {
            $meta = $this->getEntityManager()->getClassMetadata($this->getClass());

            // retrieve object references
            $block = $this->getEntityManager()->getReference($this->getClass(), $id);

            if (!$block instanceof DashboardBlockInterface) {
                throw new \InvalidArgumentException('Invalid block element found');
            }

            $dashboardRelation = $meta->getAssociationMapping('dashboard');
            $dashboard = $this->getEntityManager()->getPartialReference($dashboardRelation['targetEntity'], $dashboardId);

            if (!$dashboard instanceof DashboardInterface) {
                throw new \InvalidArgumentException('Invalid dashboard block element found');
            }

            $parentRelation = $meta->getAssociationMapping('parent');
            $parent = $this->getEntityManager()->getPartialReference($parentRelation['targetEntity'], $parentId);

            if (!$parent instanceof DashboardBlockInterface) {
                throw new \InvalidArgumentException('Invalid parent block element found');
            }

            $block->setDashboard($dashboard);
            $block->setParent($parent);
        } else {
            $block = $this->find($id);
        }

        // set new values
        $block->setPosition($position);
        $this->getEntityManager()->persist($block);

        return $block;
    }

    /**
     * {@inheritdoc}
     */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = [])
    {
        $query = $this->getRepository()
            ->createQueryBuilder('b')
            ->select('b');

        $parameters = [];

        if (isset($criteria['enabled'])) {
            $query->andWhere('p.enabled = :enabled');
            $parameters['enabled'] = $criteria['enabled'];
        }

        if (isset($criteria['type'])) {
            $query->andWhere('p.type = :type');
            $parameters['type'] = $criteria['type'];
        }

        $query->setParameters($parameters);

        $pager = new Pager();
        $pager->setMaxPerPage($limit);
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }
}
