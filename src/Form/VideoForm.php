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

namespace Module\Video\Form;

use Pi;
use Pi\Form\Form as BaseForm;

class VideoForm extends BaseForm
{
    public function __construct($name = null, $option = [])
    {
        $this->category  = [0 => ' '];
        $this->thumbUrl  = empty($option['thumbUrl']) ? '' : $option['thumbUrl'];
        $this->removeUrl = empty($option['removeUrl']) ? '' : $option['removeUrl'];
        $this->option    = $option;
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new VideoFilter($this->option);
        }
        return $this->filter;
    }

    public function init()
    {
        // extra_general
        $this->add(
            [
                'name'    => 'extra_general',
                'type'    => 'fieldset',
                'options' => [
                    'label' => __('General options'),
                ],
            ]
        );
        // id
        $this->add(
            [
                'name'       => 'id',
                'attributes' => [
                    'type' => 'hidden',
                ],
            ]
        );
        // status
        if ($this->option['side'] == 'admin') {
            $this->add(
                [
                    'name'       => 'status',
                    'type'       => 'select',
                    'options'    => [
                        'label'         => __('Status'),
                        'value_options' => [
                            1 => __('Published'),
                            2 => __('Pending review'),
                            3 => __('Draft'),
                            4 => __('Private'),
                            5 => __('Delete'),
                        ],
                    ],
                    'attributes' => [
                        'required' => true,
                        'class'    => 'video-from-status',
                    ],
                ]
            );
        } else {
            $this->add(
                [
                    'name'       => 'status',
                    'attributes' => [
                        'type' => 'hidden',
                    ],
                ]
            );
        }
        // title
        $this->add(
            [
                'name'       => 'title',
                'options'    => [
                    'label' => __('Title'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => '',
                    'required'    => true,
                ],
            ]
        );
        // slug
        if ($this->option['side'] == 'admin') {
            $this->add(
                [
                    'name'       => 'slug',
                    'options'    => [
                        'label' => __('slug'),
                    ],
                    'attributes' => [
                        'type'        => 'text',
                        'description' => '',
                    ],
                ]
            );
        } else {
            $this->add(
                [
                    'name'       => 'slug',
                    'attributes' => [
                        'type' => 'hidden',
                    ],
                ]
            );
        }
        // text_summary
        $this->add(
            [
                'name'       => 'text_summary',
                'options'    => [
                    'label' => __('Summary'),
                ],
                'attributes' => [
                    'type'        => 'textarea',
                    'rows'        => '5',
                    'cols'        => '40',
                    'description' => __('Keep summery short, 2 or 3 lines'),
                ],
            ]
        );
        // text_description
        $this->add(
            [
                'name'       => 'text_description',
                'options'    => [
                    'label'  => __('Description'),
                    'editor' => 'html',
                ],
                'attributes' => [
                    'type'        => 'editor',
                    'description' => '',
                ],
            ]
        );
        // category
        $this->add(
            [
                'name'       => 'category',
                'type'       => 'Module\Video\Form\Element\Category',
                'options'    => [
                    'label'    => __('Category'),
                    'category' => $this->category,
                ],
                'attributes' => [
                    'required' => true,
                ],
            ]
        );
        // category_main
        $this->add(
            [
                'name'       => 'category_main',
                'type'       => 'Module\Video\Form\Element\Category',
                'options'    => [
                    'label'    => __('Main category'),
                    'category' => $this->category,
                ],
                'attributes' => [
                    'size'        => 1,
                    'multiple'    => 0,
                    'description' => __('Use for breadcrumbs ,mobile app and attribute'),
                    'required'    => true,
                ],
            ]
        );
        // brand
        if ($this->option['brand_system']) {
            $this->add(
                [
                    'name'       => 'brand',
                    'type'       => 'Module\Video\Form\Element\Brand',
                    'options'    => [
                        'label'    => __('Brand'),
                        'category' => $this->category,
                    ],
                    'attributes' => [
                        'size'     => 1,
                        'multiple' => 0,
                        'required' => false,
                    ],
                ]
            );
        }
        // Image
        if ($this->thumbUrl) {
            $this->add(
                [
                    'name'       => 'imageview',
                    'type'       => 'Module\Video\Form\Element\Image',
                    'options'    => [
                        //'label' => __('Image'),
                    ],
                    'attributes' => [
                        'src' => $this->thumbUrl,
                    ],
                ]
            );
            if ($this->option['side'] == 'admin') {
                $this->add(
                    [
                        'name'       => 'remove',
                        'type'       => 'Module\Video\Form\Element\Remove',
                        'options'    => [
                            'label' => __('Remove image'),
                        ],
                        'attributes' => [
                            'link' => $this->removeUrl,
                        ],
                    ]
                );
            }
            $this->add(
                [
                    'name'       => 'image',
                    'attributes' => [
                        'type' => 'hidden',
                    ],
                ]
            );
        } else {
            $this->add(
                [
                    'name'       => 'image',
                    'options'    => [
                        'label' => __('Image'),
                    ],
                    'attributes' => [
                        'type'        => 'file',
                        'description' => '',
                    ],
                ]
            );
        }
        // Video
        if ($this->option['side'] == 'admin') {
            // extra_video
            $this->add(
                [
                    'name'    => 'extra_video',
                    'type'    => 'fieldset',
                    'options' => [
                        'label' => __('Video file'),
                    ],
                ]
            );
            // video_duration
            $this->add(
                [
                    'name'       => 'video_duration',
                    'options'    => [
                        'label' => __('Duration ( second )'),
                    ],
                    'attributes' => [
                        'type'        => 'text',
                        'description' => '',
                    ],
                ]
            );
        } else {
            // video_duration
            $this->add(
                [
                    'name'       => 'video_duration',
                    'attributes' => [
                        'type' => 'hidden',
                    ],
                ]
            );
        }
        // Price
        if ($this->option['side'] == 'admin') {
            switch ($this->option['sale_video']) {
                case 'package':
                    // sale_type
                    $this->add(
                        [
                            'name'       => 'sale_type',
                            'type'       => 'select',
                            'options'    => [
                                'label'         => __('Sale video'),
                                'value_options' => [
                                    'free' => __('Free'),
                                    'paid' => __('Paid'),
                                ],
                            ],
                            'attributes' => [
                                'description' => __('If check it, users should buy package to watch this video'),
                            ],
                        ]
                    );
                    break;

                case 'single':
                    // sale_type
                    $this->add(
                        [
                            'name'       => 'sale_type',
                            'type'       => 'select',
                            'options'    => [
                                'label'         => __('Sale video'),
                                'value_options' => [
                                    'free' => __('Free'),
                                    'paid' => __('Paid'),
                                ],
                            ],
                            'attributes' => [
                                'description' => __('If check it and put price, users should pay to watch this video'),
                            ],
                        ]
                    );
                    // sale_price
                    $this->add(
                        [
                            'name'       => 'sale_price',
                            'options'    => [
                                'label' => __('Sale Video price'),
                            ],
                            'attributes' => [
                                'type'        => 'text',
                                'description' => '',
                            ],
                        ]
                    );
                    break;
            }
        }
        // Seo
        if ($this->option['side'] == 'admin') {
            // extra_seo
            $this->add(
                [
                    'name'    => 'extra_seo',
                    'type'    => 'fieldset',
                    'options' => [
                        'label' => __('SEO options'),
                    ],
                ]
            );
            // seo_title
            $this->add(
                [
                    'name'       => 'seo_title',
                    'options'    => [
                        'label' => __('SEO Title'),
                    ],
                    'attributes' => [
                        'type'        => 'textarea',
                        'rows'        => '2',
                        'cols'        => '40',
                        'description' => __('Between 10 to 70 character'),
                    ],
                ]
            );
            // seo_keywords
            $this->add(
                [
                    'name'       => 'seo_keywords',
                    'options'    => [
                        'label' => __('SEO Keywords'),
                    ],
                    'attributes' => [
                        'type'        => 'textarea',
                        'rows'        => '2',
                        'cols'        => '40',
                        'description' => __('Between 5 to 10 words'),
                    ],
                ]
            );
            // seo_description
            $this->add(
                [
                    'name'       => 'seo_description',
                    'options'    => [
                        'label' => __('SEO Description'),
                    ],
                    'attributes' => [
                        'type'        => 'textarea',
                        'rows'        => '3',
                        'cols'        => '40',
                        'description' => __('Between 80 to 160 character'),
                    ],
                ]
            );
            // tag
            if (Pi::service('module')->isActive('tag')) {
                $this->add(
                    [
                        'name'       => 'tag',
                        'type'       => 'tag',
                        'options'    => [
                            'label' => __('Tags'),
                        ],
                        'attributes' => [
                            'id'          => 'tag',
                            'description' => __('Use `|` as delimiter to separate tag terms'),
                        ],
                    ]
                );
            }
        } else {
            // seo_title
            $this->add(
                [
                    'name'       => 'seo_title',
                    'attributes' => [
                        'type' => 'hidden',
                    ],
                ]
            );
            // seo_keywords
            $this->add(
                [
                    'name'       => 'seo_keywords',
                    'attributes' => [
                        'type' => 'hidden',
                    ],
                ]
            );
            // seo_description
            $this->add(
                [
                    'name'       => 'seo_description',
                    'attributes' => [
                        'type' => 'hidden',
                    ],
                ]
            );
            // tag
            $this->add(
                [
                    'name'       => 'tag',
                    'attributes' => [
                        'type' => 'hidden',
                    ],
                ]
            );
        }
        // Save
        $this->add(
            [
                'name'       => 'submit',
                'type'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => __('Submit'),
                ],
            ]
        );
    }
}