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
namespace Module\Video\Api;

use Pi;
use Pi\Application\Api\AbstractApi;
use Zend\Math\Rand;

/*
 * Pi::api('qmery', 'video')->upload($video);
 * Pi::api('qmery', 'video')->link($video, $link);
 * Pi::api('qmery', 'video')->updateListToWebsite($server, $page);
 */

class Qmery extends AbstractApi
{
    public function update($video)
    {
        // Set retrun
        $result = array();
        // Canonize video
        $video = Pi::api('video', 'video')->canonizeVideoFilter($video);
        // Get video from qmery
        $apiUrl = 'https://api.qmery.com/v1/videos/%s.json?api_token=%s';
        $apiUrl = sprintf($apiUrl, $video['video_qmery_hash'], $video['server']['qmery_upload_token']);
        $videoQmery = Pi::service('remote')->get($apiUrl);
        // Check qmery status
        switch ($videoQmery['status']) {
            case 'not ready':
                $result['status'] = 2;
                break;

            case 'ready':
                // Check file exist
                if (!empty($video['video_path']) && !empty($video['video_file'])) {
                    // Set video file
                    $file = Pi::path(
                        sprintf('upload/video/file/%s/%s', $video['video_path'], $video['video_file'])
                    );
                    // Check file exist
                    if (Pi::service('file')->exists($file)) {
                        // remove file
                        if (Pi::service('file')->remove($file)) {
                            // Update db
                            Pi::model('video', $this->getModule())->update(
                                array(
                                    'video_path' => '',
                                    'video_file' => '',
                                ),
                                array(
                                    'id' => $video['id']
                                )
                            );
                        }
                    }
                }
                // Check image
                /* ToDo : download image from qmery */
                if (!empty($video['file']) && !empty($video['image'])) {
                    // Get config
                    $config = Pi::service('registry')->config->read($this->getModule(), 'image');
                    // Set video image
                    $image = Pi::path(
                        sprintf('upload/%s/original/%s/%s', $config['image_path'], $video['video_path'], $video['video_file'])
                    );
                    // Check image
                    if (Pi::service('file')->exists($image)) {

                    }
                }


                $result['status'] = 1;
                break;
        }
        return $result;
    }

    public function upload($video)
    {
        // Canonize video
        $video = Pi::api('video', 'video')->canonizeVideoFilter($video);

        // Check setting
        if (empty($video['server']['qmery_upload_token']) || empty($video['server']['qmery_group_id'])) {
            $result = array();
            $result['message'] = __('Please set token and group id');
            $result['status'] = 0;
        } else {
            // Set API url
            $apiUrl = sprintf(
                'https://api.qmery.com/v1/videos.json?api_token=%s',
                $video['server']['qmery_upload_token']
            );

            // Set fields
            $fields = array();
            $fields['user_id'] = Pi::user()->getId();
            $fields['title'] = $video['title'];
            $fields['group_id'] = $video['server']['qmery_group_id'];
            $fields['url'] = Pi::url(sprintf(
                '%s/%s',
                $video['video_path'],
                $video['video_file']
            ));
            $fields = json_encode($fields);

            /* // Set header
            $headers = array(
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Content-Length' => strlen($fields),
            );
            // Remote post
            Pi::service('remote')->post($apiUrl, $fields, $headers); */

            // Send information to qmery server
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($fields))
            );
            $qmeryResult = curl_exec($ch);
            $qmeryResult = json_decode($qmeryResult, true);
            if (isset($qmeryResult['id']) && isset($qmeryResult['hash_id'])) {
                $result = array();
                $result['status'] = 1;
                $result['qmery'] = $qmeryResult;
                // Update db
                Pi::model('video', $this->getModule())->update(
                    array(
                        'video_qmery_hash' => $qmeryResult['hash_id'],
                        'video_qmery_id' => $qmeryResult['id'],
                        'video_qmery_hls' => !empty($qmeryResult['hls']) ? $qmeryResult['hls'] : '',
                    ),
                    array(
                        'id' => $video['id']
                    )
                );
            } else {
                $result = array();
                $result['message'] = $qmeryResult;
                $result['status'] = 0;
            }
        }
        return $result;
    }

    public function link($video, $link)
    {
        // Canonize video
        $video = Pi::api('video', 'video')->canonizeVideoFilter($video);

        // Check setting
        if (empty($video['server']['qmery_upload_token']) || empty($video['server']['qmery_group_id'])) {
            $result = array();
            $result['message'] = __('Please set token and group id');
            $result['status'] = 0;
        } else {
            // Set API url
            $apiUrl = sprintf(
                'https://api.qmery.com/v1/videos.json?api_token=%s',
                $video['server']['qmery_upload_token']
            );

            // Set fields
            $fields = array();
            $fields['user_id'] = Pi::user()->getId();
            $fields['title'] = $video['title'];
            $fields['group_id'] = $video['server']['qmery_group_id'];
            $fields['url'] = $link;
            $fields = json_encode($fields);

            /* // Set header
            $headers = array(
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Content-Length' => strlen($fields),
            );
            // Remote post
            $qmeryResult = Pi::service('remote')->post($apiUrl, $fields, $headers); */

            // Send information to qmery server
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($fields))
            );
            $qmeryResult = curl_exec($ch);

            if (is_array($qmeryResult)) {
                $result = json_decode($qmeryResult, true);
                $result['status'] = 1;
                // Update db
                if (!empty($result['hash_id']) && !empty($result['id'])) {
                    Pi::model('video', $this->getModule())->update(
                        array(
                            'video_qmery_hash' => $result['hash_id'],
                            'video_qmery_id' => $result['id'],
                            'video_qmery_hls' => !empty($result['hls']) ? $result['hls'] : '',
                        ),
                        array(
                            'id' => $video['id']
                        )
                    );
                }
            } else {
                $result = array();
                $result['message'] = json_decode($qmeryResult, true);
                $result['status'] = 0;
            }
        }
        return $result;
    }

    public function updateListToQmery($videos)
    {
        return $videos;
    }

    public function updateListToWebsite($server, $page)
    {
        $apiUrl = 'https://api.qmery.com/v1/videos.json?api_token=%s&page=%s&per_page=%s&sort_dir';
        $apiUrl = sprintf($apiUrl, $server['qmery_update_token'], $page, 50);
        $videoList = Pi::service('remote')->get($apiUrl);
        $uid = Pi::user()->getId();
        $time = time();
        if (!empty($videoList)) {
            foreach ($videoList as $videoSingle) {
                if ($videoSingle['group_id'] == $server['qmery_group_id']) {
                    $video = Pi::model('video', $this->getModule())->find($videoSingle['id'], 'video_qmery_id');
                    if ($video) {
                        $video->video_qmery_hash = $videoSingle['hash_id'];
                        $video->video_qmery_id = $videoSingle['id'];
                        $video->video_qmery_hls = isset($videoSingle['hls']) ? $videoSingle['hls'] : '';
                        $video->save();
                    } elseif ($server['qmery_import']) {
                        $video = Pi::model('video', $this->getModule())->createRow();
                        $video->title = $videoSingle['title'];
                        $video->slug = Rand::getString(16, 'abcdefghijklmnopqrstuvwxyz123456789', true);
                        $video->time_create = $time;
                        $video->time_update = $time;
                        $video->uid = $uid;
                        $video->status = 2;
                        $video->video_server = $server['id'];
                        $video->video_qmery_hash = $videoSingle['hash_id'];
                        $video->video_qmery_id = $videoSingle['id'];
                        $video->video_qmery_hls = isset($videoSingle['hls']) ? $videoSingle['hls'] : '';
                        $video->save();
                        // Set to link table
                        Pi::api('category', 'video')->setLink($video->id, 0, $time, $time, 2, $uid, 0);
                    }
                }
            }
        }
        return count($videoList);
    }
}