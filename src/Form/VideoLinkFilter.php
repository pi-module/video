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
    public function __construct($option = array())
    {
        // id
        $this->add(array(
            'name' => 'id',
            'required' => false,
        ));
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
        // Check server type
        switch ($option['server']['type']) {
            case 'file':
                // video_path
                $this->add(array(
                    'name' => 'video_path',
                    'required' => true,
                    'filters' => array(
                        array(
                            'name' => 'StringTrim',
                        ),
                    ),
                ));
                // video_file
                $this->add(array(
                    'name' => 'video_file',
                    'required' => true,
                    'filters' => array(
                        array(
                            'name' => 'StringTrim',
                        ),
                    ),
                ));
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
                $this->add(array(
                    'name' => 'video_file',
                    'required' => true,
                    'filters' => array(
                        array(
                            'name' => 'StringTrim',
                        ),
                    ),
                ));
                break;

            case 'qmery':
                // video_qmery_hash
                $this->add(array(
                    'name' => 'video_qmery_hash',
                    'required' => true,
                    'filters' => array(
                        array(
                            'name' => 'StringTrim',
                        ),
                    ),
                ));
                // video_qmery_id
                $this->add(array(
                    'name' => 'video_qmery_id',
                    'required' => true,
                    'filters' => array(
                        array(
                            'name' => 'StringTrim',
                        ),
                    ),
                ));
                break;
        }
    }
}