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
    private string $streamType = 'embed';

    public function getType(): string
    {
        return $this->streamType;
    }

    public function getUrl($params): string
    {
        // Set url
        return sprintf('https://player.vimeo.com/video/%s', $params['streamName']);
    }

    public function player($params): string
    {
        // Set template
        $template
            = <<<'EOT'
<iframe src="%s" width="560" height="315" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
EOT;

        // Set player
        return sprintf($template, $params['videoUrl']);
    }
}
