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

class SitemapForm extends BaseForm
{
    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    public function init()
    {
        // type
        $this->add(array(
            'name' => 'type',
            'type' => 'select',
            'options' => array(
                'label' => __('Select for rebuild'),
                'value_options' => array(
                    1 => __('All of tables'),
                    2 => __('Just video table'),
                    3 => __('Just category table'),
                ),
            ),
            'attributes' => array(
                'required' => true,
            )
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