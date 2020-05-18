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

class VideoUploadFilter extends InputFilter
{
    public function __construct($option = [])
    {
        // video
        $this->add(
            [
                'name'     => 'video',
                'required' => false,
            ]
        );
    }
}