<?php

/**
 * This file is part of NestedSet.
 *
 * (c) Henrik Thesing <mail@henrikthesing.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Henrik Thesing <mail@henrikthesing.de>
 */

namespace HenrikThesing\NestedSet\Factory;

use HenrikThesing\NestedSet\Exception\InvalidServiceConfigurationException;
use HenrikThesing\NestedSet\Exception\InvalidServiceNameException;
use HenrikThesing\NestedSet\Service\NestedSetService;
use HenrikThesing\NestedSet\Mapper\SqlNestedSetMapper;

use Zend\Filter\Word\CamelCaseToUnderscore;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\ClassMethods;

class AbstractServiceFactory implements AbstractFactoryInterface
{
    protected $serviceNamespace = 'HenrikThesing\NestedSet\Service';

    /**
     * Determine if we can create a service with the requested name.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return substr($requestedName, 0, mb_strlen($this->serviceNamespace)) === $this->serviceNamespace;
    }

    /**
     * Create a service with the given name.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return NestedSetService
     * @throws InvalidServiceConfigurationException
     * @throws InvalidServiceNameException
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $extracted = $this->extractRequestedName($requestedName);
        if (count($extracted) === 0) {
            throw new InvalidServiceNameException('Could not map service for '.strip_tags($requestedName));
        }

        $alias = $extracted['alias'];
        $config = $serviceLocator->get('Config');

        if (!array_key_exists($alias, $config['henrikthesing']['nested_set'])) {
            throw new InvalidServiceConfigurationException('Configuration for "'.strip_tags($alias).'" not found');
        }

        $config = $config['henrikthesing']['nested_set'][$alias];

        $mapper = new SqlNestedSetMapper(
            $serviceLocator,
            $serviceLocator->get($config['database_adapter']),
            new ClassMethods(),
            $config['table_name']
        );

        return new NestedSetService($mapper);
    }

    /**
     * @param $requestedName
     *
     * @return array
     */
    protected function extractRequestedName($requestedName)
    {
        $filter = new CamelCaseToUnderscore();
        $alias  = mb_strtolower($filter->filter(str_replace($this->serviceNamespace.'\\', '', $requestedName)));
        $alias = str_replace(['nested_set_', '_service'], '', $alias);
        return ['alias' => $alias];
    }
}