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

namespace HenrikThesing\NestedSet\Hydrator;

use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\Stdlib\Hydrator\NamingStrategy\NamingStrategyInterface;

class MapperNamingStrategy implements NamingStrategyInterface
{
    /**
     * Converts the given name so that it can be extracted by the hydrator.
     *
     * @param string $name The original name
     *
     * @return mixed The hydrated name
     */
    public function hydrate($name)
    {
        $filter = new UnderscoreToCamelCase();
        $name = lcfirst($filter->filter($name));

        return $name;
    }

    /**
     * Converts the given name so that it can be hydrated by the hydrator.
     *
     * @param string $name The original name
     *
     * @return mixed The extracted name
     */
    public function extract($name)
    {
        if (!preg_match('/^is/', $name)) {
            return $name;
        }

        return lcfirst(substr($name, 2));
    }
}