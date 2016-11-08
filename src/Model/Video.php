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

class Video extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $columns = array(
        'id',
        'title',
        'slug',
        'category',
        'category_main',
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
        'point',
        'count',
        'attribute',
        'related',
        'recommended',
        'favourite',
        'video_type',
        'video_extension',
        'video_link',
        'video_file',
        'video_path',
        'video_url',
        'video_size',
        'video_duration',
        'video_qmery_hash',
        'video_qmery_id',
        'video_qmery_hls',
        'setting',
        'video_server',
    );
}