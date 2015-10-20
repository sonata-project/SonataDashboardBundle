<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Dashboard Admin Controller.
 *
 * @author Quentin Somazzi <qsomazzi@ekino.com>
 */
class DashboardAdminController extends CRUDController
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws AccessDeniedException
     */
    public function composeAction(Request $request = null)
    {
        $this->admin->checkAccess('compose');
        if (false === $this->get('sonata.dashboard.admin.block')->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $id   = $request->get($this->admin->getIdParameter());
        $dashboard = $this->admin->getObject($id);
        if (!$dashboard) {
            throw new NotFoundHttpException(sprintf('unable to find the dashboard with id : %s', $id));
        }

        $containers = array();

        // separate containers
        foreach ($dashboard->getBlocks() as $block) {
            $blockCode = $block->getSetting('code');
            if ($block->getParent() === null) {
                $containers[$blockCode]['block'] = $block;
            }
        }

        $csrfProvider = $this->get('form.csrf_provider');

        return $this->render('SonataDashboardBundle:DashboardAdmin:compose.html.twig', array(
            'object'     => $dashboard,
            'action'     => 'edit',
            'containers' => $containers,
            'csrfTokens' => array(
                'remove' => $csrfProvider->generateCsrfToken('sonata.delete'),
            ),
        ));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function composeContainerShowAction(Request $request = null)
    {
        if (false === $this->get('sonata.dashboard.admin.block')->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $id    = $request->get($this->admin->getIdParameter());
        $block = $this->get('sonata.dashboard.admin.block')->getObject($id);
        if (!$block) {
            throw new NotFoundHttpException(sprintf('unable to find the block with id : %s', $id));
        }

        $blockServices = $this->get('sonata.block.manager')->getServicesByContext('sonata_dashboard_bundle', false);

        return $this->render('SonataDashboardBundle:DashboardAdmin:compose_container_show.html.twig', array(
            'blockServices' => $blockServices,
            'container'     => $block,
            'dashboard'     => $block->getDashboard(),
        ));
    }
}
