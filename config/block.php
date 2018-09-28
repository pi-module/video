<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

/**
 * @author Somayeh Karami <somayeh.karami@gmail.com>
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
return [
    'video-new'    => [
        'name'        => 'video-new',
        'title'       => _a('New video'),
        'description' => _a('New video list'),
        'render'      => ['block', 'videoNew'],
        'template'    => 'video-new',
        'config'      => [
            'category'    => [
                'title'       => _a('Category'),
                'description' => '',
                'edit'        => 'Module\Video\Form\Element\Category',
                'filter'      => 'string',
                'value'       => 0,
            ],
            'number'      => [
                'title'       => _a('Number'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => 10,
            ],
            'recommended' => [
                'title'       => _a('Show just recommended'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'list-type'   => [
                'title'       => _a('Video list type'),
                'description' => '',
                'edit'        => [
                    'type'    => 'select',
                    'options' => [
                        'options' => [
                            'horizontal' => _a('Horizontal'),
                            'vertical'   => _a('Vertical'),
                            'box'        => _a('Multi size Box'),
                            'list'       => _a('List'),
                            'slide'      => _a('Slide'),
                            'slidehover' => _a('Slide by hover effect'),
                            'spotlight'  => _a('Spotlight'),
                        ],
                    ],
                ],
                'filter'      => 'text',
                'value'       => 'horizontal',
            ],
            'column'      => [
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
            ],
            'blockEffect' => [
                'title'       => _a('Use block effects'),
                'description' => _a('Use block effects or set custom effect on theme'),
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'more-show'   => [
                'title'       => _a('Show More link to module page'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'more-link'   => [
                'title'       => _a('Set More link to module page'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'string',
                'value'       => '',
            ],
        ],
    ],
    'video-random' => [
        'name'        => 'video-random',
        'title'       => _a('Random video'),
        'description' => _a('Random video list'),
        'render'      => ['block', 'videoRandom'],
        'template'    => 'video-random',
        'config'      => [
            'category'    => [
                'title'       => _a('Category'),
                'description' => '',
                'edit'        => 'Module\Video\Form\Element\Category',
                'filter'      => 'string',
                'value'       => 0,
            ],
            'number'      => [
                'title'       => _a('Number'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => 10,
            ],
            'recommended' => [
                'title'       => _a('Show just recommended'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'list-type'   => [
                'title'       => _a('Video list type'),
                'description' => '',
                'edit'        => [
                    'type'    => 'select',
                    'options' => [
                        'options' => [
                            'horizontal' => _a('Horizontal'),
                            'vertical'   => _a('Vertical'),
                            'box'        => _a('Multi size Box'),
                            'list'       => _a('List'),
                            'slide'      => _a('Slide'),
                            'slidehover' => _a('Slide by hover effect'),
                            'spotlight'  => _a('Spotlight'),
                        ],
                    ],
                ],
                'filter'      => 'text',
                'value'       => 'horizontal',
            ],
            'column'      => [
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
            ],
            'blockEffect' => [
                'title'       => _a('Use block effects'),
                'description' => _a('Use block effects or set custom effect on theme'),
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'more-show'   => [
                'title'       => _a('Show More link to module page'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'more-link'   => [
                'title'       => _a('Set More link to module page'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'string',
                'value'       => '',
            ],
        ],
    ],
    'video-tag'    => [
        'name'        => 'video-tag',
        'title'       => _a('Tag video'),
        'description' => _a('Videos from selected tag'),
        'render'      => ['block', 'videoTag'],
        'template'    => 'video-tag',
        'config'      => [
            'tag-term'    => [
                'title'       => _a('Tag term'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'string',
                'value'       => '',
            ],
            'number'      => [
                'title'       => _a('Number'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => 10,
            ],
            'recommended' => [
                'title'       => _a('Show just recommended'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'list-type'   => [
                'title'       => _a('Video list type'),
                'description' => '',
                'edit'        => [
                    'type'    => 'select',
                    'options' => [
                        'options' => [
                            'horizontal' => _a('Horizontal'),
                            'vertical'   => _a('Vertical'),
                            'box'        => _a('Multi size Box'),
                            'list'       => _a('List'),
                            'slide'      => _a('Slide'),
                            'slidehover' => _a('Slide by hover effect'),
                            'spotlight'  => _a('Spotlight'),
                        ],
                    ],
                ],
                'filter'      => 'text',
                'value'       => 'horizontal',
            ],
            'column'      => [
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
            ],
            'blockEffect' => [
                'title'       => _a('Use block effects'),
                'description' => _a('Use block effects or set custom effect on theme'),
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'more-show'   => [
                'title'       => _a('Show More link to module page'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'more-link'   => [
                'title'       => _a('Set More link to module page'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'string',
                'value'       => '',
            ],
        ],
    ],
    'video-hits'   => [
        'name'        => 'video-hits',
        'title'       => _a('Top hits video'),
        'description' => _a('Top hits video list'),
        'render'      => ['block', 'videoHits'],
        'template'    => 'video-hits',
        'config'      => [
            'category'    => [
                'title'       => _a('Category'),
                'description' => '',
                'edit'        => 'Module\Video\Form\Element\Category',
                'filter'      => 'string',
                'value'       => 0,
            ],
            'day'         => [
                'title'       => _a('Top hits on X last days'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => 30,
            ],
            'number'      => [
                'title'       => _a('Number'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => 10,
            ],
            'recommended' => [
                'title'       => _a('Show just recommended'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'list-type'   => [
                'title'       => _a('Video list type'),
                'description' => '',
                'edit'        => [
                    'type'    => 'select',
                    'options' => [
                        'options' => [
                            'horizontal' => _a('Horizontal'),
                            'vertical'   => _a('Vertical'),
                            'box'        => _a('Multi size Box'),
                            'list'       => _a('List'),
                            'slide'      => _a('Slide'),
                            'slidehover' => _a('Slide by hover effect'),
                            'spotlight'  => _a('Spotlight'),
                        ],
                    ],
                ],
                'filter'      => 'text',
                'value'       => 'horizontal',
            ],
            'column'      => [
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
            ],
            'blockEffect' => [
                'title'       => _a('Use block effects'),
                'description' => _a('Use block effects or set custom effect on theme'),
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'more-show'   => [
                'title'       => _a('Show More link to module page'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'more-link'   => [
                'title'       => _a('Set More link to module page'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'string',
                'value'       => '',
            ],
        ],
    ],
    'video-select' => [
        'name'        => 'video-select',
        'title'       => _a('Selected video'),
        'description' => _a('Selected video player'),
        'render'      => ['block', 'videoSelect'],
        'template'    => 'video-select',
        'config'      => [
            'vid'    => [
                'title'       => _a('Video ID'),
                'description' => _a('If not set , show last video'),
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => '',
            ],
            'size'   => [
                'title'       => _a('Player size'),
                'description' => '',
                'edit'        => [
                    'type'    => 'select',
                    'options' => [
                        'options' => [
                            'responsive' => _a('Responsive'),
                            'custom'     => _a('Custom'),
                        ],
                    ],
                ],
                'filter'      => 'text',
                'value'       => 'custom',
            ],
            'width'  => [
                'title'       => _a('Player width'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => 640,
            ],
            'height' => [
                'title'       => _a('Player height'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => 360,
            ],
        ],
    ],
];