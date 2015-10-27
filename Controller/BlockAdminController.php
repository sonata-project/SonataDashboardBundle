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

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Sonata\DashboardBundle\Entity\BaseBlock;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Block Admin Controller.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class BlockAdminController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function savePositionAction(Request $request = null)
    {
        $this->setSubject($request->get('block_id'));
        $this->admin->checkAccess('savePosition');

        try {
            $params = $request->get('disposition');

            if (!is_array($params)) {
                throw new HttpException(400, 'wrong parameters');
            }

            $result = $this->get('sonata.dashboard.block_interactor')->saveBlocksPosition($params, false);
            $status = 200;
        } catch (HttpException $e) {
            $status = $e->getStatusCode();
            $result = array(
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
                'code'      => $e->getCode(),
            );
        } catch (\Exception $e) {
            $status = 500;
            $result = array(
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
                'code'      => $e->getCode(),
            );
        }

        $result = ($result === true) ? 'ok' : $result;

        return $this->renderJson(array('result' => $result), $status);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request = null)
    {
        $this->admin->checkAccess('create');

        if (!$this->admin->getParent()) {
            throw new NotFoundHttpException('You cannot create a block without a dashboard');
        }

        $parameters = $this->admin->getPersistentParameters();

        if (!$parameters['type']) {
            return $this->render('SonataDashboardBundle:BlockAdmin:select_type.html.twig', array(
                'services'      => $this->get('sonata.block.manager')->getServicesByContext('sonata_dashboard_bundle'),
                'base_template' => $this->getBaseTemplate(),
                'admin'         => $this->admin,
                'action'        => 'create',
            ));
        } elseif ($parameters['type'] == $this->admin->getDefaultContainerType()) {
            $dashboard = $this->admin->getParent()->getSubject();
            $position  = count($dashboard->getBlocks()) + 1;
            $name      = $request->get('name') != '' ? $request->get('name') : $this->admin->trans('composer.default.container.name', array('%position%' => $position), '');

            $container = $this->get('sonata.dashboard.block_interactor')->createNewContainer(array(
                'name'      => $name,
                'dashboard' => $dashboard,
                'position'  => $position,
                'code'      => $name,
            ));

            return $this->render('SonataDashboardBundle:BlockAdmin:block_container.html.twig', array(
                'admin'  => $this->admin->getParent(),
                'object' => $container,
            ));
        }

        return parent::createAction();
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function switchParentAction(Request $request = null)
    {
        $blockId  = $request->get('block_id');
        $parentId = $request->get('parent_id');
        if ($blockId === null or $parentId === null) {
            throw new HttpException(400, 'wrong parameters');
        }

        $block = $this->setSubject($blockId);
        $this->admin->checkAccess('switchParent');

        $parent = $this->admin->getObject($parentId);
        if (!$parent) {
            throw new NotFoundHttpException(sprintf('Unable to find parent block with id %d', $parentId));
        }

        $parent->addChildren($block);
        $this->admin->update($parent);

        return $this->renderJson(array('result' => 'ok'));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function composePreviewAction(Request $request = null)
    {
        $block = $this->setSubject($request->get('block_id'));
        $this->admin->checkAccess('composePreview');

        $container = $block->getParent();
        if (!$container) {
            throw new NotFoundHttpException('No parent found');
        }

        $blockServices = $this->get('sonata.block.manager')->getServicesByContext('sonata_dashboard_bundle', false);

        return $this->render('SonataDashboardBundle:BlockAdmin:compose_preview.html.twig', array(
            'container'     => $container,
            'child'         => $block,
            'blockServices' => $blockServices,
        ));
    }

    /**
     * Initialize the admin subject, to contextualize checkAccess verification.
     *
     * @param $blockId
     *
     * @return BaseBlock
     */
    private function setSubject($blockId)
    {
        /** @var BaseBlock $block */
        $block = $this->admin->getObject($blockId);
        if (!$block) {
            throw new NotFoundHttpException(sprintf('Unable to find block with id %d', $blockId));
        }
        $this->admin->setSubject($block);

        return $block;
    }
}
