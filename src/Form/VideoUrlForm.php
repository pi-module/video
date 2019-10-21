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

class VideoUrlForm extends BaseForm
{
    public function __construct($name = null, $option = [])
    {
        $this->option = $option;
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new VideoUrlFilter($this->option);
        }
        return $this->filter;
    }

    public function init()
    {
        // slug
        $this->add(
            [
                'name'       => 'slug',
                'attributes' => [
                    'type' => 'hidden',
                ],
            ]
        );

        // video_url
        $this->add(
            [
                'name'       => 'video_url',
                'options'    => [
                    'label' => __('Video full url'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => __('Full video link, based on your selected server mp4 or hls player loaded for play your video'),
                    'required'    => true,
                ],
            ]
        );

        // Save
        $this->add(
            [
                'name'       => 'submit',
                'type'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => __('Submit'),
                ],
            ]
        );
    }
}