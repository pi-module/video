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
    // Module meta
    'meta'         => array(
        'title'         => _a('Video'),
        'description'   => _a('Video play system.'),
        'version'       => '0.8.0',
        'license'       => 'New BSD',
        'logo'          => 'image/logo.png',
        'readme'        => 'docs/readme.txt',
        'demo'          => '',
        'icon'          => 'fa-video-camera',
    ),
    // Author information
    'author'        => array(
        'Name'          => 'Somayeh Karami',
        'email'         => 'somayeh.karami@gmail.com',
        'website'       => '',
        'credits'       => ''
    ),
    // Resource
    'resource' => array(
        'database'      => 'database.php',
        'config'        => 'config.php',
        'permission'    => 'permission.php',
        'page'          => 'page.php',
        'navigation'    => 'navigation.php',
        'block'         => 'block.php',
        'route'         => 'route.php',
        'comment'       => 'comment.php',
    ),
);
