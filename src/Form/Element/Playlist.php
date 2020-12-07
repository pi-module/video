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
use Laminas\Form\Element\Select;

class Playlist extends Select
{
    /**
     * @return array
     */
    public function getValueOptions()
    {
        if (empty($this->valueOptions)) {
            $playlists = Pi::registry('playlist', 'video')->read();

            $list = ['' => ''];
            foreach ($playlists as $playlist) {
                $list[$playlist['id']] = $playlist['title'];
            }

            $this->valueOptions = $list;
        }
        return $this->valueOptions;
    }
}