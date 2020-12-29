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
use Pi\Form\Form as BaseForm;

class VideoUploadForm extends BaseForm
{
    public function __construct($name = null, $option = [])
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
        // video
        $this->add(
            [
                'name'       => 'video',
                'options'    => [
                    'label' => __('Video'),
                ],
                'attributes' => [
                    'type'        => 'file',
                    'description' => '',
                    'id'          => 'videoFile',
                ],
            ]
        );

        // Save
        $this->add(
            [
                'name'       => 'submit',
                'type'       => 'submit',
                'attributes' => [
                    'value'   => __('Upload'),
                    'class'   => 'btn btn-primary videoUpload',
                    'onclick' => 'upload_image();',
                ],
            ]
        );
    }
}
