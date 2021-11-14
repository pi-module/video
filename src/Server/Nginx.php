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

class Nginx extends AbstractAdapter
{
    private string $streamType = 'hls';

    public function getType(): string
    {
        return $this->streamType;
    }

    public function getUrl($params): string
    {
        // Set stream type
        $streamType = isset($params['streamType']) ? $params['streamType'] : $this->streamType;

        // Set url
        $url = $params['serverUrl'];
        if (isset($params['serverApplication']) && !empty($params['serverApplication'])) {
            $url = $url . '/' . $params['serverApplication'];
        }
        if (isset($params['streamPath']) && !empty($params['streamPath'])) {
            $url = $url . '/' . $params['streamPath'];
        }

        // Set video name end of url
        switch ($streamType) {
            default:
            case 'hls':
                $url = $url . '/' . $params['streamName'] . '/master.m3u8';
                break;

            case 'dash':
                $url = $url . '/' . $params['streamName'] . '/manifest.mpd';
                break;
        }

        return $url;
    }
}
