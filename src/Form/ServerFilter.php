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
use Zend\InputFilter\InputFilter;

class ServerFilter extends InputFilter
{
    public function __construct()
    {
        // id
        $this->add(array(
            'name' => 'id',
            'required' => false,
        ));
        // title
        $this->add(array(
            'name' => 'title',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // status
        $this->add(array(
            'name' => 'status',
            'required' => false,
        ));
        // type
        $this->add(array(
            'name' => 'type',
            'required' => false,
        ));
        // url
        $this->add(array(
            'name' => 'url',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // default
        $this->add(array(
            'name' => 'default',
            'required' => false,
        ));
        // qmery_upload_token
        $this->add(array(
            'name' => 'qmery_upload_token',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // qmery_update_token
        $this->add(array(
            'name' => 'qmery_update_token',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // qmery_group_id
        $this->add(array(
            'name' => 'qmery_group_id',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // qmery_group_hash
        $this->add(array(
            'name' => 'qmery_group_hash',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // qmery_import
        $this->add(array(
            'name' => 'qmery_import',
            'required' => false,
        ));
    }
}