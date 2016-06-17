Introduction
============

This small introduction will try to explain the basics concepts behind the
``DashboardBundle``.

A Dashboard
-----------

A ``Dashboard`` is a collection of blocks, actually of block containers, containing the
blocks required to manage its content.

A Block
-------

The ``SonataDashboardBundle`` does not know how to manage content, actually there is
no content management. This part is delegated to services. The bundle only
manages references to the service required by a dashboard. Reference information is
stored in a ``Block``.

A block is a small unit, it contains the following information:

 - service id
 - position
 - settings used by the service

Each block service must implement the ``Sonata\PageBundle\Block\BlockServiceInterface``
which defines a set of functions to create so the service can be integrated
nicely with editing workflow. The important information is that a block service
must always return a ``Response`` object.

By default the ``SonataDashboardBundle`` is shipped with a core block service:

 - sonata.dashboard.block.container      : Block container
