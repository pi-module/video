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

class Mistserver extends AbstractAdapter
{
    private $streamType = 'hls';

    public function getUrl($params)
    {
        // Set stream type
        $streamType = isset($params['streamType']) ? $params['streamType'] : $this->streamType;

        // Set url
        $url = $params['serverUrl'];
        if (isset($params['serverPath']) && !empty($params['serverPath'])) {
            $url = $url . '/' . $params['serverPath'];
        }
        if (isset($params['videoPath']) && !empty($params['videoPath'])) {
            $url = $url . '/' . $params['videoPath'];
        }

        // Set video name end of url
        switch ($streamType) {
            default:
            case 'hls':
                $url = $url . '/' . $params['videoName'] . '/index.m3u8';
                break;
        }

        return $url;
    }
}