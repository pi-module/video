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
use Laminas\InputFilter\InputFilter;

class ServerFilter extends InputFilter
{
    public function __construct($option= [])
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

        // status
        $this->add(
            [
                'name'     => 'status',
                'required' => false,
            ]
        );

        // type
        $this->add(
            [
                'name'     => 'type',
                'required' => false,
            ]
        );

        // url
        $this->add(
            [
                'name'     => 'url',
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );

        // application
        $this->add(
            [
                'name'     => 'application',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );

        // default
        $this->add(
            [
                'name'     => 'default',
                'required' => false,
            ]
        );

        // token
        $this->add(
            [
                'name'     => 'token',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );

        // username
        $this->add(
            [
                'name'     => 'username',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );

        // password
        $this->add(
            [
                'name'     => 'password',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );
    }
}
