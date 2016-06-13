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
        array(
            'title'         => _a('Video'),
            'controller'    => 'video',
            'permission'    => 'video',
        ),
        array(
            'title'         => _a('Category'),
            'controller'    => 'category',
            'permission'    => 'category',
        ),
        array(
            'label' => _a('Attribute'),
            'controller' => 'attribute',
            'permission' => 'attribute',
        ),
        array(
            'label' => _a('Logs'),
            'controller' => 'log',
            'permission' => 'log',
        ),
        array(
            'label' => _a('Tools'),
            'controller' => 'tools',
            'permission' => 'tools',
        ),
        array(
            'title' => _a('Json output'),
            'controller' => 'json',
            'permission' => 'json',
        ),
    ),
    // Front section
    'front' => array(
        array(
            'title'         => _a('Index'),
            'controller'    => 'index',
            'permission'    => 'public',
            'block'         => 1,
        ),
        array(
            'title'         => _a('Category'),
            'controller'    => 'category',
            'permission'    => 'public',
            'block'         => 1,
        ),
        array(
            'title'         => _a('Category list'),
            'controller'    => 'category',
            'action'        => 'list',
            'permission'    => 'public',
            'block'         => 1,
        ),
        array(
            'title'         => _a('Tag'),
            'controller'    => 'tag',
            'permission'    => 'public',
            'block'         => 1,
        ),
        array(
            'title'         => _a('Tag list'),
            'controller'    => 'tag',
            'action'        => 'list',
            'permission'    => 'public',
            'block'         => 1,
        ),
        array(
            'title'         => _a('Watch video'),
            'controller'    => 'watch',
            'permission'    => 'public',
            'block'         => 1,
        ),
        array(
            'title'         => _a('Channel'),
            'controller'    => 'channel',
            'permission'    => 'channel',
            'block'         => 1,
        ),
        array(
            'title'         => _a('Submit'),
            'controller'    => 'submit',
            'permission'    => 'public',
            'block'         => 1,
        ),
        array(
            'label' => _a('Json output'),
            'controller' => 'json',
            'permission' => 'public',
            'block' => 0,
        ),
    ),
);