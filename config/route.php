<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
return [
    // route name
    'video' => [
        'name'    => 'video',
        'type'    => 'Module\Video\Route\Video',
        'options' => [
            'route'    => '/video',
            'defaults' => [
                'module'     => 'video',
                'controller' => 'index',
                'action'     => 'index',
            ],
        ],
    ],
];
