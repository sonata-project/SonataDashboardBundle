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

namespace Sonata\DashboardBundle\DependencyInjection;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author  Quentin Somazzi <qsomazzi@ekino.com>
 */
final class SonataDashboardExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('admin.xml');
        $loader->load('block.xml');
        $loader->load('dashboard.xml');
        $loader->load('http_kernel.xml');
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
    public function registerParameters(ContainerBuilder $container, array $config): void
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

        $container->setParameter('sonata.dashboard.default_container', $config['default_container']);
    }

    /**
     * Registers doctrine mapping on concrete dashboard entities.
     */
    public function registerDoctrineMapping(array $config): void
    {
        if (!class_exists($config['class']['dashboard'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['dashboard'], 'mapOneToMany', [
            'fieldName' => 'blocks',
            'targetEntity' => $config['class']['block'],
            'cascade' => [
                'remove',
                'persist',
                'refresh',
                'merge',
                'detach',
            ],
            'mappedBy' => 'dashboard',
            'orphanRemoval' => false,
            'orderBy' => [
                'position' => 'ASC',
            ],
        ]);

        $collector->addAssociation($config['class']['block'], 'mapOneToMany', [
            'fieldName' => 'children',
            'targetEntity' => $config['class']['block'],
            'cascade' => [
                'remove',
                'persist',
            ],
            'mappedBy' => 'parent',
            'orphanRemoval' => true,
            'orderBy' => [
                'position' => 'ASC',
            ],
        ]);

        $collector->addAssociation($config['class']['block'], 'mapManyToOne', [
            'fieldName' => 'parent',
            'targetEntity' => $config['class']['block'],
            'cascade' => [
            ],
            'mappedBy' => null,
            'inversedBy' => 'children',
            'joinColumns' => [
                [
                    'name' => 'parent_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['block'], 'mapManyToOne', [
            'fieldName' => 'dashboard',
            'targetEntity' => $config['class']['dashboard'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => null,
            'inversedBy' => 'blocks',
            'joinColumns' => [
                [
                    'name' => 'dashboard_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ],
            ],
            'orphanRemoval' => false,
        ]);
    }
}
