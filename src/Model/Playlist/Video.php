<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <hossein@azizabadi.com>
 */

namespace Module\Video\Model\Playlist;

use Pi\Application\Model\Model;

class Video extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $columns
        = [
            'id',
            'playlist_id',
            'video_id',
            'video_order',
            'time_create',
        ];
}