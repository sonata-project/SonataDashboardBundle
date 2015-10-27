<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundle\Admin;

use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\BlockBundle\Block\BlockServiceManagerInterface;
use Sonata\Cache\CacheManagerInterface;
use Sonata\DashboardBundle\Entity\BaseBlock;
use Sonata\DashboardBundle\Model\DashboardInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Abstract admin class for the Block model.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class BlockAdmin extends Admin
{
    /**
     * @var BlockServiceManagerInterface
     */
    protected $blockManager;

    /**
     * @var CacheManagerInterface
     */
    protected $cacheManager;

    /**
     * @var bool
     */
    protected $inValidate = false;

    /**
     * @var array
     */
    protected $containerBlockTypes = array();

    /**
     * @var string
     */
    protected $defaultContainerType;

    /**
     * @var string
     */
    protected $parentAssociationMapping = 'dashboard';

    protected $accessMapping = array(
        'savePosition'   => 'EDIT',
        'switchParent'   => 'EDIT',
        'composePreview' => 'EDIT',
    );

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('type')
            ->add('name')
            ->add('enabled', null, array('editable' => true))
            ->add('updatedAt')
            ->add('position')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
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
    public function preUpdate($object)
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
    public function postUpdate($object)
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
    public function prePersist($object)
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
    public function postPersist($object)
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
    public function preRemove($object)
    {
        $this->blockManager->get($object)->preRemove($object);
    }

    /**
     * {@inheritdoc}
     *
     * @param BaseBlock $object
     */
    public function postRemove($object)
    {
        $this->blockManager->get($object)->postRemove($object);
    }

    /**
     * @param BlockServiceManagerInterface $blockManager
     */
    public function setBlockManager(BlockServiceManagerInterface $blockManager)
    {
        $this->blockManager = $blockManager;
    }

    /**
     * @param CacheManagerInterface $cacheManager
     */
    public function setCacheManager(CacheManagerInterface $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * @param array $containerBlockTypes
     */
    public function setContainerBlockTypes(array $containerBlockTypes)
    {
        $this->containerBlockTypes = $containerBlockTypes;
    }

    /**
     * @param $defaultContainerType
     */
    public function setDefaultContainerType($defaultContainerType)
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
            return array();
        }

        $parameters = parent::getPersistentParameters();

        if ($composer = $this->getRequest()->get('composer')) {
            $parameters['composer'] = $composer;
        }

        if ($composer = $this->getRequest()->get('type')) {
            $parameters['type'] = $composer;
        }

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection->add('view', $this->getRouterIdParameter().'/view');
        $collection->add('switchParent', 'switch-parent');
        $collection->add('savePosition', '{block_id}/save-position', array(
            'block_id' => null,
        ));
        $collection->add('composePreview', '{block_id}/compose_preview', array(
            'block_id' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $block = $this->getSubject();

        $dashboard = false;

        if ($this->getParent()) {
            $dashboard = $this->getParent()->getSubject();

            if (!$dashboard instanceof DashboardInterface) {
                throw new \RuntimeException('The BlockAdmin must be attached to a parent DashboardAdmin');
            }

            if ($block->getId() === null) { // new block
                $block->setType($this->request->get('type'));
                $block->setDashboard($dashboard);
            }

            if ($block->getDashboard()->getId() != $dashboard->getId()) {
                throw new \RuntimeException('The dashboard reference on BlockAdmin and parent admin are not the same');
            }
        }

        $isComposer = $this->hasRequest() ? $this->getRequest()->get('composer', false) : false;
        $generalGroupOptions = $optionsGroupOptions = array();
        if ($isComposer) {
            $generalGroupOptions['class'] = 'hidden';
            $optionsGroupOptions['name'] = '';
        }

        $formMapper->with($this->trans('form.field_group_general'), $generalGroupOptions);

        $containerBlockTypes = $this->containerBlockTypes;
        $isContainerRoot = $block && in_array($block->getType(), $containerBlockTypes) && !$this->hasParentFieldDescription();
        $isStandardBlock = $block && !in_array($block->getType(), $containerBlockTypes) && !$this->hasParentFieldDescription();

        if (!$isComposer) {
            $formMapper->add('name');
        } elseif (!$isContainerRoot) {
            $formMapper->add('name', 'hidden');
        }

        $formMapper->end();

        if ($isContainerRoot || $isStandardBlock) {
            $formMapper->with($this->trans('form.field_group_general'), $generalGroupOptions);

            $service = $this->blockManager->get($block);

            // need to investigate on this case where $dashboard == null ... this should not be possible
            if ($isStandardBlock && $dashboard && !empty($containerBlockTypes)) {
                $formMapper->add('parent', 'entity', array(
                    'class'         => $this->getClass(),
                    'query_builder' => function (EntityRepository $repository) use ($dashboard, $containerBlockTypes) {
                        return $repository->createQueryBuilder('a')
                            ->andWhere('a.dashboard = :dashboard AND a.type IN (:types)')
                            ->setParameters(array(
                                'dashboard' => $dashboard,
                                'types'     => $containerBlockTypes,
                            ));
                    },
                ), array(
                    'admin_code' => $this->getCode(),
                ));
            }

            if ($isComposer) {
                $formMapper->add('enabled', 'hidden', array('data' => true));
            } else {
                $formMapper->add('enabled');
            }

            if ($isStandardBlock) {
                $formMapper->add('position', 'integer');
            }

            $formMapper->end();

            $formMapper->with($this->trans('form.field_group_options'), $optionsGroupOptions);

            if ($block->getId() > 0) {
                $service->buildEditForm($formMapper, $block);
            } else {
                $service->buildCreateForm($formMapper, $block);
            }

            // When editing a container in composer view, hide some settings
            if ($isContainerRoot && $isComposer) {
                $formMapper->remove('children');
                $formMapper->add('name', 'text', array('required' => true));

                $formSettings = $formMapper->get('settings');

                $formSettings->remove('code');
                $formSettings->remove('layout');
                $formSettings->remove('template');
            }

            $formMapper->end();
        } else {
            $formMapper
                ->with($this->trans('form.field_group_options'), $optionsGroupOptions)
                    ->add('type', 'sonata_block_service_choice', array(
                        'context' => 'sonata_dashboard_bundle',
                    ))
                    ->add('enabled')
                    ->add('position', 'integer')
                ->end()
            ;
        }
    }

    /**
     * Override needed to make the dashboard composer cleaner.
     *
     * {@inheritdoc}
     */
    public function toString($object)
    {
        return $object->getName();
    }
}
