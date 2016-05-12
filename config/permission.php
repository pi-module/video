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
            'title' => _a('Video'),
            'access' => array(),
        ),
        'category' => array(
            'title' => _a('Category'),
            'access' => array(),
        ),
        'attribute' => array(
            'title' => _a('Attribute'),
            'access' => array(),
        ),
        'log' => array(
            'title' => _a('Logs'),
            'access' => array(),
        ),
        'tools' => array(
            'title' => _a('Tools'),
            'access' => array(),
        ),
        'json' => array(
            'title' => _a('Json'),
            'access' => array(),
        ),
    ),
    // Front section
    'front' => array(
        'public' => array(
            'title' => _a('Global public resource'),
            'access' => array(
                'guest',
                'member',
            ),
        ),

        'submit' => array(
            'title' => _a('Submit'),
            'access' => array(
                'member',
            ),
        ),

        'channel' => array(
            'title' => _a('Channel'),
            'access' => array(
                'guest',
                'member',
            ),
        ),
    ),
);