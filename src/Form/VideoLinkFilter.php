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

class VideoLinkFilter extends InputFilter
{
    public function __construct($option = [])
    {
        // video_server
        $this->add(
            [
                'name'     => 'video_server',
                'required' => true,
            ]
        );

        // video_path
        $this->add(
            [
                'name'     => 'video_path',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );

        // video_file
        $this->add(
            [
                'name'     => 'video_file',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );

        // video_url
        $this->add(
            [
                'name'     => 'video_url',
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
