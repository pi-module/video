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

class Telewebion extends AbstractAdapter
{
    private string $streamType = 'embed';

    public function getType(): string
    {
        return $this->streamType;
    }

    public function getUrl($params): string
    {
        // Set url
        return sprintf('https://m.telewebion.com/embed/vod?EpisodeID=%s',
            $params['streamName']
        );
    }

    public function getPlayer($params): string
    {
        // Set template
        $template
            = <<<'EOT'
<div id="%s">
    <iframe src="%s" width="300" height="200" frameborder="0" allowFullScreen="true" webkitallowfullscreen="true" mozallowfullscreen="true"></iframe>
</div>
EOT;

        // Set player
        return sprintf($template, $params['streamName'], $params['videoUrl']);
    }
}
