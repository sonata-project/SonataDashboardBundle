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
use Sonata\BlockBundle\Block\BlockServiceManagerInterface;
use Sonata\BlockBundle\Form\Type\ServiceListType;
use Sonata\Cache\CacheManagerInterface;
use Sonata\DashboardBundle\Entity\BaseBlock;
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
    /**
     * @var string
     */
    protected $parentAssociationMapping = 'dashboard';

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
     * @var string
     */
    private $defaultContainerType;

    /**
     * {@inheritdoc}
     */
    public function getObject($id)
    {
        $subject = parent::getObject($id);

        if ($subject) {
            $service = $this->blockManager->get($subject);

            $resolver = new OptionsResolver();
            $service->setDefaultSettings($resolver);

            try {
                $subject->setSettings($resolver->resolve($subject->getSettings()));
            } catch (InvalidOptionsException $e) {
                // @TODO : add a logging error or a flash message
            }

            $service->load($subject);
        }

        return $subject;
    }

    /**
     * {@inheritdoc}
     *
     * @param BaseBlock $object
     */
    public function preUpdate($object): void
    {
        $this->blockManager->get($object)->preUpdate($object);

        // fix weird bug with setter object not being call
        $object->setChildren($object->getChildren());

        if ($object->getDashboard() instanceof DashboardInterface) {
            $object->getDashboard()->setEdited(true);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param BaseBlock $object
     */
    public function postUpdate($object): void
    {
        $this->blockManager->get($object)->postUpdate($object);

        $service = $this->blockManager->get($object);

        $this->cacheManager->invalidate($service->getCacheKeys($object));
    }

    /**
     * {@inheritdoc}
     *
     * @param BaseBlock $object
     */
    public function prePersist($object): void
    {
        $this->blockManager->get($object)->prePersist($object);

        if ($object->getDashboard() instanceof DashboardInterface) {
            $object->getDashboard()->setEdited(true);
        }

        // fix weird bug with setter object not being call
        $object->setChildren($object->getChildren());
    }

    /**
     * {@inheritdoc}
     *
     * @param BaseBlock $object
     */
    public function postPersist($object): void
    {
        $this->blockManager->get($object)->postPersist($object);

        $service = $this->blockManager->get($object);

        $this->cacheManager->invalidate($service->getCacheKeys($object));
    }

    /**
     * {@inheritdoc}
     *
     * @param BaseBlock $object
     */
    public function preRemove($object): void
    {
        $this->blockManager->get($object)->preRemove($object);
    }

    /**
     * {@inheritdoc}
     *
     * @param BaseBlock $object
     */
    public function postRemove($object): void
    {
        $this->blockManager->get($object)->postRemove($object);
    }

    /**
     * @param BlockServiceManagerInterface $blockManager
     */
    public function setBlockManager(BlockServiceManagerInterface $blockManager): void
    {
        $this->blockManager = $blockManager;
    }

    /**
     * @param CacheManagerInterface $cacheManager
     */
    public function setCacheManager(CacheManagerInterface $cacheManager): void
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * @param array $containerBlockTypes
     */
    public function setContainerBlockTypes(array $containerBlockTypes): void
    {
        $this->containerBlockTypes = $containerBlockTypes;
    }

    /**
     * @param $defaultContainerType
     */
    public function setDefaultContainerType($defaultContainerType): void
    {
        $this->defaultContainerType = $defaultContainerType;
    }

    /**
     * @return string
     */
    public function getDefaultContainerType()
    {
        return $this->defaultContainerType;
    }

    /**
     * {@inheritdoc}
     */
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
     *
     * {@inheritdoc}
     */
    public function toString($object)
    {
        if (!is_object($object)) {
            return '';
        }
        if (method_exists($object, 'getName') && null !== $object->getName()) {
            return (string) $object->getName();
        }

        return parent::toString($object);
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
            ->add('enabled')
            ->add('type')
        ;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

            if ($block->getDashboard()->getId() != $dashboard->getId()) {
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
        $isContainerRoot = $block && in_array($block->getType(), $containerBlockTypes) && !$this->hasParentFieldDescription();
        $isStandardBlock = $block && !in_array($block->getType(), $containerBlockTypes) && !$this->hasParentFieldDescription();

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
                    'query_builder' => function (EntityRepository $repository) use ($dashboard, $containerBlockTypes) {
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

            if ($block->getId() > 0) {
                $service->buildEditForm($formMapper, $block);
            } else {
                $service->buildCreateForm($formMapper, $block);
            }

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
}
