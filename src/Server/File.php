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

class File extends AbstractAdapter
{
    public function updateMethod()
    {
        return [
            'upload' => [
                'name'  => 'upload',
                'title' => __('Upload file'),
                'icon'  => 'fas fa-file',
            ],
            'put'    => [
                'name'  => 'put',
                'title' => __('Put information'),
                'icon'  => 'fas fa-file',
            ],
            'url'    => [
                'name'  => 'url',
                'title' => __('Put full url'),
                'icon'  => 'fas fa-file',
            ],
        ];
    }

    public function getUrl($params)
    {
        $url = '';
        $url .= $params['serverUrl'];
        if (isset($params['serverPath']) && !empty($params['serverPath'])) {
            $url .= $url . '/' . $params['serverPath'];
        }
        if (isset($params['videoPath']) && !empty($params['videoPath'])) {
            $url .= $url . '/' . $params['videoPath'];
        }
        $url .= $url . '/' . $params['videoName'];

        return $url;
    }
}