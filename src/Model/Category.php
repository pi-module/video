<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt BSD 3-Clause License
 */

/**
 * @author Somayeh Karami <somayeh.karami@gmail.com>
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
namespace Module\Video\Model;

use Pi\Application\Model\Model;

class Category extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $columns = array(
        'id',
        'parent',
        'title',
        'slug',
        'image',
        'path',
        'text_description',
        'seo_title',
        'seo_keywords',
        'seo_description',
        'time_create',
        'time_update',
        'setting',
        'status',
        'display_order'
    );
}