<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\Video\Form\Element;

use Pi;
use Zend\Form\Element\Radio;

class Server extends Radio
{
    /**
     * @return array
     */
    public function getValueOptions()
    {
        if (empty($this->valueOptions)) {
            $serverList = Pi::registry('serverList', 'video')->read();

            $list = [];
            foreach ($serverList as $server) {
                $list[$server['id']] = sprintf('%s ( %s )', $server['title'], $server['type']);
            }

            $this->valueOptions = $list;
        }
        return $this->valueOptions;
    }
}