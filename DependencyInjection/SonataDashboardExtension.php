<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundle\DependencyInjection;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class SonataDashboardExtension.
 *
 * @author  Quentin Somazzi <qsomazzi@ekino.com>
 */
class SonataDashboardExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('dashboard.xml');
        $loader->load('admin.xml');
        $loader->load('block.xml');
        $loader->load('orm.xml');
        $loader->load('twig.xml');

        $this->registerDoctrineMapping($config);
        $this->registerParameters($container, $config);
    }

    /**
     * Registers service parameters from bundle configuration.
     *
     * @param ContainerBuilder $container Container builder
     * @param array            $config    Array of configuration
     */
    public function registerParameters(ContainerBuilder $container, array $config)
    {
        $container->setParameter('sonata.dashboard.block.class', $config['class']['block']);
        $container->setParameter('sonata.dashboard.dashboard.class', $config['class']['dashboard']);

        $container->setParameter('sonata.dashboard.admin.block.entity', $config['class']['block']);
        $container->setParameter('sonata.dashboard.admin.dashboard.entity', $config['class']['dashboard']);

        $container->setParameter(
            'sonata.dashboard.admin.dashboard.templates.compose',
            $config['templates']['compose']
        );
        $container->setParameter(
            'sonata.dashboard.admin.dashboard.templates.compose_container_show',
            $config['templates']['compose_container_show']
        );
        $container->setParameter(
            'sonata.dashboard.admin.dashboard.templates.render',
            $config['templates']['render']
        );
        
        //@todo : check this container is a service
        //if (!$container->hasDefinition($config['default_container'])) {
        //    throw new \RuntimeException(sprintf('The container %s must be an existing service', $config['default_container']));
        //}
        $container->setParameter('sonata.dashboard.default_container', $config['default_container']);
    }

    /**
     * Registers doctrine mapping on concrete dashboard entities.
     *
     * @param array $config
     */
    public function registerDoctrineMapping(array $config)
    {
        if (!class_exists($config['class']['dashboard'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['dashboard'], 'mapOneToMany', array(
            'fieldName' => 'blocks',
            'targetEntity' => $config['class']['block'],
            'cascade' => array(
                'remove',
                'persist',
                'refresh',
                'merge',
                'detach',
            ),
            'mappedBy' => 'dashboard',
            'orphanRemoval' => false,
            'orderBy' => array(
                'position' => 'ASC',
            ),
        ));

        $collector->addAssociation($config['class']['block'], 'mapOneToMany', array(
            'fieldName' => 'children',
            'targetEntity' => $config['class']['block'],
            'cascade' => array(
                'remove',
                'persist',
            ),
            'mappedBy' => 'parent',
            'orphanRemoval' => true,
            'orderBy' => array(
                'position' => 'ASC',
            ),
        ));

        $collector->addAssociation($config['class']['block'], 'mapManyToOne', array(
            'fieldName' => 'parent',
            'targetEntity' => $config['class']['block'],
            'cascade' => array(
            ),
            'mappedBy' => null,
            'inversedBy' => 'children',
            'joinColumns' => array(
                array(
                    'name' => 'parent_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ),
            ),
            'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['block'], 'mapManyToOne', array(
            'fieldName' => 'dashboard',
            'targetEntity' => $config['class']['dashboard'],
            'cascade' => array(
                'persist',
            ),
            'mappedBy' => null,
            'inversedBy' => 'blocks',
            'joinColumns' => array(
                array(
                    'name' => 'dashboard_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ),
            ),
            'orphanRemoval' => false,
        ));
    }

    /**
     * Add class to compile.
     */
    public function configureClassesToCompile()
    {
        $this->addClassesToCompile(array(
            'Sonata\\DashboardBundle\\Block\\ContainerBlockService',
            'Sonata\\DashboardBundle\\CmsManager\\CmsManagerInterface',
            'Sonata\\DashboardBundle\\CmsManager\\CmsManagerSelector',
            'Sonata\\DashboardBundle\\CmsManager\\CmsManagerSelectorInterface',
            'Sonata\\DashboardBundle\\CmsManager\\CmsDashboardManager',
            'Sonata\\DashboardBundle\\Entity\\BaseBlock',
            'Sonata\\DashboardBundle\\Entity\\BaseDashboard',
            'Sonata\\DashboardBundle\\Entity\\BlockInteractor',
            'Sonata\\DashboardBundle\\Entity\\BlockManager',
            'Sonata\\DashboardBundle\\Entity\\DashboardManager',
            'Sonata\\DashboardBundle\\Model\\Block',
            'Sonata\\DashboardBundle\\Model\\BlockManagerInterface',
            'Sonata\\DashboardBundle\\Model\\BlockInteractorInterface',
            'Sonata\\DashboardBundle\\Model\\Dashboard',
            'Sonata\\DashboardBundle\\Model\\DashboardBlockInterface',
            'Sonata\\DashboardBundle\\Model\\DashboardInterface',
            'Sonata\\DashboardBundle\\Model\\DashboardManagerInterface',
            'Sonata\\DashboardBundle\\Twig\\Extension\\DashboardExtension',
            'Sonata\\DashboardBundle\\Twig\\GlobalVariables',
        ));
    }
}
