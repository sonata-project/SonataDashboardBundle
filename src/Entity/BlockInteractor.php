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

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Sonata\DashboardBundle\Model\BlockInteractorInterface;
use Sonata\DashboardBundle\Model\BlockManagerInterface;
use Sonata\DashboardBundle\Model\DashboardBlockInterface;
use Sonata\DashboardBundle\Model\DashboardInterface;

/**
 * This class interacts with blocks.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class BlockInteractor implements BlockInteractorInterface
{
    /**
     * @var bool[]
     */
    private $dashboardBlocksLoaded = [];

    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @var BlockManagerInterface
     */
    private $blockManager;

    /**
     * @var string
     */
    private $defaultContainer;

    /**
     * @param ManagerRegistry       $registry         Doctrine registry
     * @param BlockManagerInterface $blockManager     Block manager
     * @param string                $defaultContainer
     */
    public function __construct(ManagerRegistry $registry, BlockManagerInterface $blockManager, $defaultContainer)
    {
        $this->blockManager = $blockManager;
        $this->registry = $registry;
        $this->defaultContainer = $defaultContainer;
    }

    public function getBlock(int $id): ?DashboardBlockInterface
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('b')
            ->from($this->blockManager->getClass(), 'b')
            ->where('b.id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getBlocksById(DashboardInterface $dashboard)
    {
        $blocks = $this->getEntityManager()
            ->createQuery(sprintf('SELECT b FROM %s b INDEX BY b.id WHERE b.dashboard = :dashboard ORDER BY b.position ASC', $this->blockManager->getClass()))
            ->setParameters([
                'dashboard' => $dashboard->getId(),
            ])
            ->execute();

        return $blocks;
    }

    public function saveBlocksPosition(array $data = [], bool $partial = true): void
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();

        try {
            foreach ($data as $block) {
                if (!$block['id'] || !\array_key_exists('position', $block) || !$block['parent_id'] || !$block['dashboard_id']) {
                    continue;
                }

                $this->blockManager->updatePosition($block['id'], $block['position'], $block['parent_id'], $block['dashboard_id'], $partial);
            }

            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollBack();

            throw $e;
        }
    }

    public function createNewContainer(array $values = [], ?\Closure $alter = null): DashboardBlockInterface
    {
        $container = $this->blockManager->create();

        if (!$container instanceof DashboardBlockInterface) {
            throw new \InvalidArgumentException('Invalid block created');
        }

        $container->setEnabled($values['enabled'] ?? true);
        $container->setCreatedAt(new \DateTime());
        $container->setUpdatedAt(new \DateTime());
        $container->setType($this->defaultContainer);

        if (isset($values['dashboard'])) {
            $container->setDashboard($values['dashboard']);
        }

        if (isset($values['name'])) {
            $container->setName($values['name']);
        } else {
            $container->setName($values['code'] ?? 'No name defined');
        }

        $container->setSettings(['code' => $values['code'] ?? 'no code defined']);
        $container->setPosition($values['position'] ?? 1);

        if (isset($values['parent'])) {
            $container->setParent($values['parent']);
        }

        if ($alter) {
            $alter($container);
        }

        $this->blockManager->save($container);

        return $container;
    }

    public function loadDashboardBlocks(DashboardInterface $dashboard)
    {
        if (isset($this->dashboardBlocksLoaded[$dashboard->getId()])) {
            return [];
        }

        $blocks = $this->getBlocksById($dashboard);

        $dashboard->disableBlockLazyLoading();

        foreach ($blocks as $block) {
            $parent = $block->getParent();

            $block->disableChildrenLazyLoading();
            if (!$parent) {
                $dashboard->addBlocks($block);

                continue;
            }

            $blocks[$block->getParent()->getId()]->disableChildrenLazyLoading();
            $blocks[$block->getParent()->getId()]->addChildren($block);
        }

        $this->dashboardBlocksLoaded[$dashboard->getId()] = true;

        return $blocks;
    }

    private function getEntityManager(): EntityManager
    {
        return $this->registry->getManagerForClass($this->blockManager->getClass());
    }
}
