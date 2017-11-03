<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt BSD 3-Clause License
 */

/**
 * @author Somayeh Karami <somayeh.karami@gmail.com>
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