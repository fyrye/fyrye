#PhpUnitsOfMeasureBundle

[![Build Status](https://travis-ci.org/fyrye/php-units-of-measure-bundle.svg?branch=master)](https://travis-ci.org/fyrye/php-units-of-measure-bundle)
[![Build status](https://ci.appveyor.com/api/projects/status/vv214lv6x0xvv01h/branch/master?svg=true)](https://ci.appveyor.com/project/fyrye/phpunitsofmeasurebundle/branch/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fyrye/php-units-of-measure-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fyrye/php-units-of-measure-bundle/?branch=master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/bed244cbc946459b8c23eb994f721b78)](https://www.codacy.com/app/fyrye/php-units-of-measure-bundle?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=fyrye/php-units-of-measure-bundle&amp;utm_campaign=Badge_Grade)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/eb5fe6b5-e19b-4511-b721-22201fc2e1c3/small.png)](https://insight.sensiolabs.com/projects/eb5fe6b5-e19b-4511-b721-22201fc2e1c3)

##Introduction

This package provides [php-units-of-measure](https://github.com/PhpUnitsOfMeasure/php-units-of-measure) 
support for use in [Symfony 2](https://github.com/symfony/symfony) projects.

A list of feature changes and implementations can be found in the [CHANGELOG](https://github.com/fyrye/php-units-of-measure-bundle/CHANGELOG.md) and upcoming changes can be found in the [TODO](https://github.com/fyrye/php-units-of-measure-bundle/TODO.md).

##Installation

The package is best included in your Symfony 2 project via Composer. 
See the [Composer website](http://getcomposer.org/) for more details.

See the [Packagist.org website](https://packagist.org/packages/fyrye/php-units-of-measure-bundle) 
for package details on this library.

Issue the following command to include the package into your project libraries.

```
php composer.phar require fyrye/php-units-of-measure-bundle
```

###Manual Download

Download the desired version from the 
[releases section](https://github.com/fyrye/php-units-of-measure-bundle/releases).

Extract the source files into your project libraries directory.

Use a [PSR-4 autoloader](http://www.php-fig.org/psr/psr-4/) to autoload the project classes for your Symfony 2 project.

###Enable the Bundle

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

##Getting Started
WIP

###Usage
WIP

###Configuration
WIP

##Testing and Contributing

###Unit Testing
All tests with this project can be manually run by issuing 

```
vendor/bin/phpunit -c /vendor/fyrye/php-units-of-measure-bundle/phpunit.xml.dist /vendor/fyrye/php-units-of-measure-bundle/Tests
```

###Pull Requests
Please create all pull requests against the
[fyrye/fyrye Repository](https://github.com/fyrye/fyrye/issues/new/) 
using the [.github/PULL_REQUEST_TEMPLATE](https://github.com/fyrye/fyrye/.github/PULL_REQUEST_TEMPLATE.md).

When making a pull request please follow the Semantic Versioning detailed below.  

###Semantic Versioning

In order to identify the compatibility characteristics of any version
of the service, we can adopt a form of [semantic
versioning](http://semver.org/) tailored to the notions of binary
compatibility.  With this proposal, the form the definitions of major,
minor and patch numbers look like this:

    MAJOR.MINOR.PATCH

An increment of the MAJOR (first) number represents an binary backward
incompatible upgrade to the previous version.  Clients will not be
able to connect to this version of the service without updating their
service stubs to the latest version, which will require recompilation
and changes to the source.

In other words, you can't connect to a 2.* service with stubs generated
from a 1.* version of the IDL.

The meaning of MINOR and PATCH increments would still align with the
conventional semantic version rules.

###Reporting Issues

Please create issues related to this project at the 
[fyrye/fyrye Repository](https://github.com/fyrye/fyrye/issues/new/) 
using the [.github/ISSUE_TEMPLATE](https://github.com/fyrye/fyrye/.github/ISSUE_TEMPLATE.md)

##### Example:

| Q                  | A                                      |
| ------------------ | -----                                  |
| Bug report?        | yes                                    |
| Feature request?   | no                                     |
| BC Break report?   | no                                     |
| RFC?               | no                                     |
| OS                 | Window 10 pro x64                      |
| PHP version        | 5.6.28-x64-NTS                         |
| Symfony version    | 3.2.1                                  |
| Related Package(s) | fyrye/php-units-of-measure-bundle:^2.0 |

####Steps Performed
 1. `php composer.phar require fyrye/php-units-of-measure-bundle:^2.0`
 2. Added `new \Fyrye\Bundle\PhpUnitsOfMeasureBundle(),` to AppKernel Bundles 
 3. `php bin/console --env=dev cache:clear`

####Expected
Symfony to successfully clear the cache
```
[OK] Cache for the "dev" environment (debug=true) was successfully cleared.
```

####Result
Received Error Message 
```
Class '\Fyrye\Bundle\PhpUnitsOfMesureBundle' not found.
```

####Additional Comments
Removing the bundle from AppKernel resolves the issue.


