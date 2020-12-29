<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\Video\Form;

use Pi;
use Pi\Form\Form as BaseForm;

class PlaylistForm extends BaseForm
{
    public function __construct($name = null, $option = [])
    {
        $this->option = $option;
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new PlaylistForm($this->option);
        }
        return $this->filter;
    }

    public function init()
    {
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

        // main_image
        $this->add(
            [
                'name'    => 'main_image',
                'type'    => 'Module\Media\Form\Element\Media',
                'options' => [
                    'label'  => __('Main image'),
                    'module' => 'video',
                ],
                'attributes' => [
                    'required' => true,
                ],
            ]
        );

        // status
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

        // back_url
        $this->add(
            [
                'name'       => 'back_url',
                'options'    => [
                    'label' => __('Back URL after order'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => '',
                ],
            ]
        );

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
