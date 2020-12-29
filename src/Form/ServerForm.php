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
        // Set server type
        $option['serverType'] = [];
        $serverType           = Pi::registry('serverType', 'video')->read();
        foreach ($serverType as $type) {
            $option['serverType'][$type['name']] = $type['title'];
        }

        $this->option = $option;
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new ServerFilter($this->option);
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
                    'label'         => __('Server type'),
                    'value_options' => $this->option['serverType'],
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
                    'label' => __('Server url'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => sprintf(
                        '<ul><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li></ul>',
                        __('Set url or ip by http:// or https:// without end slash'),
                        __('If your service run on any special port, put port number after server url like :PORT'),
                        __('Example : https://stream.piengine.org'),
                        __('Example : https://stream.piengine.org:8888'),
                        __('Example : https://127.0.0.1:1935')
                    ),
                    'required'    => true,
                ],
            ]
        );

        // application
        $this->add(
            [
                'name'       => 'application',
                'options'    => [
                    'label' => __('Server application'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => __('Internal server video application, Just if you set it on your stream server and needed to put in full url playable'),
                    'required'    => false,
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
                    'required'    => false,
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

        // token
        $this->add(
            [
                'name'       => 'token',
                'options'    => [
                    'label' => __('Authentication token'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => __('Some service need token for stream access, if your service needed it, please put it here'),
                    'required'    => false,
                ],
            ]
        );

        // username
        $this->add(
            [
                'name'       => 'username',
                'options'    => [
                    'label' => __('Authentication username'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => __('Some service need username and password for stream access, if your service needed it, please put it here'),
                    'required'    => false,
                ],
            ]
        );

        // password
        $this->add(
            [
                'name'       => 'password',
                'options'    => [
                    'label' => __('Authentication password'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => __('Some service need username and password for stream access, if your service needed it, please put it here'),
                    'required'    => false,
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
