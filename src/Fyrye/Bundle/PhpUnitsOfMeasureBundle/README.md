# PhpUnitsOfMeasureBundle

[![Build Status](https://travis-ci.org/fyrye/php-units-of-measure-bundle.svg?branch=master)](https://travis-ci.org/fyrye/php-units-of-measure-bundle)
[![Build status](https://ci.appveyor.com/api/projects/status/vv214lv6x0xvv01h/branch/master?svg=true)](https://ci.appveyor.com/project/fyrye/phpunitsofmeasurebundle/branch/master)
[![Coverage Status](https://coveralls.io/repos/github/fyrye/php-units-of-measure-bundle/badge.svg?branch=master)](https://coveralls.io/github/fyrye/php-units-of-measure-bundle?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fyrye/php-units-of-measure-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fyrye/php-units-of-measure-bundle/?branch=master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/bed244cbc946459b8c23eb994f721b78)](https://www.codacy.com/app/fyrye/php-units-of-measure-bundle?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=fyrye/php-units-of-measure-bundle&amp;utm_campaign=Badge_Grade)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/eb5fe6b5-e19b-4511-b721-22201fc2e1c3/small.png)](https://insight.sensiolabs.com/projects/eb5fe6b5-e19b-4511-b721-22201fc2e1c3)

## Introduction

This package provides [php-units-of-measure](https://github.com/PhpUnitsOfMeasure/php-units-of-measure) 
support for use in [Symfony 2](https://github.com/symfony/symfony) projects.

A list of feature changes and implementations can be found in the [CHANGELOG](https://github.com/fyrye/php-units-of-measure-bundle/blob/master/CHANGELOG.md) and upcoming changes can be found in the [TODO](https://github.com/fyrye/php-units-of-measure-bundle/blob/master/TODO.md).

For contributing to this package please see [CONTRIBUTING](https://github.com/fyrye/php-units-of-measure-bundle/blob/master/CONTRIBUTING.md).

## Installation

The package is best included in your Symfony 2 project via Composer. 
See the [Composer website](http://getcomposer.org/) for more details.

See the [Packagist.org website](https://packagist.org/packages/fyrye/php-units-of-measure-bundle) 
for package details on this library.

Issue the following command to include the package into your project libraries.

```
php composer.phar require fyrye/php-units-of-measure-bundle
```

### Manual Download

Download the desired version from the 
[releases section](https://github.com/fyrye/php-units-of-measure-bundle/releases).

Extract the source files into your project libraries directory.

Use a [PSR-4 autoloader](http://www.php-fig.org/psr/psr-4/) to autoload the project classes for your Symfony 2 project.

### Enable the Bundle

Open your `app/AppKernel.php` file and add the package to your bundles configuration.

```php
class AppKernel
{
     public function registerBundles()
     {
         $bundles = [
             //...
             new Fyrye\Bundle\PhpUnitsOfMeasureBundle\PhpUnitsOfMeasureBundle(),
         ];
         
         //...
         
         return $bundles;
     }
}
```

## Getting Started

 1. [Use in a Controller](#controller)
 2. [Use in a Twig Template](#twig)
 3. [Configuration](#configuration)

### Usage

Enabled Physical Quantities are added as [Symfony Services](http://symfony.com/doc/current/service_container.html)
by name to the `php_units_of_measure.quantity.%physical_quanitty%` namespace.
Additionally the Registry Manager that holds all of the Physical Quantity definitions is
also available for use as `php_units_of_measure.quantity`.

All registered Physical Quantities become available as Twig Filters 
by using the `%value%|uom_%physical_quantity%(%from%, %to%)`. 

The registry manager is available as the `uom` function 
and optionally accepts the quantity, value and unit arguments. 
`uom(%quantity%, %value%, %unit)` or `uom().getUnit(%quantity%, %value%, %unit%)`.

Please see the Configuration section for details on how to enable or add
Physical Quantities and Units.

#### Controller

_`src/AppBundle/Controller/DefaultController.php`_
```php   
namespace AppBundle\Controller;
    
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    
class DefaultContoller extends Conoller
{
    
   public function indexAction()
   {
       //using the registry manager
       $uom = $this->controller->get('php_units_of_measure.quantity');
       $yard = $uom->getUnit('length', 1, 'yd');
       $feet = $yard->toUnit('ft'); //return 3
       
       //obtaining a physical quantity
       $length = $this->controller->get('php_units_of_measure.quantity.length');
       $feet = $length->getUnit(3, 'ft');
       $feet->toUnit('yd'); //returns 1
       
       return $this->render(':default:index.html.twig');
   }
    
}
```

#### Twig
_`:default:index.html.twig`_
```twig
{% block content %}
    1 yard = {{ 1|uom_length('yd', 'feet') }} feet
    1 mile = {{ uom('length', 2 'mi').toUnit('inches') }} inches
{% endblock %}
```

### Configuration

[Full Example](#full-configuration)

- [enabled](#configuration-enabled)
- [options](#configuration-options)
  - [auto](#configuration-auto)
- [bundles](#configuration-bundles)
- [units](#configuration-units)
  - [PhysicalQuantity](#configuration-physical-quantity)
    - [enabled](#configuration-physical-quantity-enabled)
    - [Unit](#configuration-physical-quantity-unit)
      - [factor](#configuration-physical-quantity-unit-factor)
      - [type](#configuration-physical-quantity-unit-type)
      - [aliases](#configuration-physical-quantity-unit-aliases)

<dl>
<dt id="configuration-enabled">enabled</dt>
<dd>
<p>
<b>type</b>: <code>boolean</code> <b>default</b>: <code>true</code>
</p>
<p>
Allows for disabling the Bundle from registering
the Physical Quantities as services.
</p>
</dd>
<dt id="configuration-options">options</dt>
<dd>
<p>
Collection of key value options sent to the registry manager.
</p>
<dl>
<dt id="configuration-auto">auto</dt>
<dd>
<p>
<b>type</b>: <code>boolean</code> <b>default</b>: <code>false</code>
</p>
<p>
Controls the automatic registration of integrated Physical Quantities available in
the php-units-of-measure package, that are not explicitly defined.
When this option is false, integrated physical quantities must be explicitly defined
within the `units` configuration section.
</p>
                    
<div class="highlight highlight-source-yaml">
<pre>
php_units_of_measure:
options:
auto: true
</pre>
</div>

</dd>
</dl>
</dd>
<dt id="configuration-bundles">bundles</dt>
<dd>
<p>
<b>type</b>: <code>array</code> <b>default</b>: <code>all bundles</code>
</p>
<p>
Collection of bundle names to scan the `BundleName/PhysicalQuantity`
directory and registers a service for all objects contained within the directory
that extends `PhpUnitsOfMeasure\AbstractPhysicalQuantity`.
</p>
</dd>
<dt id="configuration-auto">units</dt>
<dd>
<p>
Collection of Physical Quantities and their related unit configurations.
</p>
<dl>
<dt id="configuration-physical-quantity">PhysicalQuantity</dt>
<dd>
<p>
Collection of Physical Quantity names and associated Unit configurations.
</p>
<blockquote>
<p>
Physical Quantities that are integrated with the php-units-of-measure package
will be added as services.
If a Physical Quantity is specified that has not been integrated with
the php-units-of-measure package, a proxy object is registered to enable its
usage within the service container.
</p>
</blockquote>
<dl>
<dt id="configuration-physical-quantity-enabled">enabled</dt>
<dd>
<p>
<b>type</b>: <code>boolean</code> <b>default</b>: <code>true</code> <b>required</b>
</p>
<p>
Control the enabling of integrated Physical Quantities without
defining addition custom units.
</p>
</dd>
<dt id="configuration-physical-quantity-unit">Unit</dt>
<dd>
<p>
Specifies a custom unit for use with the parent physical quantity
</p>
<dl>
<dt id="configuration-physical-quantity-unit-factor">factor</dt>
<dd>
<p>
<b>type</b>: <code>float</code> <b>default</b>: <code>1</code>
</p>
<p>
The factor to apply to the unit when the specified type is linear
</p>
</dd>
<dt id="configuration-physical-quantity-unit-type">type</dt>
<dd>
<p>
<b>type</b>: <code>string</code> <b>default</b>: <code>linear</code>
</p>
<p>
Specifies the unit type as <code>linear</code> or <code>native</code>. When
specified as <code>native</code>
the factor used will always be set to <code>1</code>.
</p>
</dd>
<dt id="configuration-physical-quantity-unit-aliases">aliases</dt>
<dd>
<p>
<b>type</b>: <code>array</code>
</p>
<p>
Collection of alias names to use for the specified unit. Such as <code>ft</code>,
<code>feet</code>.
</p>
</dd>
</dl>
</dd>
</dl>
</dd>
</dl>
</dd>
</dl>

#### Full Configuration

_`app/config/config.yml`_

```yaml
php_units_of_measure:
    enabled: true #enable the service
    options:
       auto: true #enable auto registration of integrated Physical Quantities
    units:
       Length: #enable the Length Physical Quantity and add custom units 
           UltraMeter:
               factor: 100000000
               aliases: ['um']
       Mass:
           enabled: true #enable the Physical Quantity without changes
       CustomQuantity: #create a custom quantity with the following units
           CustomUnit:  #define the native unit
               type: native
               aliases: ['cqu']
           CustomUnitTest: #create another unit that is half of the native unit
               factor: .5
               aliases: ['cqut']
```


