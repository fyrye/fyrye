# Contributing

## Unit Testing
All tests with this project can be manually run by issuing 

```
vendor/bin/phpunit -c /vendor/fyrye/php-units-of-measure-bundle/phpunit.xml.dist /vendor/fyrye/php-units-of-measure-bundle/Tests
```

## Pull Requests
Please create all pull requests against the
[fyrye/fyrye Repository](https://github.com/fyrye/php-units-of-measure-bundle/pull/new/master) 
using the [.github/PULL_REQUEST_TEMPLATE](https://github.com/fyrye/fyrye/blob/master/.github/PULL_REQUEST_TEMPLATE.md).

When making a pull request please follow the Semantic Versioning detailed below.  

##Semantic Versioning

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

## Reporting Issues

Please create issues related to this project at the 
[fyrye/fyrye Repository](https://github.com/fyrye/fyrye/issues/new/) 
using the [.github/ISSUE_TEMPLATE](https://github.com/fyrye/fyrye/blob/master/.github/ISSUE_TEMPLATE.md)

### Example:

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

#### Steps Performed
 1. `php composer.phar require fyrye/php-units-of-measure-bundle:^2.0`
 2. Added `new \Fyrye\Bundle\PhpUnitsOfMeasureBundle(),` to AppKernel Bundles 
 3. `php bin/console --env=dev cache:clear`

#### Expected
Symfony to successfully clear the cache
```
[OK] Cache for the "dev" environment (debug=true) was successfully cleared.
```

#### Result
Received Error Message 
```
Class '\Fyrye\Bundle\PhpUnitsOfMesureBundle' not found.
```

#### Additional Comments
Removing the bundle from AppKernel resolves the issue.
