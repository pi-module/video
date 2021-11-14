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

namespace Module\Video\Model;

use Pi\Application\Model\Model;

class Category extends Model
{
    /**
     * {@inheritDoc}
     */
    protected array $columns
        = [
            'id',
            'parent',
            'title',
            'slug',
            'image',
            'image_wide',
            'path',
            'text_summary',
            'text_description',
            'seo_title',
            'seo_keywords',
            'seo_description',
            'time_create',
            'time_update',
            'setting',
            'status',
            'display_order',
            'display_type',
            'type',
            'hits',
        ];
}
