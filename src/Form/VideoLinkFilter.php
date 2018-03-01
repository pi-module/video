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

class VideoLinkFilter extends InputFilter
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
        // Check server type
        switch ($option['server']['type']) {
            case 'file':
                // video_path
                $this->add([
                    'name'     => 'video_path',
                    'required' => true,
                    'filters'  => [
                        [
                            'name' => 'StringTrim',
                        ],
                    ],
                ]);
                // video_file
                $this->add([
                    'name'     => 'video_file',
                    'required' => true,
                    'filters'  => [
                        [
                            'name' => 'StringTrim',
                        ],
                    ],
                ]);
                break;

            case 'wowza':
                // video_path
                /* $this->add(array(
                    'name' => 'video_path',
                    'required' => true,
                    'filters' => array(
                        array(
                            'name' => 'StringTrim',
                        ),
                    ),
                )); */
                // video_file
                $this->add([
                    'name'     => 'video_file',
                    'required' => true,
                    'filters'  => [
                        [
                            'name' => 'StringTrim',
                        ],
                    ],
                ]);
                break;

            case 'qmery':
                // qmery_url
                $this->add([
                    'name'     => 'qmery_url',
                    'required' => true,
                    'filters'  => [
                        [
                            'name' => 'StringTrim',
                        ],
                    ],
                ]);
                break;
        }
    }
}