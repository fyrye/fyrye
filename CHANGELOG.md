# PhpUnitsOfMeasureBundle Changelog
## Version 3.0
###### 2018-11-05

- Updated to support Symfony 3.4+
- Refactored named services as aliases
- Removed parameters:
    - `php_units_of_measure.registry.namespace`
    - `php_units_of_measure.library.namespace`
    - `php_units_of_measure.quantity_definition.class`
    - `php_units_of_measure.physical_quantity.class`
    - `php_units_of_measure.bundles`
- Renamed service declarations in favor of using `::class`:
    - `php_units_of_measure.registry_manager` => `Fyrye\Bundle\PhpUnitsOfMeasureBundle\Registry\Manager`
    - `php_units_of_measure.twig_extension` => `Fyrye\Bundle\PhpUnitsOfMeasureBundle\DependencyInjection\TwigExtension`
- Refactored `Fyrye\Bundle\PhpUnitsOfMeasureBundle\Registry\Manager` from singleton pattern to instanced
- Removed `Fyrye\Bundle\PhpUnitsOfMeasureBundle\Registry\Manager::getInstance()` method
- Renamed `auto` option of `none` to `manual`
- Removed `auto` option of `bundles`
- Removed `bundles` option in favor of using tagging with `autoconfigure=true`
- Added service tagging support for custom quantities with tag name `php_units_of_measure.extension`

## Version 2.1.1 
###### 2017-01-16 

- Added support for dynamic loading of the twig extension.

## Version 2.1.0 
###### 2017-01-16 

Initial Release.
