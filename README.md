NestedSet
=========

henrikthesing/nested-set is a Zend Framework 2 implementation of Nested Set

https://de.wikipedia.org/wiki/Nested_Sets

Requirements
------------

* PHP >= 5.3.3
* zendframework ~2.3.0
* phpunit 4.8.*

Installation
------------

Add the nested set module to your applications composer.json file:

```
{
    "require": {
        "henrikthesing/nested-set": "^1.0.0"
    }
}
```

Install Composer

```
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

Now tell composer to download the library by running the following command:

``` bash
$ composer update henrikthesing/nested-set
```

Composer will install the bundle into your project's `vendor/henrikthesing` directory.


Add the nested set module to the `application.config.php` file of your application

```
    'modules' => [
        'HenrikThesing\NestedSet'
    ],
```

Usage
-----

todo

Contribute
----------

[See contributing file](CONTRIBUTING.md)