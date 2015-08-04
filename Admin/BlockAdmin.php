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
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DashboardBundle\Model\DashboardInterface;

/**
 * Admin class for the Block model
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class BlockAdmin extends BaseBlockAdmin
{
    protected $parentAssociationMapping = 'dashboard';

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection->add('savePosition', 'save-position');
        $collection->add('switchParent', 'switch-parent');
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
            $optionsGroupOptions['name']  = '';
        }

        $formMapper->with($this->trans('form.field_group_general'), $generalGroupOptions);

        $containerBlockTypes = $this->containerBlockTypes;
        $isContainerRoot     = $block && in_array($block->getType(), $containerBlockTypes) && !$this->hasParentFieldDescription();
        $isStandardBlock     = $block && !in_array($block->getType(), $containerBlockTypes) && !$this->hasParentFieldDescription();

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
                    'query_builder' => function(EntityRepository $repository) use ($dashboard, $containerBlockTypes) {
                        return $repository->createQueryBuilder('a')
                            ->andWhere('a.dashboard = :dashboard AND a.type IN (:types)')
                            ->setParameters(array(
                                'dashboard' => $dashboard,
                                'types'     => $containerBlockTypes,
                            ));
                    }
                ),array(
                    'admin_code' => $this->getCode()
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
                $formMapper->add('name');

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
                        'context' => 'sonata_dashboard_bundle'
                    ))
                    ->add('enabled')
                    ->add('position', 'integer')
                ->end()
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistentParameters()
    {
        $parameters = parent::getPersistentParameters();

        if ($composer = $this->getRequest()->get('composer')) {
            $parameters['composer'] = $composer;
        }

        return $parameters;
    }

    /**
     * Override needed to make the dashboard composer cleaner
     *
     * {@inheritdoc}
     */
    public function toString($object)
    {
        return $object->getName();
    }
}
