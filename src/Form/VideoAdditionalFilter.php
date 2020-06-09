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

class VideoAdditionalFilter extends InputFilter
{
    public function __construct($option = [])
    {
        // Set attribute position
        $position = Pi::api('attribute', 'video')->attributePositionForm();
        // id
        $this->add(
            [
                'name'     => 'id',
                'required' => false,
            ]
        );
        // Set attribute
        if (!empty($option['field'])) {
            foreach ($position as $key => $value) {
                if (!empty($option['field'][$key])) {
                    foreach ($option['field'][$key] as $field) {
                        $this->add(
                            [
                                'name'     => $field['id'],
                                'required' => false,
                            ]
                        );
                    }
                }
            }
        }
    }
}    	