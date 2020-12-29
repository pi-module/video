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
use Laminas\InputFilter\InputFilter;

class VideoFilter extends InputFilter
{
    public function __construct($option = [])
    {
        // title
        $this->add(
            [
                'name'     => 'title',
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );

        // slug
        $this->add(
            [
                'name'       => 'slug',
                'required'   => false,
                'filters'    => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    new \Module\Video\Validator\SlugDuplicate(
                        [
                            'module' => Pi::service('module')->current(),
                            'table'  => 'video',
                            'id'     => $option['id'],
                        ]
                    ),
                ],
            ]
        );

        // text_summary
        $this->add(
            [
                'name'     => 'text_summary',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );

        // text_description
        $this->add(
            [
                'name'     => 'text_description',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );

        // status
        $this->add(
            [
                'name'     => 'status',
                'required' => true,
            ]
        );

        // playlist
        $this->add(
            [
                'name'     => 'playlist',
                'required' => false,
            ]
        );

        // category
        $this->add(
            [
                'name'     => 'category',
                'required' => true,
            ]
        );

        // category_main
        $this->add(
            [
                'name'       => 'category_main',
                'required'   => true,
                'validators' => [
                    new \Module\Video\Validator\Category,
                ],
            ]
        );

        // brand
        if ($option['brand_system']) {
            $this->add(
                [
                    'name'     => 'brand',
                    'required' => false,
                ]
            );
        }

        // main_image
        $this->add(
            [
                'name'     => 'main_image',
                'required' => false,
            ]
        );

        // video_duration
        $this->add(
            [
                'name'     => 'video_duration',
                'required' => false,
            ]
        );

        // Check is admin
        if ($option['side'] == 'admin') {
            switch ($option['sale_video']) {
                case 'package':
                    // sale
                    $this->add(
                        [
                            'name'     => 'sale_type',
                            'required' => false,
                        ]
                    );
                    break;

                case 'single':
                    // sale
                    $this->add(
                        [
                            'name'     => 'sale_type',
                            'required' => false,
                        ]
                    );
                    // sale_price
                    $this->add(
                        [
                            'name'     => 'sale_price',
                            'required' => false,
                        ]
                    );
                    break;
            }
        }

        // seo_title
        $this->add(
            [
                'name'     => 'seo_title',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );

        // seo_keywords
        $this->add(
            [
                'name'     => 'seo_keywords',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );

        // seo_description
        $this->add(
            [
                'name'     => 'seo_description',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );

        // tag
        if (Pi::service('module')->isActive('tag')) {
            $this->add(
                [
                    'name'     => 'tag',
                    'required' => false,
                ]
            );
        }
    }
}
