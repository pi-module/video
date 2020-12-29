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
    private $streamType = 'embed';

    public function getType()
    {
        return $this->streamType;
    }

    public function getUrl($params)
    {
        // Set url
        $url = sprintf('https://www.aparat.com/embed/qaeTw?data[rnddiv]=%s&data[responsive]=yes', $params['streamName']);

        return $url;
    }

    public function getPlayer($params)
    {
        // Set template
        $template
            = <<<'EOT'
<div id="%s">
    <script type="text/JavaScript" src="%s"></script>
</div>
EOT;

        // Set player
        $player = sprintf($template, $params['streamName'], $params['videoUrl']);

        return $player;
    }
}
