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

class ServerForm extends BaseForm
{
    public function __construct($name = null, $option = [])
    {
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new ServerFilter;
        }
        return $this->filter;
    }

    public function init()
    {
        // id
        $this->add(
            [
                'name'       => 'id',
                'attributes' => [
                    'type' => 'hidden',
                ],
            ]
        );
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
        // type
        $this->add(
            [
                'name'       => 'type',
                'type'       => 'select',
                'options'    => [
                    'label'         => __('Type'),
                    'value_options' => [
                        'file'  => __('File server'),
                        'wowza' => __('Wowza server'),
                        'qmery' => __('Qmery server'),
                    ],
                ],
                'attributes' => [
                    'required' => true,
                ],
            ]
        );
        // url
        $this->add(
            [
                'name'       => 'url',
                'options'    => [
                    'label' => __('Url'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => sprintf(
                        '<ul><li>%s</li><li>%s</li><li>%s</li></ul>',
                        __('File : set url or ip by http:// or https:// without end slash'),
                        __('Wowza : set url or ip without http:// or https:// and end slash'),
                        __('Qmery : http://www.qmery.com or https://www.qmery.com')
                    ),
                    'required'    => true,
                ],
            ]
        );
        // default
        $this->add(
            [
                'name'       => 'default',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Default server ?'),
                ],
                'attributes' => [
                    'description' => '',
                ],
            ]
        );
        // extra_setting
        $this->add(
            [
                'name'    => 'extra_setting',
                'type'    => 'fieldset',
                'options' => [
                    'label' => __('Server setting'),
                ],
            ]
        );
        // qmery_token
        $this->add(
            [
                'name'       => 'qmery_token',
                'options'    => [
                    'label' => __('Qmery token'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => '',
                ],
            ]
        );
        // qmery_refresh_value
        $this->add(
            [
                'name'       => 'qmery_refresh_value',
                'options'    => [
                    'label' => __('Qmery refreshvalue'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => '',
                ],
            ]
        );
        // qmery_group_id
        $this->add(
            [
                'name'       => 'qmery_group_id',
                'options'    => [
                    'label' => __('Qmery group ID'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => '',
                ],
            ]
        );
        // qmery_group_hash
        $this->add(
            [
                'name'       => 'qmery_group_hash',
                'options'    => [
                    'label' => __('Qmery group hash'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => '',
                ],
            ]
        );
        // qmery_import
        $this->add(
            [
                'name'       => 'qmery_import',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Qmery import video'),
                ],
                'attributes' => [
                    'description' => __('Import video from qmery to website if video not exist on website'),
                ],
            ]
        );
        // qmery_show_embed
        $this->add(
            [
                'name'       => 'qmery_show_embed',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Qmery show embed'),
                ],
                'attributes' => [
                    'description' => __('Show embed code for copy to other pages and websites'),
                ],
            ]
        );
        // qmery_player_type
        $this->add(
            [
                'name'    => 'qmery_player_type',
                'type'    => 'select',
                'options' => [
                    'label'         => __('Qmery player type'),
                    'value_options' => [
                        'embed'  => __('embed'),
                        'iframe' => __('iframe'),
                    ],
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