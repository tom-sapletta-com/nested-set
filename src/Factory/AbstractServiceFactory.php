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

use Interop\Container\ContainerInterface;
use Zend\Filter\Word\CamelCaseToUnderscore;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\Hydrator\ClassMethods;

class AbstractServiceFactory implements AbstractFactoryInterface
{
    protected $serviceNamespace = 'HenrikThesing\NestedSet\Service';

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $extracted = $this->extractRequestedName($requestedName);
        if (count($extracted) === 0) {
            throw new InvalidServiceNameException('Could not map service for '.strip_tags($requestedName));
        }

        $alias = $extracted['alias'];
        $config = $container->get('Config');

        if (!array_key_exists($alias, $config['henrikthesing']['nested_set'])) {
            throw new InvalidServiceConfigurationException('Configuration for "'.strip_tags($alias).'" not found');
        }

        $config = $config['henrikthesing']['nested_set'][$alias];

        $mapper = new SqlNestedSetMapper(
            $container,
            $container->get($config['database_adapter']),
            new ClassMethods(),
            $config['table_name']
        );

        return new NestedSetService($mapper);
    }

    /**
     * Determine if we can create a service with the requested name.
     *
     * @param ContainerInterface $container
     * @param $requestedName
     *
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return substr($requestedName, 0, mb_strlen($this->serviceNamespace)) === $this->serviceNamespace;
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