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

class AdminSearchFilter extends InputFilter
{
    public function __construct()
    {
        // title
        $this->add(
            [
                'name'     => 'title',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );
        // category
        $this->add(
            [
                'name'     => 'category',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );
        // brand
        $this->add(
            [
                'name'     => 'brand',
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
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );
        // recommended
        $this->add(
            [
                'name'     => 'recommended',
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