# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

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
