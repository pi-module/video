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

class Inventory extends Model
{
    /**
     * {@inheritDoc}
     */
    protected array $columns
        = [
            'id',
            'title',
            'text_description',
            'seo_title',
            'seo_keywords',
            'seo_description',
            'status',
            'can_edit',
            'time_create',
            'time_update',
            'uid',
            'hits',
            'company_id',
            'main_image',
            'sale_price',
            'back_url',
        ];
}
