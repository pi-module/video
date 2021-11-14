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

class Aparat extends AbstractAdapter
{
    private string $streamType = 'embed';

    public function getType(): string
    {
        return $this->streamType;
    }

    public function getUrl($params): string
    {
        // Set url
        return sprintf('https://www.aparat.com/embed/%s?data[rnddiv]=%s&data[responsive]=yes',
            $params['streamName'],
            $params['streamPath']
        );
    }

    public function getPlayer($params): string
    {
        // Set template
        $template
            = <<<'EOT'
<div id="%s">
    <script type="text/JavaScript" src="%s"></script>
</div>
EOT;

        // Set player
        return sprintf($template, $params['streamName'], $params['videoUrl']);
    }
}
