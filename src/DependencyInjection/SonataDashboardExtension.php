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

use Sonata\Doctrine\Mapper\Builder\OptionsBuilder;
use Sonata\Doctrine\Mapper\DoctrineCollector;
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
        $bundles = $container->getParameter('kernel.bundles');

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('admin.xml');
        $loader->load('block.xml');
        $loader->load('dashboard.xml');
        $loader->load('http_kernel.xml');
        $loader->load('orm.xml');
        $loader->load('twig.xml');

        if (isset($bundles['SonataDoctrineBundle'])) {
            $this->registerSonataDoctrineMapping($config);
        } else {
            throw new \RuntimeException('You must register SonataDoctrineBundle to use SonataDashboardBundle.');
        }

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

    private function registerSonataDoctrineMapping(array $config): void
    {
        if (!class_exists($config['class']['dashboard'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation(
            $config['class']['dashboard'],
            'mapOneToMany',
            OptionsBuilder::createOneToMany('blocks', $config['class']['block'])
                ->cascade(['remove', 'persist', 'refresh', 'merge', 'detach'])
                ->mappedBy('dashboard')
                ->addOrder('position', 'ASC')
        );

        $collector->addAssociation(
            $config['class']['block'],
            'mapOneToMany',
            OptionsBuilder::createOneToMany('children', $config['class']['block'])
                ->cascade(['remove', 'persist'])
                ->mappedBy('parent')
                ->orphanRemoval()
                ->addOrder('position', 'ASC')
        );

        $collector->addAssociation(
            $config['class']['block'],
            'mapManyToOne',
            OptionsBuilder::createManyToOne('parent', $config['class']['block'])
                ->inversedBy('children')
                ->addJoin([
                    'name' => 'parent_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ])
        );

        $collector->addAssociation(
            $config['class']['block'],
            'mapManyToOne',
            OptionsBuilder::createManyToOne('dashboard', $config['class']['dashboard'])
                ->cascade(['persist'])
                ->inversedBy('blocks')
                ->addJoin([
                    'name' => 'dashboard_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ])
        );
    }
}
