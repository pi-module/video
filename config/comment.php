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
    'news' => [
        'title'    => _a('Video comments'),
        'icon'     => 'icon-post',
        'callback' => 'Module\Video\Api\Comment',
        'locator'  => 'Module\Video\Api\Comment',
    ],
];