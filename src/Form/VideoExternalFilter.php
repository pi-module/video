<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt BSD 3-Clause License
 */

/**
 * @author Somayeh Karami <somayeh.karami@gmail.com>
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\Video\Form;

use Pi;
use Zend\InputFilter\InputFilter;

class VideoExternalFilter extends InputFilter
{
    public function __construct($option = [])
    {
        // id
        $this->add([
            'name'     => 'id',
            'required' => false,
        ]);
        // slug
        $this->add([
            'name'       => 'slug',
            'required'   => true,
            'filters'    => [
                [
                    'name' => 'StringTrim',
                ],
            ],
            'validators' => [
                new \Module\Video\Validator\SlugDuplicate([
                    'module' => Pi::service('module')->current(),
                    'table'  => 'video',
                ]),
            ],
        ]);
        // external_link
        $this->add([
            'name'     => 'external_link',
            'required' => true,
            'filters'  => [
                [
                    'name' => 'StringTrim',
                ],
            ],
        ]);
    }
}