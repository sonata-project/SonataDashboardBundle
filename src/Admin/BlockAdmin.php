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

namespace Sonata\DashboardBundle\Admin;

use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\BlockBundle\Block\BlockServiceInterface;
use Sonata\BlockBundle\Block\BlockServiceManagerInterface;
use Sonata\BlockBundle\Block\Service\EditableBlockService;
use Sonata\BlockBundle\Form\Type\ServiceListType;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Cache\CacheManagerInterface;
use Sonata\DashboardBundle\Mapper\DashboardFormMapper;
use Sonata\DashboardBundle\Model\DashboardBlockInterface;
use Sonata\DashboardBundle\Model\DashboardInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Abstract admin class for the Block model.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class BlockAdmin extends AbstractAdmin
{
    protected $accessMapping = [
        'savePosition' => 'EDIT',
        'switchParent' => 'EDIT',
        'composePreview' => 'EDIT',
    ];

    /**
     * @var BlockServiceManagerInterface
     */
    private $blockManager;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var bool
     */
    private $inValidate = false;

    /**
     * @var array
     */
    private $containerBlockTypes = [];

    /**
     * @var string|null
     */
    private $defaultContainerType;

    public function getObject($id)
    {
        $subject = parent::getObject($id);

        if ($subject) {
            $service = $this->blockManager->get($subject);

            $resolver = new OptionsResolver();

            if (method_exists($service, 'configureSettings')) {
                $service->configureSettings($resolver);
            } else {
                $service->setDefaultSettings($resolver);
            }

            try {
                $subject->setSettings($resolver->resolve($subject->getSettings()));
            } catch (InvalidOptionsException $e) {
                // @TODO : add a logging error or a flash message
            }

            $service->load($subject);
        }

        return $subject;
    }

    public function preUpdate($object): void
    {
        if (!$object instanceof DashboardBlockInterface) {
            throw new \InvalidArgumentException('Invalid block object');
        }

        $block = $this->blockManager->get($object);

        if (\is_callable([$block, 'preUpdate'])) {
            $block->preUpdate($object);

            @trigger_error(
                 'The '.__METHOD__.'() method is deprecated since sonata-project/dashboard-bundle 0.x and will be removed in version 1.0.',
                 E_USER_DEPRECATED
             );
        }

        // fix weird bug with setter object not being call
        $object->setChildren($object->getChildren());

        if ($object->getDashboard() instanceof DashboardInterface) {
            $object->getDashboard()->setEdited(true);
        }
    }

    public function postUpdate($object): void
    {
        if (!$object instanceof DashboardBlockInterface) {
            throw new \InvalidArgumentException('Invalid block object');
        }

        $block = $this->blockManager->get($object);

        if (\is_callable([$block, 'postUpdate'])) {
            $block->postUpdate($object);

            @trigger_error(
                'The '.__METHOD__.'() method is deprecated since sonata-project/dashboard-bundle 0.x and will be removed in version 1.0.',
                E_USER_DEPRECATED
            );
        }

        $service = $this->blockManager->get($object);

        $this->cacheManager->invalidate($service->getCacheKeys($object));
    }

    public function prePersist($object): void
    {
        if (!$object instanceof DashboardBlockInterface) {
            throw new \InvalidArgumentException('Invalid block object');
        }

        $block = $this->blockManager->get($object);

        if (\is_callable([$block, 'prePersist'])) {
            $block->prePersist($object);

            @trigger_error(
                'The '.__METHOD__.'() method is deprecated since sonata-project/dashboard-bundle 0.x and will be removed in version 1.0.',
                E_USER_DEPRECATED
            );
        }

        if ($object->getDashboard() instanceof DashboardInterface) {
            $object->getDashboard()->setEdited(true);
        }

        // fix weird bug with setter object not being call
        $object->setChildren($object->getChildren());
    }

    public function postPersist($object): void
    {
        if (!$object instanceof DashboardBlockInterface) {
            throw new \InvalidArgumentException('Invalid block object');
        }

        $block = $this->blockManager->get($object);

        if (\is_callable([$block, 'postPersist'])) {
            $block->postPersist($object);

            @trigger_error(
                'The '.__METHOD__.'() method is deprecated since sonata-project/dashboard-bundle 0.x and will be removed in version 1.0.',
                E_USER_DEPRECATED
            );
        }

        $service = $this->blockManager->get($object);

        $this->cacheManager->invalidate($service->getCacheKeys($object));
    }

    public function preRemove($object): void
    {
        if (!$object instanceof DashboardBlockInterface) {
            throw new \InvalidArgumentException('Invalid block object');
        }

        $block = $this->blockManager->get($object);

        if (\is_callable([$block, 'preRemove'])) {
            $block->preRemove($object);

            @trigger_error(
                'The '.__METHOD__.'() method is deprecated since sonata-project/dashboard-bundle 0.x and will be removed in version 1.0.',
                E_USER_DEPRECATED
            );
        }
    }

    public function postRemove($object): void
    {
        if (!$object instanceof DashboardBlockInterface) {
            throw new \InvalidArgumentException('Invalid block object');
        }

        $block = $this->blockManager->get($object);

        if (\is_callable([$block, 'postRemove'])) {
            $block->postRemove($object);

            @trigger_error(
                'The '.__METHOD__.'() method is deprecated since sonata-project/dashboard-bundle 0.x and will be removed in version 1.0.',
                E_USER_DEPRECATED
            );
        }
    }

    public function setBlockManager(BlockServiceManagerInterface $blockManager): void
    {
        $this->blockManager = $blockManager;
    }

    public function setCacheManager(CacheManagerInterface $cacheManager): void
    {
        $this->cacheManager = $cacheManager;
    }

    public function setContainerBlockTypes(array $containerBlockTypes): void
    {
        $this->containerBlockTypes = $containerBlockTypes;
    }

    public function setDefaultContainerType(string $defaultContainerType): void
    {
        $this->defaultContainerType = $defaultContainerType;
    }

    public function getDefaultContainerType(): ?string
    {
        return $this->defaultContainerType;
    }

    public function getPersistentParameters()
    {
        if (!$this->hasRequest()) {
            return [];
        }

        $parameters = parent::getPersistentParameters();

        if ($composer = $this->getRequest()->get('composer')) {
            $parameters['composer'] = $composer;
        }

        $parameters['type'] = $this->getRequest()->get('type');

        return $parameters;
    }

    /**
     * Override needed to make the dashboard composer cleaner.
     */
    public function toString($object)
    {
        if (!\is_object($object)) {
            return '';
        }
        if (method_exists($object, 'getName') && null !== $object->getName()) {
            return (string) $object->getName();
        }

        return parent::toString($object);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('type')
            ->add('name')
            ->add('enabled', null, ['editable' => true])
            ->add('updatedAt')
            ->add('position')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
            ->add('enabled')
            ->add('type')
        ;
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        parent::configureRoutes($collection);

        $collection->add('view', $this->getRouterIdParameter().'/view');
        $collection->add('switchParent', 'switch-parent');
        $collection->add('savePosition', '{block_id}/save-position', [
            'block_id' => null,
        ]);
        $collection->add('composePreview', '{block_id}/compose_preview', [
            'block_id' => null,
        ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $block = $this->getSubject();

        $dashboard = false;

        if ($this->getParent()) {
            $dashboard = $this->getParent()->getSubject();

            if (!$dashboard instanceof DashboardInterface) {
                throw new \RuntimeException('The BlockAdmin must be attached to a parent DashboardAdmin');
            }

            if (null === $block->getId()) { // new block
                $block->setType($this->request->get('type'));
                $block->setDashboard($dashboard);
            }

            if ($block->getDashboard()->getId() !== $dashboard->getId()) {
                throw new \RuntimeException('The dashboard reference on BlockAdmin and parent admin are not the same');
            }
        }

        $isComposer = $this->hasRequest() ? $this->getRequest()->get('composer', false) : false;
        $generalGroupOptions = $optionsGroupOptions = [];
        if ($isComposer) {
            $generalGroupOptions['class'] = 'hidden';
            $optionsGroupOptions['name'] = '';
        }

        $formMapper->with('form.field_group_general', $generalGroupOptions);

        $containerBlockTypes = $this->containerBlockTypes;
        $isContainerRoot = $block && \in_array($block->getType(), $containerBlockTypes, true) && !$this->hasParentFieldDescription();
        $isStandardBlock = $block && !\in_array($block->getType(), $containerBlockTypes, true) && !$this->hasParentFieldDescription();

        if (!$isComposer) {
            $formMapper->add('name');
        } elseif (!$isContainerRoot) {
            $formMapper->add('name', HiddenType::class);
        }

        $formMapper->end();

        if ($isContainerRoot || $isStandardBlock) {
            $formMapper->with('form.field_group_general', $generalGroupOptions);

            $service = $this->blockManager->get($block);

            // need to investigate on this case where $dashboard == null ... this should not be possible
            if ($isStandardBlock && $dashboard && !empty($containerBlockTypes)) {
                $formMapper->add('parent', EntityType::class, [
                    'class' => $this->getClass(),
                    'query_builder' => static function (EntityRepository $repository) use ($dashboard, $containerBlockTypes) {
                        return $repository->createQueryBuilder('a')
                            ->andWhere('a.dashboard = :dashboard AND a.type IN (:types)')
                            ->setParameters([
                                'dashboard' => $dashboard,
                                'types' => $containerBlockTypes,
                            ]);
                    },
                ], [
                    'admin_code' => $this->getCode(),
                ]);
            }

            if ($isComposer) {
                $formMapper->add('enabled', HiddenType::class, [
                    'data' => true,
                ]);
            } else {
                $formMapper->add('enabled');
            }

            if ($isStandardBlock) {
                $formMapper->add('position', IntegerType::class);
            }

            $formMapper->end();

            $formMapper->with('form.field_group_options', $optionsGroupOptions);

            $this->configureBlockFields($formMapper, $block);

            // When editing a container in composer view, hide some settings
            if ($isContainerRoot && $isComposer) {
                $formMapper->remove('children');
                $formMapper->add('name', TextType::class, [
                    'required' => true,
                ]);

                $formSettings = $formMapper->get('settings');

                $formSettings->remove('code');
                $formSettings->remove('layout');
                $formSettings->remove('template');
            }

            $formMapper->end();
        } else {
            $formMapper
                ->with('form.field_group_options', $optionsGroupOptions)
                    ->add('type', ServiceListType::class, [
                        'context' => 'sonata_dashboard_bundle',
                    ])
                    ->add('enabled')
                    ->add('position', IntegerType::class)
                ->end()
            ;
        }
    }

    private function configureBlockFields(FormMapper $formMapper, BlockInterface $block): void
    {
        $service = $this->blockManager->get($block);
        $blockType = $block->getType();

        if (!$service instanceof BlockServiceInterface) {
            throw new \RuntimeException(sprintf(
                'The block "%s" must implement %s',
                $blockType,
                BlockServiceInterface::class
            ));
        }

        if ($service instanceof EditableBlockService) {
            $blockMapper = new DashboardFormMapper($formMapper);

            if ($block->getId() > 0) {
                $service->configureEditForm($blockMapper, $block);
            } else {
                $service->configureCreateForm($blockMapper, $block);
            }
        } else {
            @trigger_error(
                sprintf(
                    'Editing a block service which doesn\'t implement %s is deprecated since sonata-project/dashboard-bundle 0.x and will not be allowed with version 1.0.',
                    EditableBlockService::class
                ),
                E_USER_DEPRECATED
            );

            if ($block->getId() > 0) {
                $service->buildEditForm($formMapper, $block);
            } else {
                $service->buildCreateForm($formMapper, $block);
            }
        }
    }
}
