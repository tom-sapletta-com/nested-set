# NestedSet

henrikthesing/nested-set is a Zend Framework 2 implementation of Nested Set

https://de.wikipedia.org/wiki/Nested_Sets

## Requirements

* PHP >= 5.3.3
* zendframework ~2.3.0
* phpunit 4.8.*

## Installation

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

## Usage

### Basics

### findAll()
To get an entity of every single node of the table - including the root node - call the findAll() method after creating the nestedSetService.

```
    $nestedSetService = $sm->get('HenrikThesing\NestedSet\Service\NestedSetMainNavigationService');
    $nodes = $nestedSetService->findAll();
```

### find()
To get an entity of a specific node by id, call the find() method with the node id as the first parameter after creating the nestedSetService.

```
    $nestedSetService = $sm->get('HenrikThesing\NestedSet\Service\NestedSetMainNavigationService');
    $nodes = $nestedSetService->find(3);
```

### findAllByRootId()
To get all node entities of a single root node call the findAllByRootId() method with the root node id as the first parameter after creating the nestedSetService.

```
    $nestedSetService = $sm->get('HenrikThesing\NestedSet\Service\NestedSetMainNavigationService');
    $nodes = $nestedSetService->findAllByRootId(2);
```

### getBranch()
To get the whole branch a given node entity is in, call the getBranch() method with the node entity as the first parameter after creating the nestedSetService.

```
    $nestedSetService = $sm->get('HenrikThesing\NestedSet\Service\NestedSetMainNavigationService');
    $node = $nestedSetService->find(3);
    $branch = $nestedSetService->getBranch($node);
```

### getAncestors()
To get all ancestor nodes of a given node entity, call the getAncestors() method with the node entity as the first parameter after creating the nestedSetService.

```
    $nestedSetService = $sm->get('HenrikThesing\NestedSet\Service\NestedSetMainNavigationService');
    $node = $nestedSetService->find(6);
    $branch = $nestedSetService->getAncestors($node);
```

### getParent()
To get the parent node of a given node entity, call the getParent() method with the node entity as the first parameter after creating the nestedSetService.

```
    $nestedSetService = $sm->get('HenrikThesing\NestedSet\Service\NestedSetMainNavigationService');
    $node = $nestedSetService->find(6);
    $branch = $nestedSetService->getParent($node);
```

### getDescendants()
To get all descendant nodes of a given node entity, call the getDescendants() method with the node entity as the first parameter after creating the nestedSetService.

```
    $nestedSetService = $sm->get('HenrikThesing\NestedSet\Service\NestedSetMainNavigationService');
    $node = $nestedSetService->find(4);
    $branch = $nestedSetService->getDescendants($node);
```

### getChildren()
To get the child nodes of a given node entity, call the getChildren() method with the node entity as the first parameter after creating the nestedSetService.

```
    $nestedSetService = $sm->get('HenrikThesing\NestedSet\Service\NestedSetMainNavigationService');
    $node = $nestedSetService->find(4);
    $branch = $nestedSetService->getChildren($node);
```

### getSiblings()
To get the siblings of a given node entity, call the getSiblings() method with the node entity as the first parameter after creating the nestedSetService.

```
    $nestedSetService = $sm->get('HenrikThesing\NestedSet\Service\NestedSetMainNavigationService');
    $node = $nestedSetService->find(6);
    $branch = $nestedSetService->getSiblings($node);
```

### getPath()
To get all node from the root node to the given node entity, call the getPath() method with the node entity as the first parameter after creating the nestedSetService.

```
    $nestedSetService = $sm->get('HenrikThesing\NestedSet\Service\NestedSetMainNavigationService');
    $node = $nestedSetService->find(2);
    $branch = $nestedSetService->getPath($node);
```

## Contribute

[See contributing file](CONTRIBUTING.md)