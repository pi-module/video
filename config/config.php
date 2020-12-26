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
return [
    'category' => [
        [
            'title' => _a('Admin'),
            'name'  => 'admin',
        ],
        [
            'title' => _a('View'),
            'name'  => 'view',
        ],
        [
            'title' => _a('Submit'),
            'name'  => 'submit',
        ],
        [
            'title' => _a('Media'),
            'name'  => 'media',
        ],
        [
            'title' => _a('Image'),
            'name'  => 'image',
        ],
        [
            'title' => _a('Order video'),
            'name'  => 'order',
        ],
        [
            'title' => _a('Social'),
            'name'  => 'social',
        ],
        [
            'title' => _a('Vote'),
            'name'  => 'vote',
        ],
        [
            'title' => _a('Favourite'),
            'name'  => 'favourite',
        ],
        [
            'title' => _a('Texts'),
            'name'  => 'text',
        ],
    ],
    'item'     => [
        // Admin
        'admin_perpage'             => [
            'category'    => 'admin',
            'title'       => _a('Perpage'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 50,
        ],
        'brand_system'              => [
            'category'    => 'admin',
            'title'       => _a('Active brand system'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 0,
        ],
        // View
        'homepage_type'             => [
            'title'       => _a('Homepage type'),
            'description' => '',
            'edit'        => [
                'type'    => 'select',
                'options' => [
                    'options' => [
                        'list'     => _a('List of all videos'),
                        'category' => _a('List of categories'),
                    ],
                ],
            ],
            'filter'      => 'text',
            'value'       => 'list',
            'category'    => 'view',
        ],
        'homepage_title'            => [
            'category'    => 'view',
            'title'       => _a('Homepage title'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'string',
        ],
        'view_perpage'              => [
            'category'    => 'view',
            'title'       => _a('Perpage'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 20,
        ],
        'view_column'               => [
            'title'       => _a('Columns'),
            'description' => '',
            'edit'        => [
                'type'    => 'select',
                'options' => [
                    'options' => [
                        1 => _a('One columns'),
                        2 => _a('Two columns'),
                        3 => _a('Three columns'),
                        4 => _a('Four columns'),
                        6 => _a('Six columns'),
                    ],
                ],
            ],
            'filter'      => 'text',
            'value'       => 3,
            'category'    => 'view',
        ],
        'view_breadcrumbs'          => [
            'category'    => 'view',
            'title'       => _a('Show breadcrumbs'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'view_tag'                  => [
            'category'    => 'view',
            'title'       => _a('Show Tags'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'view_related'              => [
            'category'    => 'view',
            'title'       => _a('Show related video'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'view_related_number'       => [
            'category'    => 'view',
            'title'       => _a('Related video number'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 5,
        ],
        'view_attribute'            => [
            'category'    => 'view',
            'title'       => _a('Show attribute fields'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'view_description_category' => [
            'category'    => 'view',
            'title'       => _a('Show category description on category page'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'view_description_video'    => [
            'category'    => 'view',
            'title'       => _a('Show category description on video page'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 0,
        ],
        // Submit
        'user_submit'               => [
            'category'    => 'submit',
            'title'       => _a('Users can submit videos'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        // Media
        'media_size'                => [
            'category'    => 'media',
            'title'       => _a('Video Size'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 50000000,
        ],
        'media_extension'           => [
            'category'    => 'media',
            'title'       => _a('Video Extension'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'string',
            'value'       => 'mp4,mp3',
        ],
        // Image
        'image_minw'                => [
            'category'    => 'image',
            'title'       => _t('Min Image width (upload)'),
            'description' => _t('This config can override media module value'),
            'edit'        => 'text',
            'filter'      => 'string',
            'value'       => '',
        ],
        'image_minh'                => [
            'category'    => 'image',
            'title'       => _t('Min Image height (upload)'),
            'description' => _t('This config can override media module value'),
            'edit'        => 'text',
            'filter'      => 'string',
            'value'       => '',
        ],
        'image_largeh'              => [
            'category'    => 'image',
            'title'       => _a('Large Image height'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 1200,
        ],
        'image_largew'              => [
            'category'    => 'image',
            'title'       => _a('Large Image width'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 1200,
        ],
        'image_itemh'               => [
            'category'    => 'image',
            'title'       => _a('Item Image height'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 800,
        ],
        'image_itemw'               => [
            'category'    => 'image',
            'title'       => _a('Item Image width'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 800,
        ],
        'image_mediumh'             => [
            'category'    => 'image',
            'title'       => _a('Medium Image height'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 500,
        ],
        'image_mediumw'             => [
            'category'    => 'image',
            'title'       => _a('Medium Image width'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 500,
        ],
        'image_thumbh'              => [
            'category'    => 'image',
            'title'       => _a('Thumb Image height'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 250,
        ],
        'image_thumbw'              => [
            'category'    => 'image',
            'title'       => _a('Thumb Image width'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 250,
        ],

        'image_quality'            => [
            'category'    => 'image',
            'title'       => _a('Image quality'),
            'description' => _a('Between 0 to 100 and support both of JPG and PNG, default is 75. This config can override media module value'),
            'edit'        => 'text',
            'filter'      => 'string',
            'value'       => 75,
        ],
        'image_size'               => [
            'category'    => 'image',
            'title'       => _a('Image Size'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 1000000,
        ],
        'image_path'               => [
            'category'    => 'image',
            'title'       => _a('Image path'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'string',
            'value'       => 'video/image',
        ],
        'image_extension'          => [
            'category'    => 'image',
            'title'       => _a('Image Extension'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'string',
            'value'       => 'jpg,jpeg,png,gif',
        ],
        'image_lightbox'           => [
            'category'    => 'image',
            'title'       => _a('Use lightbox'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'image_watermark'          => [
            'category'    => 'image',
            'title'       => _a('Add Watermark'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 0,
        ],
        'image_watermark_source'   => [
            'category'    => 'image',
            'title'       => _a('Watermark Image'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'string',
            'value'       => '',
        ],
        'image_watermark_position' => [
            'title'       => _a('Watermark Positio'),
            'description' => '',
            'edit'        => [
                'type'    => 'select',
                'options' => [
                    'options' => [
                        'top-left'     => _a('Top Left'),
                        'top-right'    => _a('Top Right'),
                        'bottom-left'  => _a('Bottom Left'),
                        'bottom-right' => _a('Bottom Right'),
                    ],
                ],
            ],
            'filter'      => 'text',
            'value'       => 'bottom-right',
            'category'    => 'image',
        ],
        // Order
        'sale_video'               => [
            'title'       => _a('Sale video'),
            'description' => '',
            'edit'        => [
                'type'    => 'select',
                'options' => [
                    'options' => [
                        'free'    => _a('All videos is free'),
                        'single'  => _a('User pay one by one for paid video'),
                        'package' => _a('User buy package to watch all paid videos on package time'),
                    ],
                ],
            ],
            'filter'      => 'text',
            'value'       => 'free',
            'category'    => 'order',
        ],
        // Social
        'social_sharing'           => [
            'title'       => _t('Social sharing items'),
            'description' => '',
            'edit'        => [
                'type'    => 'multi_checkbox',
                'options' => [
                    'options' => Pi::service('social_sharing')->getList(),
                ],
            ],
            'filter'      => 'array',
            'category'    => 'social',
        ],
        // Vote
        'vote_bar'                 => [
            'category'    => 'vote',
            'title'       => _a('Use vote system'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        // favourite
        'favourite_bar'            => [
            'category'    => 'favourite',
            'title'       => _a('Use favourite system'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        // Texts
        'text_description_index'   => [
            'category'    => 'head_meta',
            'title'       => _a('Description for index page'),
            'description' => '',
            'edit'        => 'textarea',
            'filter'      => 'string',
            'value'       => '',
        ],
        'force_replace_space'      => [
            'category'    => 'head_meta',
            'title'       => _a('Force replace space by comma(,)'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
    ],
];