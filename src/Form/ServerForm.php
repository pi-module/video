<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\Video\Form;

use Pi;
use Pi\Form\Form as BaseForm;

class ServerForm extends BaseForm
{
    public function __construct($name = null, $option = array())
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
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));
        // title
        $this->add(array(
            'name' => 'title',
            'options' => array(
                'label' => __('Title'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
                'required' => true,
            )
        ));
        // status
        $this->add(array(
            'name' => 'status',
            'type' => 'select',
            'options' => array(
                'label' => __('Status'),
                'value_options' => array(
                    1 => __('Published'),
                    2 => __('Pending review'),
                    3 => __('Draft'),
                    4 => __('Private'),
                    5 => __('Delete'),
                ),
            ),
            'attributes' => array(
                'required' => true,
            )
        ));
        // type
        $this->add(array(
            'name' => 'type',
            'type' => 'select',
            'options' => array(
                'label' => __('Type'),
                'value_options' => array(
                    'file' => __('File server'),
                    'wowza' => __('Wowza server'),
                    'qmery' => __('Qmery server'),
                ),
            ),
            'attributes' => array(
                'required' => true,
            )
        ));
        // url
        $this->add(array(
            'name' => 'url',
            'options' => array(
                'label' => __('Url'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => sprintf(
                    '<ul><li>%s</li><li>%s</li><li>%s</li></ul>',
                    __('File : set url or ip by http:// or https:// without end slash'),
                    __('Wowza : set url or ip without http:// or https:// and end slash'),
                    __('Qmery : http://www.qmery.com or https://www.qmery.com')
                ),
                'required' => true,
            )
        ));
        // default
        $this->add(array(
            'name' => 'default',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Default server ?'),
            ),
            'attributes' => array(
                'description' => '',
            )
        ));
        // extra_setting
        $this->add(array(
            'name' => 'extra_setting',
            'type' => 'fieldset',
            'options' => array(
                'label' => __('Server setting'),
            ),
        ));
        // qmery_upload_token
        $this->add(array(
            'name' => 'qmery_upload_token',
            'options' => array(
                'label' => __('Qmery upload token'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // qmery_update_token
        $this->add(array(
            'name' => 'qmery_update_token',
            'options' => array(
                'label' => __('Qmery update token'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // qmery_group_id
        $this->add(array(
            'name' => 'qmery_group_id',
            'options' => array(
                'label' => __('Qmery group ID'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // qmery_group_hash
        $this->add(array(
            'name' => 'qmery_group_hash',
            'options' => array(
                'label' => __('Qmery group hash'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // qmery_import
        $this->add(array(
            'name' => 'qmery_import',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Qmery import video'),
            ),
            'attributes' => array(
                'description' => __('Import video from qmery to website if video not exist on website'),
            )
        ));
        // qmery_show_embed
        $this->add(array(
            'name' => 'qmery_show_embed',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Qmery show embed'),
            ),
            'attributes' => array(
                'description' => __('Show embed code for copy to other pages and websites'),
            )
        ));
        // qmery_player_type
        $this->add(array(
            'name' => 'qmery_player_type',
            'type' => 'select',
            'options' => array(
                'label' => __('Qmery player type'),
                'value_options' => array(
                    'embed' => __('embed'),
                    'iframe' => __('iframe'),
                ),
            ),
        ));
        // Save
        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'value' => __('Submit'),
            )
        ));
    }
}