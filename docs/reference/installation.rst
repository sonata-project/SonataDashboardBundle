Installation
============

Prerequisites
-------------
PHP 5.3 and Symfony 2 are needed to make this bundle work ; there are also some
Sonata dependencies that need to be installed and configured beforehand:

    - `SonataCacheBundle <https://sonata-project.org/bundles/cache>`_
    - `SonataBlockBundle <https://sonata-project.org/bundles/block>`_
    - `SonataEasyExtendsBundle <https://sonata-project.org/bundles/easy-extends>`_
    - `SonataNotificationBundle <https://sonata-project.org/bundles/notification>`_
    - `SonataAdminBundle <https://sonata-project.org/bundles/admin>`_
    - `SonataDoctrineORMAdminBundle <https://sonata-project.org/bundles/doctrine-orm-admin>`_

Follow also their configuration steps; you will find everything you need in their installation chapter.

.. note::
    If a dependency is already installed somewhere in your project or in
    another dependency, you won't need to install it again.

Enable the Bundle
-----------------
Add the dependant bundles to the vendor/bundles directory:

.. code-block:: bash

    php composer.phar require sonata-project/dashboard-bundle --no-update
    php composer.phar require sonata-project/datagrid-bundle 2.2.*@dev --no-update
    php composer.phar require sonata-project/doctrine-orm-admin-bundle --no-update
    php composer.phar update

.. note::

    The SonataAdminBundle and SonataDoctrineORMAdminBundle must be installed, please refer to `the dedicated documentation for more information <https://sonata-project.org/bundles/admin>`_.

    The `SonataDatagridBundle <https://github.com/sonata-project/SonataDatagridBundle>`_ must be added in ``composer.json`` for SonataPageBundle versions above 2.3.6

Next, be sure to enable the ``Dashboard`` and ``EasyExtends`` bundles in your application kernel:

.. code-block:: php

    <?php
    // app/appkernel.php
    public function registerbundles()
    {
        return array(
            // ...
            new Sonata\DashboardBundle\SonataDashboardBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            // ...
        );
    }

Before we can go on with generating our Application files trough the ``EasyExtends`` bundle, we need to add some lines which we will override later (we need them now only for the following step):

.. code-block:: yaml

    sonata_dashboard:
        default_container: sonata.dashboard.block.container
        # Entity Classes
        class:
            dashboard: Application\Sonata\DashboardBundle\Entity\Dashboard
            block:     Application\Sonata\DashboardBundle\Entity\Block


Configuration
-------------
To use the ``DashboardBundle``, add the following lines to your application
configuration file.

.. note::
    If your ``auto_mapping`` have a ``false`` value, add these lines to your
    mapping configuration :

    .. code-block:: yaml

        # app/config/config.yml
        doctrine:
            orm:
                entity_managers:
                    default:
                        mappings:
                            ApplicationSonataDashboardBundle: ~ # only once the ApplicationSonataDashboardBundle is generated
                            SonataDashboardBundle: ~


Extend the Bundle
-----------------
At this point, the bundle is usable, but not quite ready yet. You need to
generate the correct entities for the dashboard:

.. code-block:: bash

    php app/console sonata:easy-extends:generate SonataDashboardBundle

If you specify no parameter, the files are generated in app/Application/Sonata... but you can specify the path with --dest=src

.. note::

    The command will generate domain objects in an ``Application`` namespace.
    So you can point entities associations to a global and common namespace.
    This will make entities sharing very easily as your models are accessible
    through a global namespace. For instance the dashboard will be
    ``Application\Sonata\DashboardBundle\Entity\Dashboard``.

Now, add the new `Application` Bundle to the kernel

.. code-block:: php

    <?php
    public function registerbundles()
    {
        return array(
            // ...

            // Application Bundles
            new Application\Sonata\DashboardBundle\ApplicationSonataDashboardBundle(),

            // ...
        );
    }

And now, you're good to go !
