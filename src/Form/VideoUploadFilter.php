<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

/**
 * @author Somayeh Karami <somayeh.karami@gmail.com>
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\Video\Form;

use Pi;
use Zend\InputFilter\InputFilter;

class VideoUploadFilter extends InputFilter
{
    public function __construct($option = [])
    {
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
        // video
        $this->add([
            'name'     => 'video',
            'required' => false,
        ]);
    }
}