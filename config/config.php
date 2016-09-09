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
return array(
    'category' => array(
        array(
            'title' => _a('Admin'),
            'name' => 'admin'
        ),
        array(
            'title' => _a('Json output'),
            'name' => 'json'
        ),
        array(
            'title' => _a('Broadcasting System'),
            'name' => 'broadcast'
        ),
        array(
            'title' => _a('Manage link'),
            'name' => 'link'
        ),
        array(
            'title' => _a('View'),
            'name' => 'view'
        ),
        array(
            'title' => _a('Media'),
            'name' => 'media'
        ),
        array(
            'title' => _a('Image'),
            'name' => 'image'
        ),
        array(
            'title' => _a('Social'),
            'name' => 'social'
        ),
        array(
            'title'  => _a('Vote'),
            'name'   => 'vote'
        ),
        array(
            'title'  => _a('Favourite'),
            'name'   => 'favourite'
        ),
        array(
            'title' => _a('Texts'),
            'name' => 'text'
        ),
    ),
    'item' => array(
    	// Admin
        'admin_perpage' => array(
            'category' => 'admin',
            'title' => _a('Perpage'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 50
        ),
        // Json
        'json_perpage' => array(
            'category' => 'json',
            'title' => _a('Perpage on json output'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 100
        ),
        'json_check_password' => array(
            'category' => 'json',
            'title' => _a('Check password for json output'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 0
        ),
        'json_password' => array(
            'category' => 'json',
            'title' => _a('Password for json output'),
            'description' => _a('After use on mobile device , do not change it'),
            'edit' => 'text',
            'filter' => 'string',
            'value' => md5(rand(1,99999)),
        ),
        // Broadcast
        'broadcast_system' => array(
            'title' => _a('Broadcasting System'),
            'description' => '',
            'edit' => array(
                'type' => 'select',
                'options' => array(
                    'options' => array(
                        'file' => _a('Broadcast from file'),
                        'wowza' => _a('Wowza Streaming Engine'),
                    ),
                ),
            ),
            'filter' => 'text',
            'value' => 'file',
            'category' => 'broadcast',
        ),
        'broadcast_url' => array(
            'category' => 'broadcast',
            'title' => _a('Wowza url and application name'),
            'description' => _a('Wowza server ip or url and port, without prefix ( http:// , rtmp:// , rtsp:// ), for example : 127.0.0.1:1935/vod'),
            'edit' => 'text',
            'filter' => 'string',
            'value' => ''
        ),
        'broadcast_source' => array(
            'category' => 'broadcast',
            'title' => _a('Wowza source'),
            'description' => _a('Name of media source, than create on wowza vod or media cache service, for example : mp4:http'),
            'edit' => 'text',
            'filter' => 'string',
            'value' => ''
        ),
        // Link
        'link_url' => array(
            'category' => 'link',
            'title' => _a('Default video url'),
            'description' => sprintf(_a('Full url by http:// or https:// without add / on end, for example : %s'), Pi::url()),
            'edit' => 'text',
            'filter' => 'string',
            'value' => ''
        ),
        'link_path' => array(
            'category' => 'link',
            'title' => _a('Default video path'),
            'description' => _a('Without add / on start and end, for example : upload/video/2016/09'),
            'edit' => 'text',
            'filter' => 'string',
            'value' => ''
        ),
        // View
        'view_perpage' => array(
            'category' => 'view',
            'title' => _a('Perpage'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 20
        ),
        'view_column' => array(
            'title' => _a('Columns'),
            'description' => '',
            'edit' => array(
                'type' => 'select',
                'options' => array(
                    'options' => array(
                        1 => _a('One columns'),
                        2 => _a('Two columns'),
                        3 => _a('Three columns'),
                        4 => _a('Four columns'),
                        6 => _a('Six columns'),
                    ),
                ),
            ),
            'filter' => 'text',
            'value' => 3,
            'category' => 'view',
        ),
        'view_breadcrumbs' => array(
            'category' => 'view',
            'title' => _a('Show breadcrumbs'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'view_tag' => array(
            'category' => 'view',
            'title' => _a('Show Tags'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'view_related' => array(
            'category' => 'view',
            'title' => _a('Show related video'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'view_related_number' => array(
            'category' => 'view',
            'title' => _a('Related video number'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 5
        ),
        // Media
        'media_size' => array(
            'category' => 'media',
            'title' => _a('Video Size'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 50000000
        ),
        'media_extension' => array(
            'category' => 'media',
            'title' => _a('Video Extension'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'string',
            'value' => 'mp4,mp3'
        ),
        // Image
        'image_size' => array(
            'category' => 'image',
            'title' => _a('Image Size'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 1000000
        ),
        'image_quality' => array(
            'category' => 'image',
            'title' => _a('Image quality'),
            'description' => _a('Between 0 to 100 and support both of JPG and PNG, default is 75'),
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 75
        ),
        'image_path' => array(
            'category' => 'image',
            'title' => _a('Image path'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'string',
            'value' => 'video/image'
        ),
        'image_extension' => array(
            'category' => 'image',
            'title' => _a('Image Extension'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'string',
            'value' => 'jpg,jpeg,png,gif'
        ),
        'image_largeh' => array(
            'category' => 'image',
            'title' => _a('Large Image height'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 1200
        ),
        'image_largew' => array(
            'category' => 'image',
            'title' => _a('Large Image width'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 1200
        ),
        'image_mediumh' => array(
            'category' => 'image',
            'title' => _a('Medium Image height'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 500
        ),
        'image_mediumw' => array(
            'category' => 'image',
            'title' => _a('Medium Image width'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 500
        ),
        'image_thumbh' => array(
            'category' => 'image',
            'title' => _a('Thumb Image height'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 250
        ),
        'image_thumbw' => array(
            'category' => 'image',
            'title' => _a('Thumb Image width'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 250
        ),
        'image_lightbox' => array(
            'category' => 'image',
            'title' => _a('Use lightbox'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'image_watermark' => array(
            'category' => 'image',
            'title' => _a('Add Watermark'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 0
        ),
        'image_watermark_source' => array(
            'category' => 'image',
            'title' => _a('Watermark Image'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'string',
            'value' => ''
        ),
        'image_watermark_position' => array(
            'title' => _a('Watermark Positio'),
            'description' => '',
            'edit' => array(
                'type' => 'select',
                'options' => array(
                    'options' => array(
                        'top-left' => _a('Top Left'),
                        'top-right' => _a('Top Right'),
                        'bottom-left' => _a('Bottom Left'),
                        'bottom-right' => _a('Bottom Right'),
                    ),
                ),
            ),
            'filter' => 'text',
            'value' => 'bottom-right',
            'category' => 'image',
        ),
        // Social
        'social_sharing' => array(
            'title' => _t('Social sharing items'),
            'description' => '',
            'edit' => array(
                'type' => 'multi_checkbox',
                'options' => array(
                    'options' => Pi::service('social_sharing')->getList(),
                ),
            ),
            'filter' => 'array',
            'category' => 'social',
        ),
        // Vote
        'vote_bar' => array(
            'category' => 'vote',
            'title' => _a('Use vote system'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        // favourite
        'favourite_bar' => array(
            'category' => 'favourite',
            'title' => _a('Use favourite system'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        // Texts
        'text_description_index' => array(
            'category' => 'head_meta',
            'title' => _a('Description for index page'),
            'description' => '',
            'edit' => 'textarea',
            'filter' => 'string',
            'value' => ''
        ),
        'force_replace_space' => array(
            'category' => 'head_meta',
            'title' => _a('Force replace space by comma(,)'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
    ),
);