# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [0.4.0](https://github.com/sonata-project/SonataDashboardBundle/compare/0.3.0...0.4.0) - 2020-02-01
### Added
- Added Spanish translations.
- Added support for new `EditableBlockService`
- Add missing translation for breadcrumbs
- Add missing translation for admin menu

### Removed
- Remove `edited` property from `Dashboard` entity

## [0.3.0](https://github.com/sonata-project/SonataMediaBundle/compare/0.2.0...0.3.0) - 2018-03-12
### Added
- missing french translations
- Added all missing stuff, so you could finally use this bundle

### Changed
- Changed fallback translation domain to `SonataBlockBundle` in composer
- Calling internal controller methods instead of create new exception instances
- Removed usage of old form type aliases
- Switch all templates references to Twig namespaced syntax
- Switch from templating service to sonata.templating
- Throw exception if wrong element returned
- Replaced deprecated setDefaultSettings with configureSettings method
- Moved id property to model

### Fixed
- Fixed PHPDoc
- Fixed calling deprecated methods
- Fixed typo when calling method `rollBack`
- It is now allowed to install Symfony 4
- Fixed wrong / missing PHPDoc

### Removed
- Removed support for PHP 5
- Removed support for symfony <2.8 and 3.0
- Support for old versions of php
- classes to compile

## [0.2.0](https://github.com/sonata-project/SonataMediaBundle/compare/0.1.0...0.2.0) - 2017-08-01
### Changed
- Changed `BlockAdmin` and `DashboardAdmin` extends to use recommended `AbstractAdmin` class.

### Fixed
- Removed duplicate tramslation of form groups
- Fixed duplicate translation in tab menu
- Fixed duplicate translation of form help
- Fixed hardcoded paths to classes in `.xml.skeleton` files of config

## Removed
- Internal test classes are now excluded from the autoloader
