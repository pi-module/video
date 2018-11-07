<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @linkhttp   ://code.piengine.org for the Pi Engine source repository
 * @copyright  Copyright (c) Pi Engine http://piengine.org
 * @licensehttp://piengine.org/license.txt BSD 3-Clause License
 */

/**
 * @author Somayeh Karami <somayeh.karami@gmail.com>
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\Video\Model;

use Pi\Application\Model\Model;

class Server extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $columns
        = [
            'id',
            'title',
            'status',
            'default',
            'type',
            'url',
            'setting',
        ];
}