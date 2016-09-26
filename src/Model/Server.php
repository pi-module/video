<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @linkhttp://code.pialog.org for the Pi Engine source repository
 * @copyright Copyright (c) Pi Engine http://pialog.org
 * @licensehttp://pialog.org/license.txt BSD 3-Clause License
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
    protected $columns = array(
        'id',
        'title',
        'status',
        'default',
        'type',
        'url',
        'setting',
    );
}