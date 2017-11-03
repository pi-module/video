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
        // id
        $this->add([
            'name'       => 'id',
            'attributes' => [
                'type' => 'hidden',
            ],
        ]);
        // slug
        $this->add([
            'name'       => 'slug',
            'attributes' => [
                'type' => 'hidden',
            ],
        ]);
        // Check server type
        switch ($this->option['server']['type']) {
            case 'file':
                // video_path
                $this->add([
                    'name'       => 'video_path',
                    'options'    => [
                        'label' => __('Video path'),
                    ],
                    'attributes' => [
                        'type'        => 'text',
                        'description' => __('Without add / on start and end, for example : upload/video/2016/09'),
                        'required'    => true,
                    ],
                ]);
                // video_file
                $this->add([
                    'name'       => 'video_file',
                    'options'    => [
                        'label' => __('Video file name'),
                    ],
                    'attributes' => [
                        'type'        => 'text',
                        'description' => __('File name by extension, for example file.mp4'),
                        'required'    => true,
                    ],
                ]);
                break;

            case 'wowza':
                // video_file
                $this->add([
                    'name'       => 'video_file',
                    'options'    => [
                        'label' => __('Wowza stream link'),
                    ],
                    'attributes' => [
                        'type'        => 'text',
                        'description' => __('Put wowza url whitout ip or domain name'),
                        'required'    => true,
                    ],
                ]);
                break;

            case 'qmery':
                // video_qmery_hash
                $this->add([
                    'name'       => 'video_qmery_hash',
                    'options'    => [
                        'label' => __('Qmery hash code'),
                    ],
                    'attributes' => [
                        'type'        => 'text',
                        'description' => __('hash code on qmery system , like : y4A7rBoLwe'),
                        'required'    => true,
                    ],
                ]);
                // video_qmery_id
                $this->add([
                    'name'       => 'video_qmery_id',
                    'options'    => [
                        'label' => __('Qmery video id'),
                    ],
                    'attributes' => [
                        'type'     => 'text',
                        'required' => true,
                    ],
                ]);
                break;
        }
        // Save
        $this->add([
            'name'       => 'submit',
            'type'       => 'submit',
            'attributes' => [
                'value' => __('Submit'),
            ],
        ]);
    }
}