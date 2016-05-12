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
    'video-new' => array(
        'name' => 'video-new',
        'title' => _a('New video'),
        'description' => _a('New video list'),
        'render' => array('block', 'videoNew'),
        'template' => 'video-new',
        'config' => array(
            'category' => array(
                'title' => _a('Category'),
                'description' => '',
                'edit' => 'Module\Video\Form\Element\Category',
                'filter' => 'string',
                'value' => 0,
            ),
            'number' => array(
                'title' => _a('Number'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 10,
            ),
            'list-type' => array(
                'title' => _a('Video list type'),
                'description' => '',
                'edit' => array(
                    'type' => 'select',
                    'options' => array(
                        'options' => array(
                            'horizontal' => _a('Horizontal'),
                            'vertical' => _a('Vertical'),
                            'box' => _a('Multi size Box'),
                            'list' => _a('List'),
                            'slide' => _a('Slide'),
                            'slidehover' => _a('Slide by hover effect'),
                        ),
                    ),
                ),
                'filter' => 'text',
                'value' => 'horizontal',
            ),
            'blockEffect' => array(
                'title' => _a('Use block effects'),
                'description' => _a('Use block effects or set custom effect on theme'),
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'more-show' => array(
                'title' => _a('Show More link to module page'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'more-link' => array(
                'title' => _a('Set More link to module page'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'string',
                'value' => '',
            ),
        ),
    ),
    'video-random' => array(
        'name' => 'video-random',
        'title' => _a('Random video'),
        'description' => _a('Random video list'),
        'render' => array('block', 'videoRandom'),
        'template' => 'video-random',
        'config' => array(
            'category' => array(
                'title' => _a('Category'),
                'description' => '',
                'edit' => 'Module\Video\Form\Element\Category',
                'filter' => 'string',
                'value' => 0,
            ),
            'number' => array(
                'title' => _a('Number'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 10,
            ),
            'list-type' => array(
                'title' => _a('Video list type'),
                'description' => '',
                'edit' => array(
                    'type' => 'select',
                    'options' => array(
                        'options' => array(
                            'horizontal' => _a('Horizontal'),
                            'vertical' => _a('Vertical'),
                            'box' => _a('Multi size Box'),
                            'list' => _a('List'),
                            'slide' => _a('Slide'),
                            'slidehover' => _a('Slide by hover effect'),
                        ),
                    ),
                ),
                'filter' => 'text',
                'value' => 'horizontal',
            ),
            'blockEffect' => array(
                'title' => _a('Use block effects'),
                'description' => _a('Use block effects or set custom effect on theme'),
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'more-show' => array(
                'title' => _a('Show More link to module page'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'more-link' => array(
                'title' => _a('Set More link to module page'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'string',
                'value' => '',
            ),
        ),
    ),
    'video-tag' => array(
        'name' => 'video-tag',
        'title' => _a('Tag video'),
        'description' => _a('Videos from selected tag'),
        'render' => array('block', 'videoTag'),
        'template' => 'video-tag',
        'config' => array(
            'tag-term' => array(
                'title' => _a('Tag term'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'string',
                'value' => '',
            ),
            'number' => array(
                'title' => _a('Number'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 10,
            ),
            'list-type' => array(
                'title' => _a('Video list type'),
                'description' => '',
                'edit' => array(
                    'type' => 'select',
                    'options' => array(
                        'options' => array(
                            'horizontal' => _a('Horizontal'),
                            'vertical' => _a('Vertical'),
                            'box' => _a('Multi size Box'),
                            'list' => _a('List'),
                            'slide' => _a('Slide'),
                            'slidehover' => _a('Slide by hover effect'),
                        ),
                    ),
                ),
                'filter' => 'text',
                'value' => 'horizontal',
            ),
            'blockEffect' => array(
                'title' => _a('Use block effects'),
                'description' => _a('Use block effects or set custom effect on theme'),
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'more-show' => array(
                'title' => _a('Show More link to module page'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'more-link' => array(
                'title' => _a('Set More link to module page'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'string',
                'value' => '',
            ),
        ),
    ),
);