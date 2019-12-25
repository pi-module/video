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

class Vimeo extends AbstractAdapter
{
    private $streamType = 'embed';

    public function getType()
    {
        return $this->streamType;
    }

    public function getUrl($params)
    {
        // Set url
        $url = sprintf('https://player.vimeo.com/video/%s', $params['streamName']);

        return $url;
    }

    public function player($params)
    {
        // Set template
        $template
            = <<<'EOT'
<iframe src="%s" width="560" height="315" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
EOT;

        // Set player
        $player = sprintf($template, $params['videoUrl']);

        return $player;
    }
}