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
    private $streamType = 'mp4';

    public function getUrl($params)
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
            case 'mp4':
                $url = $url . '/' . $params['streamName'];
                break;
        }


        return $url;
    }
}