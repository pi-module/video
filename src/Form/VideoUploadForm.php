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
use Pi\Form\Form as BaseForm;

class VideoUploadForm  extends BaseForm
{
    public function __construct($name = null, $option = array())
    {
        $this->option = $option;
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new VideoUploadFilter($this->option);
        }
        return $this->filter;
    }

    public function init()
    {
        // slug
        $this->add(array(
            'name' => 'slug',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));
        // video
        $this->add(array(
            'name' => 'video',
            'options' => array(
                'label' => __('Video'),
            ),
            'attributes' => array(
                'type' => 'file',
                'description' => '',
                'id' => 'videoFile',
            )
        ));
        // Save
        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'value' => __('Upload'),
                'class' => 'btn btn-success videoUpload',
                'onclick' => 'upload_image();',
            )
        ));
    }
}