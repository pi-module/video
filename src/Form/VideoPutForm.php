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

class VideoPutForm extends BaseForm
{
    public function __construct($name = null, $option = [])
    {
        $this->option = $option;
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new VideoPutFilter($this->option);
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

        // video_path
        $this->add(
            [
                'name'       => 'video_path',
                'options'    => [
                    'label' => __('Video path'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => sprintf(
                        '<ul><li>%s</li><li>%s</li><li>%s</li></ul>',
                        __('Path of video file or source, put it without add / on start and end'),
                        __('Some type of servers have default path and after that it can be empty ( like MistServer or Wowza )'),
                        __('Example : upload/video/2016/09')
                    ),
                    'required'    => false,
                ],
            ]
        );

        // video_file
        $this->add(
            [
                'name'       => 'video_file',
                'options'    => [
                    'label' => __('Video source'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => sprintf(
                        '<ul><li>%s</li><li>%s</li><li>%s</li><li>%s</li></ul>',
                        __('Video file name or stream name ( stream key )'),
                        __('Example : myFileStream, ( for MistServer or Wowza )'),
                        __('Example : 360p.mp4,480p.mp4,720p.mp4, ( for nginx  multi quality )'),
                        __('Example : myFile.mp4, ( for file server )')
                    ),
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