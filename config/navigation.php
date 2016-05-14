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
return array(
    // Admin section
    'admin' => array(
        'video' => array(
            'label' => _a('Video'),
            'permission' => array(
                'resource' => 'video',
            ),
            'route' => 'admin',
            'module' => 'video',
            'controller' => 'video',
            'action' => 'index',
        ),

        'category' => array(
            'label' => _a('Category'),
            'permission' => array(
                'resource' => 'category',
            ),
            'route' => 'admin',
            'module' => 'video',
            'controller' => 'category',
            'action' => 'index',
        ),

        'attribute' => array(
            'label' => _a('Attribute'),
            'permission' => array(
                'resource' => 'attribute',
            ),
            'route' => 'admin',
            'module' => 'video',
            'controller' => 'attribute',
            'action' => 'index',
        ),

        'position' => array(
            'label' => _a('Attribute position'),
            'permission' => array(
                'resource' => 'position',
            ),
            'route' => 'admin',
            'module' => 'video',
            'controller' => 'position',
            'action' => 'index',
        ),

        'log' => array(
            'label' => _a('Logs'),
            'permission' => array(
                'resource' => 'log',
            ),
            'route' => 'admin',
            'module' => 'video',
            'controller' => 'log',
            'action' => 'index',
        ),

        'tools' => array(
            'label' => _a('Tools'),
            'permission' => array(
                'resource' => 'tools',
            ),
            'route' => 'admin',
            'module' => 'video',
            'controller' => 'tools',
            'action' => 'index',
        ),

        'json' => array(
            'label' => _a('Json'),
            'permission' => array(
                'resource' => 'json',
            ),
            'route' => 'admin',
            'module' => 'video',
            'controller' => 'json',
            'action' => 'index',
        ),
    ),
    // Front section
    'front' => array(
        'category' => array(
            'label' => _a('Category list'),
            'permission' => array(
                'resource' => 'public',
            ),
            'route' => 'video',
            'module' => 'video',
            'controller' => 'category',
        ),

        'tag' => array(
            'label' => _a('Tag list'),
            'permission' => array(
                'resource' => 'public',
            ),
            'route' => 'video',
            'module' => 'video',
            'controller' => 'tag',
        ),

        'channel' => array(
            'label' => _a('Your channel'),
            'permission' => array(
                'resource' => 'public',
            ),
            'route' => 'video',
            'module' => 'video',
            'controller' => 'channel',
        ),

        'submit' => array(
            'label' => _a('Submit'),
            'permission' => array(
                'resource' => 'public',
            ),
            'route' => 'video',
            'module' => 'video',
            'controller' => 'submit',
        ),

    ),
);