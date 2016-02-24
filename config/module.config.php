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

return [
    'henrikthesing' => [
        'nested_set' => [
            'main_navigation' => [
                'database_adapter' => '##ENTER##YOUR##DATABASE##ADAPTER##NAME##', // My/Database/Adapter/Name
                'table_name' => 'navigation',
            ],
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'HenrikThesing\NestedSet\Factory\AbstractServiceFactory',
        ],
        'invokables' => [
            'HenrikThesing\NestedSet\Entity\NodeInterface' => 'HenrikThesing\NestedSet\Entity\Node',
        ],
        'shared' => [
            'HenrikThesing\NestedSet\Entity\NodeInterface' => false,
        ]
    ]
];