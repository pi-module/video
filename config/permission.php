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
    // Admin section
    'admin' => [
        'video'     => [
            'title'  => _a('Video'),
            'access' => [],
        ],
        'category'  => [
            'title'  => _a('Category'),
            'access' => [],
        ],
        'server'    => [
            'title'  => _a('Server'),
            'access' => [],
        ],
        'attribute' => [
            'title'  => _a('Attribute'),
            'access' => [],
        ],
        'log'       => [
            'title'  => _a('Logs'),
            'access' => [],
        ],
        'tools'     => [
            'title'  => _a('Tools'),
            'access' => [],
        ],
        'json'      => [
            'title'  => _a('Json'),
            'access' => [],
        ],
    ],
    // Front section
    'front' => [
        'public' => [
            'title'  => _a('Global public resource'),
            'access' => [
                'guest',
                'member',
            ],
        ],

        'submit' => [
            'title'  => _a('Submit'),
            'access' => [
                'member',
            ],
        ],

        'channel' => [
            'title'  => _a('Channel'),
            'access' => [
                'guest',
                'member',
            ],
        ],
    ],
];