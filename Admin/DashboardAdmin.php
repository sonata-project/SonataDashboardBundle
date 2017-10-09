<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundle\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Admin definition for the Dashboard class.
 *
 * @author Quentin Somazzi <qsomazzi@ekino.com>
 */
class DashboardAdmin extends AbstractAdmin
{
    protected $accessMapping = [
        'compose' => 'EDIT',
        'composeContainerShow' => 'LIST',
    ];

    /**
     * {@inheritdoc}
     */
    public function configureRoutes(RouteCollection $collection)
    {
        $collection->add('compose', '{id}/compose', [
            'id' => null,
        ]);
        $collection->add('compose_container_show', 'compose/container/{id}', [
            'id' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
        $object->setEdited(true);
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object)
    {
        $object->setEdited(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('enabled')
            ->add('name')
            ->add('edited')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('enabled', null, ['editable' => true])
            ->add('edited', null, ['editable' => true])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('edited')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        // define group zoning
        $formMapper
            ->with('form_dashboard.group_main_label', ['class' => 'col-md-12'])->end()
        ;

        $formMapper
            ->with('form_dashboard.group_main_label')
                ->add('name')
                ->add('enabled', CheckboxType::class, ['required' => false])
            ->end()
        ;

        $formMapper->setHelps([
            'name' => 'help_dashboard_name',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, ['edit'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild('sidemenu.link_edit_dashboard',
            ['uri' => $admin->generateUrl('edit', ['id' => $id])]
        );

        $menu->addChild('sidemenu.link_compose_dashboard',
            ['uri' => $admin->generateUrl('compose', ['id' => $id])]
        );

        $menu->addChild('sidemenu.link_list_blocks',
            ['uri' => $admin->generateUrl('sonata.dashboard.admin.dashboard|sonata.dashboard.admin.block.list', ['id' => $id])]
        );
    }
}
