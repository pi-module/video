<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

/**
 * @author Somayeh Karami <somayeh.karami@gmail.com>
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\Video\Api;

use Pi;
use Pi\Application\Api\AbstractApi;
use Zend\Math\Rand;

/*
 * Pi::api('qmery', 'video')->getVideo($url, $token);
 * Pi::api('qmery', 'video')->updateVideo($video);
 * Pi::api('qmery', 'video')->updateVideoList($server, $page);
 * Pi::api('qmery', 'video')->uploadVideo($video, $link);
 * Pi::api('qmery', 'video')->downloadVideoImage($imageUrl);
 */

class Qmery extends AbstractApi
{
    public function getVideo($url, $token)
    {
        // Clean video
        $url  = str_replace('https://api.qmery.com/ovp/v/', '', $url);
        $hash = trim($url);

        // Set api url
        $apiUrl = 'https://api.qmery.com/ovp/videos/%s.json?api_token=%s';
        $apiUrl = sprintf($apiUrl, $hash, $token);

        // Get video from qmery
        return Pi::service('remote')->get($apiUrl);
    }

    public function updateVideo($video)
    {
        // Set retrun
        $result = [
            'status' => 0,
            'date'   => [],
        ];

        // Set api url
        $apiUrl = 'https://api.qmery.com/ovp/videos/%s.json?api_token=%s';
        $apiUrl = sprintf($apiUrl, $video['video_qmery_hash'], $video['server']['qmery_token']);

        // Get video from qmery
        $qmeryVideo     = Pi::service('remote')->get($apiUrl);
        $result['date'] = $qmeryVideo;

        // Check qmery status
        switch ($qmeryVideo['status']) {
            case 'not ready':
                $result['status'] = 2;
                break;

            case 'ready':
                $values = [];

                // Remove video file
                if (!empty($video['video_path']) && !empty($video['video_file'])) {
                    // Set video file
                    $file = Pi::path(
                        sprintf('upload/video/file/%s/%s', $video['video_path'], $video['video_file'])
                    );
                    // Check file exist
                    if (Pi::service('file')->exists($file)) {
                        // remove file
                        if (Pi::service('file')->remove($file)) {

                            // Set values
                            $values['video_path'] = '';
                            $values['video_file'] = '';
                        }
                    }
                }

                // Check and import image
                if (empty($video['image'])) {

                    // Download image
                    $qmeryImage = $this->downloadVideoImage($qmeryVideo['thumbnail'][1]);

                    // Set values
                    $values['image'] = $qmeryImage['image'];
                    $values['path']  = $qmeryImage['path'];
                }

                // Set video information
                $values['video_qmery_id']  = $qmeryVideo['id'];
                $values['video_qmery_hls'] = isset($qmeryVideo['hls']) ? $qmeryVideo['hls'] : '';

                // Update db
                Pi::model('video', $this->getModule())->update(
                    $values,
                    [
                        'id' => $video['id'],
                    ]
                );

                $result['status'] = 1;
                break;
        }

        return $result;
    }

    public function updateVideoList($server, $page)
    {
        // Set url
        $apiUrl = 'https://api.qmery.com/ovp/videos.json?api_token=%s&page=%s&per_page=%s&sort=id';
        $apiUrl = sprintf($apiUrl, $server['qmery_token'], $page, 25);

        // Get video list
        $videoList = Pi::service('remote')->get($apiUrl);

        // Set
        $uid  = Pi::user()->getId();
        $time = time();

        // Check video list and presses
        if (!empty($videoList)) {
            foreach ($videoList as $videoSingle) {

                if ($videoSingle['group_id'] == $server['qmery_group_id']) {
                    // try find video
                    $video = Pi::model('video', $this->getModule())->find($videoSingle['id'], 'video_qmery_id');

                    // Check video exit
                    if ($video) {
                        // Save
                        $video->video_qmery_hash = $videoSingle['hash_id'];
                        $video->video_qmery_id   = $videoSingle['id'];
                        $video->video_qmery_hls  = isset($videoSingle['hls']) ? $videoSingle['hls'] : '';
                        $video->save();
                    } elseif ($server['qmery_import']) {
                        // Set slug
                        $slug = Rand::getString(16, 'abcdefghijklmnopqrstuvwxyz123456789', true);

                        // Download image
                        $qmeryImage = $this->downloadVideoImage($videoSingle['thumbnail'][1]);

                        // Save
                        $video                   = Pi::model('video', $this->getModule())->createRow();
                        $video->title            = $videoSingle['title'];
                        $video->slug             = $slug;
                        $video->time_create      = $time;
                        $video->time_update      = $time;
                        $video->uid              = $uid;
                        $video->status           = 2;
                        $video->image            = $qmeryImage['image'];
                        $video->path             = $qmeryImage['path'];
                        $video->video_server     = $server['id'];
                        $video->video_qmery_hash = $videoSingle['hash_id'];
                        $video->video_qmery_id   = $videoSingle['id'];
                        $video->video_qmery_hls  = isset($videoSingle['hls']) ? $videoSingle['hls'] : '';
                        $video->save();

                        // Set to link table
                        // Pi::api('category', 'video')->setLink($video->id, 0, $time, $time, 2, $uid, 0, 0);
                    }
                }
            }
        }

        return count($videoList);
    }

    public function uploadVideo($video, $link = '')
    {
        // Canonize video
        $video = Pi::api('video', 'video')->canonizeVideoFilter($video);

        // Check setting
        if (empty($video['server']['qmery_token']) || empty($video['server']['qmery_group_id'])) {
            $result            = [];
            $result['message'] = __('Please set token and group id');
            $result['status']  = 0;
        } else {
            // Set API url
            $apiUrl = sprintf(
                'https://api.qmery.com/ovp/videos.json?api_token=%s',
                $video['server']['qmery_token']
            );

            // Set link
            if (empty($link)) {
                $link = Pi::url(sprintf(
                    '%s/%s',
                    $video['video_path'],
                    $video['video_file']
                ));
            }

            // Set fields
            $fields             = [];
            $fields['user_id']  = Pi::user()->getId();
            $fields['title']    = $video['title'];
            $fields['group_id'] = $video['server']['qmery_group_id'];
            $fields['url']      = $link;

            // Send information to qmery server
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: multipart/form-data',
                ]
            );
            $qmeryResult = curl_exec($ch);
            $qmeryResult = json_decode($qmeryResult, true);

            // Check result
            if (isset($qmeryResult['id']) && isset($qmeryResult['hash_id'])) {
                $result           = [];
                $result['status'] = 1;
                $result['qmery']  = $qmeryResult;
                // Update db
                Pi::model('video', $this->getModule())->update(
                    [
                        'video_qmery_hash' => $qmeryResult['hash_id'],
                        'video_qmery_id'   => $qmeryResult['id'],
                        'video_qmery_hls'  => !empty($qmeryResult['hls']) ? $qmeryResult['hls'] : '',
                    ],
                    [
                        'id' => $video['id'],
                    ]
                );
            } else {
                $result            = [];
                $result['message'] = $qmeryResult;
                $result['status']  = 0;
            }
        }
        return $result;
    }

    public function downloadVideoImage($imageUrl)
    {
        // Set key and image and path
        $key          = Rand::getString(16, 'abcdefghijklmnopqrstuvwxyz123456789', true);
        $image        = sprintf('%s.jpg', $key);
        $path         = sprintf('%s/%s', date('Y'), date('m'));
        $originalPath = Pi::path(sprintf('upload/video/image/original/%s', $path));
        $imagePath    = sprintf('%s/%s', $originalPath, $image);

        // Build upload image path
        Pi::service('file')->mkdir($originalPath);

        // download image
        Pi::service('remote')->download($imageUrl, $imagePath);

        // Resize image
        Pi::api('image', 'video')->process($image, $path);

        return [
            'image' => $image,
            'path'  => $path,
        ];
    }
}