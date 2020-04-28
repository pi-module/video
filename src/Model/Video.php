<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @linkhttp   ://code.piengine.org for the Pi Engine source repository
 * @copyright  Copyright (c) Pi Engine http://piengine.org
 * @licensehttp://piengine.org/license.txt BSD 3-Clause License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\Video\Model;

use Pi\Application\Model\Model;

class Video extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $columns
        = [
            'id',
            'title',
            'slug',
            'category',
            'category_main',
            'brand',
            'text_summary',
            'text_description',
            'seo_title',
            'seo_keywords',
            'seo_description',
            'status',
            'time_create',
            'time_update',
            'uid',
            'hits',
            'download',
            'image',
            'path',
            'main_image',
            'additional_images',
            'point',
            'count',
            'attribute',
            'related',
            'recommended',
            'favourite',
            'sale_type',
            'sale_price',
            'video_server',
            'video_path',
            'video_file',
            'video_url',
            'video_size',
            'video_duration',
            'setting',
        ];
}