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

class VideoUploadFilter extends InputFilter
{
    public function __construct()
    {
        // slug
        $this->add(array(
            'name'          => 'slug',
            'required'      => true,
            'filters'       => array(
                array(
                    'name'  => 'StringTrim',
                ),
            ),
            'validators'    => array(
                new \Module\Video\Validator\SlugDuplicate(array(
                    'module'            => Pi::service('module')->current(),
                    'table'             => 'video',
                )),
            ),
        ));
        // video
        $this->add(array(
            'name' => 'video',
            'required' => false,
        ));
    }
}