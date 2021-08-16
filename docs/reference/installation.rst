.. index::
    single: Installation
    single: Configuration

Installation
============

Prerequisites
-------------

PHP ^7.3 and Symfony ^4.4 are needed to make this bundle work, there are
also some Sonata dependencies that need to be installed and configured beforehand.

Required dependencies:

* `SonataAdminBundle <https://docs.sonata-project.org/projects/SonataAdminBundle/en/3.x/>`_
* `SonataCacheBundle <https://docs.sonata-project.org/projects/SonataCacheBundle/en/3.x/>`_
* `SonataBlockBundle <https://docs.sonata-project.org/projects/SonataBlockBundle/en/3.x/>`_
* `SonataNotificationBundle <https://docs.sonata-project.org/projects/SonataNotificationBundle/en/3.x/>`_

And the persistence bundle (currently, not all the implementations of the Sonata persistence bundles are available):

* `SonataDoctrineOrmAdminBundle <https://docs.sonata-project.org/projects/SonataDoctrineORMAdminBundle/en/3.x/>`_

Follow also their configuration step; you will find everything you need in
their own installation chapter.

.. note::

    If a dependency is already installed somewhere in your project or in
    another dependency, you won't need to install it again.

Enable the Bundle
-----------------

Add ``SonataDashboardBundle`` via composer::

    composer require sonata-project/dashboard-bundle

Next, be sure to enable the bundles in your ``config/bundles.php`` file if they
are not already enabled::

    // config/bundles.php

    return [
        // ...
        Sonata\DashboardBundle\SonataDashboardBundle::class => ['all' => true],
    ];

Configuration
=============

SonataDashboardBundle Configuration
-----------------------------------

.. code-block:: yaml

    # config/packages/sonata_dashboard.yaml

    sonata_dashboard:
        default_container: sonata.dashboard.block.container
        class:
            dashboard: App\Entity\SonataDashboardDashboard
            block: App\Entity\SonataDashboardBlock

Doctrine ORM Configuration
--------------------------

Add the in the config mapping definition (or enable `auto_mapping`_)::

    # config/packages/doctrine.yaml

    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        SonataDashboardBundle: ~

And then create the corresponding entities, ``src/Entity/SonataDashboardDashboard``::

    // src/Entity/SonataDashboardDashboard.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\DashboardBundle\Entity\BaseDashboard;

    /**
     * @ORM\Entity
     * @ORM\Table(name="dashboard__tag")
     */
    class SonataDashboardDashboard extends BaseDashboard
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

and ``src/Entity/SonataDashboardBlock``::

    // src/Entity/SonataDashboardBlock.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\DashboardBundle\Entity\BaseBlock;

    /**
     * @ORM\Entity
     * @ORM\Table(name="dashboard__context")
     */
    class SonataDashboardBlock extends BaseBlock
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

The only thing left is to update your schema::

    bin/console doctrine:schema:update --force

Next Steps
----------

At this point, your Symfony installation should be fully functional, without errors
showing up from SonataDashboardBundle. If, at this point or during the installation,
you come across any errors, don't panic:

    - Read the error message carefully. Try to find out exactly which bundle is causing the error.
      Is it SonataDashboardBundle or one of the dependencies?
    - Make sure you followed all the instructions correctly, for both SonataDashboardBundle and its dependencies.
    - Still no luck? Try checking the project's `open issues on GitHub`_.

.. _`open issues on GitHub`: https://github.com/sonata-project/SonataDashboardBundle/issues
.. _`auto_mapping`: http://symfony.com/doc/4.4/reference/configuration/doctrine.html#configuration-overviews
