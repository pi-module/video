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
        $this->add([
            'name'     => 'id',
            'required' => false,
        ]);
        // title
        $this->add([
            'name'     => 'title',
            'required' => true,
            'filters'  => [
                [
                    'name' => 'StringTrim',
                ],
            ],
        ]);
        // status
        $this->add([
            'name'     => 'status',
            'required' => false,
        ]);
        // type
        $this->add([
            'name'     => 'type',
            'required' => false,
        ]);
        // url
        $this->add([
            'name'     => 'url',
            'required' => true,
            'filters'  => [
                [
                    'name' => 'StringTrim',
                ],
            ],
        ]);
        // default
        $this->add([
            'name'     => 'default',
            'required' => false,
        ]);
        // qmery_token
        $this->add([
            'name'     => 'qmery_token',
            'required' => false,
            'filters'  => [
                [
                    'name' => 'StringTrim',
                ],
            ],
        ]);
        // qmery_refresh_value
        $this->add([
            'name'     => 'qmery_refresh_value',
            'required' => false,
            'filters'  => [
                [
                    'name' => 'StringTrim',
                ],
            ],
        ]);
        // qmery_group_id
        $this->add([
            'name'     => 'qmery_group_id',
            'required' => false,
            'filters'  => [
                [
                    'name' => 'StringTrim',
                ],
            ],
        ]);
        // qmery_group_hash
        $this->add([
            'name'     => 'qmery_group_hash',
            'required' => false,
            'filters'  => [
                [
                    'name' => 'StringTrim',
                ],
            ],
        ]);
        // qmery_import
        $this->add([
            'name'     => 'qmery_import',
            'required' => false,
        ]);
        // qmery_show_embed
        $this->add([
            'name'     => 'qmery_show_embed',
            'required' => false,
        ]);
        // qmery_player_type
        $this->add([
            'name'     => 'qmery_player_type',
            'required' => false,
            'filters'  => [
                [
                    'name' => 'StringTrim',
                ],
            ],
        ]);
    }
}