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
use Zend\Json\Json;

/*
 * Pi::api('qmery', 'video')->upload($video);
 * Pi::api('qmery', 'video')->update($video);
 */

class Qmery extends AbstractApi
{
    public function upload($video)
    {
        // Canonize video
        $video = Pi::api('video', 'video')->canonizeVideoFilter($video);

        // Check setting
        if (empty($video['server']['qmery_upload_token']) || empty($video['server']['qmery_group_id'])) {
            $result = array();
            $result['status'] = 0;
            return $result;
        }

        // Set API url
        $apiUrl = sprintf(
            'http://api.qmery.com/v1/videos.json?api_token=%s',
            $video['server']['qmery_upload_token']
        );

        // Set fields
        $fields = array();
        $fields['user_id'] = Pi::user()->getId();
        $fields['group_id'] = $video['server']['qmery_group_id'];
        $fields['url'] = Pi::url(sprintf(
            '%s/%s',
            $video['video_path'],
            $video['video_file']
        ));
        // $fields['url'] = str_replace("https://", "http://", $fields['url']);
        $fields = Json::encode($fields);

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
        $result = curl_exec($ch);
        $result = Json::decode($result, true);
        $result['status'] = 0;

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
            $result['status'] = 1;
        }
        return $result;
    }

    public function update($video)
    {
        return $video;
    }
}