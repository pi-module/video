<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\Video\Server;

class Wowza extends AbstractAdapter
{
    public function updateMethod()
    {
        return [
            'put' => [
                'name'  => 'put',
                'title' => __('Put information'),
                'icon'  => 'fas fa-file',
            ],
            'url' => [
                'name'  => 'url',
                'title' => __('Put full url'),
                'icon'  => 'fas fa-file',
            ],
        ];
    }

    public function getUrl($params)
    {
        return 'Wowza';
    }
}