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

namespace Module\Video\Api;

use Pi;
use Pi\Application\Api\AbstractApi;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;
use FFMpeg\FFProbe;

/*
 * Pi::api('convert', 'video')->getDimensionList();
 */

class Convert extends AbstractApi
{
    public function getDimensionList(): array
    {
        return [
            [
                'name'   => '720p',
                'width'  => 1280,
                'height' => 720,
            ],
        ];
    }

    public function doConvert($videoId): array
    {
        // Set default result
        $result = [
            'result' => false,
            'data'   => [],
            'error'  => [
                'code'    => 1,
                'message' => __('Nothing selected'),
            ],
        ];

        // Get video
        $video = Pi::api('video', 'video')->getVideo($videoId);

        // Get server
        $serverList = Pi::registry('serverList', 'video')->read();
        $server     = $serverList[$video['video_server']];

        // Set video path
        $sourcePath = Pi::path(sprintf('%s/%s', $video['video_path'], $video['video_file']));

        // Check file exist
        if (!Pi::service('file')->exists($sourcePath)) {
            $result['message'] = __('Video file not exist');
            return $result;
        }

        // Set convert dimension
        $dimensionList = $this->getDimensionList();

        // Convert
        $convertResult = [];
        foreach ($dimensionList as $dimensionSingle) {

            // Set convert name
            $convertName = sprintf(
                '%s-%s.mp4',
                pathinfo($video['video_file'], PATHINFO_FILENAME),
                $dimensionSingle['name']
            );

            // Set save path
            $convertPath = Pi::path(sprintf('%s/%s', $video['video_path'], $convertName));

            // Check file exist
            if (Pi::service('file')->exists($convertPath)) {
                Pi::service('file')->remove($convertPath);
            }

            // Set ffmpeg settings
            $ffmpeg    = FFMpeg::create();
            $ffprobe   = FFProbe::create();
            $format    = new X264('libfdk_aac', 'libx264');
            $dimension = new Dimension((int)$dimensionSingle['width'], (int)$dimensionSingle['height']);
            $duration  = (int)$ffprobe->format($sourcePath)->get('duration');

            // do convert
            $convert = $ffmpeg->open($sourcePath);
            $convert->filters()->resize($dimension)->synchronize();
            $convert->save($format, $convertPath);

            $convertResult[] = [
                'duration' => $duration,
                'name'     => $convertName,
                'path'     => $convertPath,
            ];
        }

        // upload
        $uploadResult = [];
        foreach ($convertResult as $convertSingle) {
            // Set ftp connection
            $ftp = sprintf(
                'ftp://%s/%s',
                $server['uri'],
                $convertSingle['name']
            );

            // Open file
            $fp = fopen($convertSingle['path'], 'r');

            // Start upload by curl
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ftp);
            curl_setopt($ch, CURLOPT_UPLOAD, 1);
            curl_setopt($ch, CURLOPT_INFILE, $fp);
            curl_setopt($ch, CURLOPT_INFILESIZE, filesize($convertSingle['path']));
            curl_setopt($ch, CURLOPT_USERPWD, sprintf('%s:%s', $server['username'], $server['password']));
            curl_exec($ch);
            $chResult = curl_errno($ch);
            curl_close($ch);

            // Check result
            if ($chResult != 0) {
                $result['message'] = __('File upload to remote server error.');
                return $result;
            }
        }

        // Set single result
        $convertResult = array_shift($convertResult);

        // Set update values
        $values = [
            'time_update'    => time(),
            'video_status'   => 1,
            'video_duration' => $convertResult['duration'],
            'video_file'     => $convertResult['name'],
        ];

        // Save
        $row = Pi::model('video', $this->getModule())->find($video['id']);
        $row->assign($values);
        $row->save();

        // Set default result
        return [
            'result' => true,
            'data'   => [
                'message' => __('Video convert and upload successfully'),
            ],
            'error'  => [],
        ];
    }
}