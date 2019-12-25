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

class VideoLinkForm extends BaseForm
{
    public function __construct($name = null, $option = [])
    {
        $this->option = $option;
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new VideoLinkFilter($this->option);
        }
        return $this->filter;
    }

    public function init()
    {
        // video_server
        $this->add(
            [
                'name'       => 'video_server',
                'type'       => 'Module\Video\Form\Element\Server',
                'options'    => [
                    'label' => __('Server'),
                ],
                'attributes' => [
                    'description' => __('Select stream server'),
                    'required'    => true,
                    'class'       => 'video-server'
                    //'value'       => Pi::registry('serverDefault', 'video')->read(),
                ],
            ]
        );

        // video_path
        $this->add(
            [
                'name'       => 'video_path',
                'options'    => [
                    'label' => __('Video path or sub-application'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => sprintf(
                        '<ul><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li></ul>',
                        __('Path or sub-application name of video file or source, put it without add / on start and end'),
                        __('Some type of servers have sub-application name or path of video, put it here'),
                        __('Some type of servers dont have sub-application name or path of video, then it can be empty'),
                        __('Example : upload/video/2016/09'),
                        __('Example : demoApplication'),
                        __('Example : hls')
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
                    'label' => __('Video source name'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => sprintf(
                        '<ul><li>%s</li><li>%s</li><li>%s</li><li>%s</li></ul>',
                        __('Video file name or stream name ( stream key )'),
                        __('Example : myFileStream, ( for MistServer or Wowza )'),
                        __('Example : 360p.mp4,480p.mp4,720p.mp4, ( for nginx  multi quality )'),
                        __('Example : myFile.mp4, ( file name for file server )'),
                        __('Example : 374354745, ( id for youtube, aparat, vimeo )')
                    ),
                    'required'    => false,
                ],
            ]
        );

        // show_video_url
        if ($this->option['isNew'] && $this->option['serverCount'] > 1) {
            $this->add(
                [
                    'name'       => 'show_video_url',
                    'type'       => 'checkbox',
                    'options'    => [
                        'label' => __('Show video URL'),
                    ],
                    'attributes' => [
                        'description' => __('If you want add full url, useful for hls and file players'),
                        'class' => 'show-video-url'
                    ],
                ]
            );
        }

        // video_url
        $this->add(
            [
                'name'       => 'video_url',
                'options'    => [
                    'label' => __('Video full url'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => sprintf(
                        '<ul><li>%s</li><li>%s</li><li>%s</li></ul>',
                        __('If you put video file, leave it empty'),
                        __('If video url is empty, system generate video url from video source name and server information'),
                        __('Full video link, based on your selected server mp4 or hls player loaded for play your video')
                    ),
                    'required'    => false,
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