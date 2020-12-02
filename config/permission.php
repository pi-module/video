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
        'playlist'    => [
            'title'  => _a('Playlist'),
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