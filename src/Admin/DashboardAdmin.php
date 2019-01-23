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

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DashboardBundle\Model\DashboardInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Admin definition for the Dashboard class.
 *
 * @author Quentin Somazzi <qsomazzi@ekino.com>
 */
final class DashboardAdmin extends AbstractAdmin
{
    protected $accessMapping = [
        'compose' => 'EDIT',
        'composeContainerShow' => 'LIST',
        'render' => 'EDIT',
    ];

    public function configureRoutes(RouteCollection $collection): void
    {
        $collection->add('compose', '{id}/compose', [
            'id' => null,
        ]);
        $collection->add('compose_container_show', 'compose/container/{id}', [
            'id' => null,
        ]);
        $collection->add('render', '{id}/render', [
            'id' => null,
        ]);
    }

    public function preUpdate($object): void
    {
        if (!$object instanceof DashboardInterface) {
            throw new \InvalidArgumentException('Invalid dashboard object');
        }

        $object->setEdited(true);
    }

    public function prePersist($object): void
    {
        if (!$object instanceof DashboardInterface) {
            throw new \InvalidArgumentException('Invalid dashboard object');
        }

        $object->setEdited(true);
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('name')
            ->add('default')
            ->add('enabled')
            ->add('edited')
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('name')
            ->add('default')
            ->add('enabled', null, ['editable' => true])
            ->add('edited', null, ['editable' => true])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
            ->add('edited')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        // define group zoning
        $formMapper
            ->with('form_dashboard.group_main_label', ['class' => 'col-md-12'])->end()
        ;

        $formMapper
            ->with('form_dashboard.group_main_label')
                ->add('name')
                ->add('default', CheckboxType::class, [
                    'required' => false,
                ])
                ->add('enabled', CheckboxType::class, [
                    'required' => false,
                ])
            ->end()
        ;

        $formMapper->setHelps([
            'name' => 'help_dashboard_name',
        ]);
    }

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null): void
    {
        if (!$childAdmin && !\in_array($action, ['edit'], true)) {
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

        $menu->addChild('sidemenu.link_render_dashboard', [
            'uri' => $admin->generateUrl('render', [
                'id' => $id,
            ]),
        ]);

        $menu->addChild('sidemenu.link_list_blocks', [
            'uri' => $admin->generateUrl('sonata.dashboard.admin.dashboard|sonata.dashboard.admin.block.list', [
                'id' => $id,
            ]),
        ]);
    }
}
