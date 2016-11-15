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
 * Pi::api('video', 'video')->getVideo($parameter, $type);
 * Pi::api('video', 'video')->getVideoLight($parameter, $type);
 * Pi::api('video', 'video')->getVideoJson($parameter, $type);
 * Pi::api('video', 'video')->getListFromId($id);
 * Pi::api('video', 'video')->attributeCount($id);
 * Pi::api('video', 'video')->canonizeVideo($video);
 */

class Video extends AbstractApi
{
    public function getVideo($parameter, $type = 'id')
    {
        // Get video
        $video = Pi::model('video', $this->getModule())->find($parameter, $type);
        $video = $this->canonizeVideo($video);
        return $video;
    }

    public function getVideoLight($parameter, $type = 'id')
    {
        // Get video
        $video = Pi::model('video', $this->getModule())->find($parameter, $type);
        $video = $this->canonizeVideoLight($video);
        return $video;
    }

    public function getVideoJson($parameter, $type = 'id')
    {
        // Get video
        $video = Pi::model('video', $this->getModule())->find($parameter, $type);
        $video = $this->canonizeVideoJson($video);
        return $video;
    }

    public function getListFromId($id)
    {
        $list = array();
        $where = array('id' => $id, 'status' => 1);
        $select = Pi::model('video', $this->getModule())->select()->where($where);
        $rowset = Pi::model('video', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $list[$row->id] = $this->canonizeVideo($row);
        }
        return $list;
    }

    public function getListFromIdLight($id)
    {
        $list = array();
        $where = array('id' => $id, 'status' => 1);
        $select = Pi::model('video', $this->getModule())->select()->where($where);
        $rowset = Pi::model('video', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $list[$row->id] = $this->canonizeVideoLight($row);
        }
        return $list;
    }

    public function attributeCount($id)
    {
        // Get attach count
        $columns = array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)'));
        $select = Pi::model('field_data', $this->getModule())->select()->columns($columns);
        $count = Pi::model('field_data', $this->getModule())->selectWith($select)->current()->count;
        // Set attach count
        Pi::model('video', $this->getModule())->update(array('attribute' => $count), array('id' => $id));
    }

    public function videoDuration($secs)
    {
        if ($secs < 3600) {
            $times = array(60, 1);
            $time = '';
            for ($i = 0; $i < 2; $i++) {
                $tmp = floor($secs / $times[$i]);
                if ($tmp < 1) {
                    $tmp = '00';
                } elseif ($tmp < 10) {
                    $tmp = '0' . $tmp;
                }
                $time .= $tmp;
                if ($i < 1) {
                    $time .= ':';
                }
                $secs = $secs % $times[$i];
            }
        } else {
            $times = array(3600, 60, 1);
            $time = '';
            for ($i = 0; $i < 3; $i++) {
                $tmp = floor($secs / $times[$i]);
                if ($tmp < 1) {
                    $tmp = '00';
                } elseif ($tmp < 10) {
                    $tmp = '0' . $tmp;
                }
                $time .= $tmp;
                if ($i < 2) {
                    $time .= ':';
                }
                $secs = $secs % $times[$i];
            }
        }
        return $time;
    }

    public function canonizeVideo($video, $categoryList = array(), $serverList = array())
    {
        // Check
        if (empty($video)) {
            return '';
        }
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Get category list
        $categoryList = (empty($categoryList)) ? Pi::registry('categoryList', 'video')->read() : $categoryList;
        // Get server list
        $serverList = (empty($serverList)) ? Pi::registry('serverList', 'video')->read() : $serverList;
        // boject to array
        $video = $video->toArray();
        // Check title
        if  (empty($video['title'])) {
            $video['title'] = sprintf(__('Submitted video on %s'), _date($video['time_create']));
        }
        // Set server information
        $video['server'] = $serverList[$video['video_server']];
        // Make setting
        $video['setting'] = json::decode($video['setting'], true);;
        // Set text_summary
        $video['text_summary'] = Pi::service('markup')->render($video['text_summary'], 'html', 'html');
        // Set text_description
        $video['text_description'] = Pi::service('markup')->render($video['text_description'], 'html', 'html');
        // Set times
        $video['time_create_view'] = _date($video['time_create']);
        $video['time_update_view'] = _date($video['time_update']);
        // Set video url
        $video['videoUrl'] = Pi::url(Pi::service('url')->assemble('video', array(
            'module' => $this->getModule(),
            'controller' => 'watch',
            'slug' => $video['slug'],
        )));
        // Set channel url
        $video['channelUrl'] = Pi::url(Pi::service('url')->assemble('video', array(
            'module' => $this->getModule(),
            'controller' => 'channel',
            'id' => $video['uid'],
        )));
        // Set local server path
        $video['localFilePath'] = Pi::path(sprintf('%s/%s',
            $video['video_path'],
            $video['video_file']
        ));
        // Set video file url
        switch ($video['server']['type']) {
            case 'file':
                $video['videoFileUrl'] = sprintf('%s/%s/%s',
                    $video['server']['url'],
                    $video['video_path'],
                    $video['video_file']
                );
                break;

            case 'qmery':
                $video['qmeryIframe'] = sprintf('%s/v/%s',
                    $video['server']['url'],
                    $video['video_qmery_hash']
                );
                $video['qmeryScript'] = sprintf('%s/embed.js?video=%s&w=640&h=360',
                    $video['server']['url'],
                    $video['video_qmery_hash']
                );
                $video['qmeryScriptResponsive'] = sprintf('%s/embed.js?video=%s',
                    $video['server']['url'],
                    $video['video_qmery_hash']
                );
                $video['qmeryDirect'] = sprintf('%s/v/%s',
                    $video['server']['url'],
                    $video['video_qmery_hash']
                );
                $video['qmeryJson'] = sprintf('%s/video/%s.json',
                    $video['server']['url'],
                    $video['video_qmery_hash']
                );
                break;

            case 'wowza':
                $video['videoFileUrl'] = sprintf('%s/%s/%s',
                    $video['server']['url'],
                    $video['video_path'],
                    $video['video_file']
                );

                $video['mpegDashUrl'] = sprintf('http://%s/%s/%s/manifest.mpd',
                    $video['server']['url'],
                    $video['video_path'],
                    $video['video_file']
                );

                $video['adobeHdsUrl'] = sprintf('http://%s/%s/%s/manifest.f4m',
                    $video['server']['url'],
                    $video['video_path'],
                    $video['video_file']
                );

                $video['jwplayerUrl'] = sprintf('http://%s/%s/%s/jwplayer.mpd',
                    $video['server']['url'],
                    $video['video_path'],
                    $video['video_file']
                );

                $video['iosUrl'] = sprintf('http://%s/%s/%s/playlist.m3u8',
                    $video['server']['url'],
                    $video['video_path'],
                    $video['video_file']
                );

                $video['androidUrl'] = sprintf('rtsp://%s/%s/%s',
                    $video['server']['url'],
                    $video['video_path'],
                    $video['video_file']
                );

                $video['rtspUrl'] = sprintf('rtsp://%s/%s/%s',
                    $video['server']['url'],
                    $video['video_path'],
                    $video['video_file']
                );

                $video['rtmpUrl'] = sprintf('rtmp://%s/%s/%s',
                    $video['server']['url'],
                    $video['video_path'],
                    $video['video_file']
                );
                break;
        }
        // Set video duration
        $video['video_duration_view'] = $this->videoDuration($video['video_duration']);
        // Set category information
        $video['category'] = Json::decode($video['category']);
        foreach ($video['category'] as $category) {
            if (!empty($categoryList[$category]['title'])) {
                $video['categories'][$category]['title'] = $categoryList[$category]['title'];
                $video['categories'][$category]['url'] = Pi::url(Pi::service('url')->assemble('video', array(
                    'module' => $this->getModule(),
                    'controller' => 'category',
                    'slug' => $categoryList[$category]['slug'],
                )));
            }
        }
        // Set image url
        if ($video['image']) {
            // Set image original url
            $video['originalUrl'] = Pi::url(
                sprintf('upload/%s/original/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                ));
            // Set image large url
            $video['largeUrl'] = Pi::url(
                sprintf('upload/%s/large/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                ));
            // Set image medium url
            $video['mediumUrl'] = Pi::url(
                sprintf('upload/%s/medium/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                ));
            // Set image thumb url
            $video['thumbUrl'] = Pi::url(
                sprintf('upload/%s/thumb/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                ));
        }
        // return video
        return $video;
    }

    public function canonizeVideoLight($video)
    {
        // Check
        if (empty($video)) {
            return '';
        }
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Get category list
        // $categoryList = (empty($categoryList)) ? Pi::registry('categoryList', 'video')->read() : $categoryList;
        // Get server list
        $serverList = (empty($serverList)) ? Pi::registry('serverList', 'video')->read() : $serverList;
        // Set server information
        $video['server'] = $serverList[$video['video_server']];
        // boject to array
        $video = $video->toArray();
        // Set times
        $video['time_create_view'] = _date($video['time_create']);
        $video['time_update_view'] = _date($video['time_update']);
        // Set video url
        $video['videoUrl'] = Pi::url(Pi::service('url')->assemble('video', array(
            'module' => $this->getModule(),
            'controller' => 'watch',
            'slug' => $video['slug'],
        )));
        // Set channel url
        $video['channelUrl'] = Pi::url(Pi::service('url')->assemble('video', array(
            'module' => $this->getModule(),
            'controller' => 'channel',
            'id' => $video['uid'],
        )));
        // Set image url
        if ($video['image']) {
            // Set image thumb url
            $video['thumbUrl'] = Pi::url(
                sprintf('upload/%s/thumb/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                ));
        }
        // unset
        unset($video['text_summary']);
        unset($video['text_description']);
        unset($video['seo_title']);
        unset($video['seo_keywords']);
        unset($video['seo_description']);
        unset($video['comment']);
        unset($video['point']);
        unset($video['count']);
        unset($video['favorite']);
        unset($video['attribute']);
        unset($video['recommended']);
        unset($video['uid']);
        unset($video['hits']);
        // return video
        return $video;
    }

    public function canonizeVideoJson($video, $categoryList = array(), $serverList = array())
    {
        // Check
        if (empty($video)) {
            return '';
        }
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Get category list
        $categoryList = (empty($categoryList)) ? Pi::registry('categoryList', 'video')->read() : $categoryList;
        // Get server list
        $serverList = (empty($serverList)) ? Pi::registry('serverList', 'video')->read() : $serverList;
        // Set server information
        $video['server'] = $serverList[$video['video_server']];
        // boject to array
        $video = $video->toArray();
        // Make setting
        $video['setting'] = json::decode($video['setting'], true);
        // Set text_summary
        $video['text_summary'] = Pi::service('markup')->render($video['text_summary'], 'html', 'html');
        // Set text_description
        $video['text_description'] = Pi::service('markup')->render($video['text_description'], 'html', 'html');
        // Set body
        $video['body'] = $video['text_summary'] . $video['text_description'];
        unset($video['text_summary']);
        unset($video['text_description']);
        // Set times
        $video['time_create_view'] = _date($video['time_create']);
        $video['time_update_view'] = _date($video['time_update']);
        // Set video url
        $video['videoUrl'] = Pi::url(Pi::service('url')->assemble('video', array(
            'module' => $this->getModule(),
            'controller' => 'watch',
            'slug' => $video['slug'],
        )));
        // Set channel url
        $video['channelUrl'] = Pi::url(Pi::service('url')->assemble('video', array(
            'module' => $this->getModule(),
            'controller' => 'channel',
            'id' => $video['uid'],
        )));
        // Set video file url
        switch ($video['server']['type']) {
            case 'file':
                $video['videoFileUrl'] = sprintf('%s/%s/%s',
                    $video['server']['url'],
                    $video['video_path'],
                    $video['video_file']
                );
                break;

            case 'qmery':
                $video['qmeryIframe'] = sprintf('%s/v/%s',
                    $video['server']['url'],
                    $video['video_qmery_hash']
                );
                $video['qmeryScript'] = sprintf('%s/embed.js?video=%s&w=640&h=360',
                    $video['server']['url'],
                    $video['video_qmery_hash']
                );
                $video['qmeryScriptResponsive'] = sprintf('%s/embed.js?video=%s',
                    $video['server']['url'],
                    $video['video_qmery_hash']
                );
                $video['qmeryDirect'] = sprintf('%s/v/%s',
                    $video['server']['url'],
                    $video['video_qmery_hash']
                );
                $video['qmeryJson'] = sprintf('%s/video/%s.json',
                    $video['server']['url'],
                    $video['video_qmery_hash']
                );
                break;

            case 'wowza':
                $video['videoFileUrl'] = sprintf('%s/%s/%s',
                    $video['server']['url'],
                    $video['video_path'],
                    $video['video_file']
                );

                $video['mpegDashUrl'] = sprintf('http://%s/%s/%s/manifest.mpd',
                    $video['server']['url'],
                    $video['video_path'],
                    $video['video_file']
                );

                $video['adobeHdsUrl'] = sprintf('http://%s/%s/%s/manifest.f4m',
                    $video['server']['url'],
                    $video['video_path'],
                    $video['video_file']
                );

                $video['jwplayerUrl'] = sprintf('http://%s/%s/%s/jwplayer.mpd',
                    $video['server']['url'],
                    $video['video_path'],
                    $video['video_file']
                );

                $video['iosUrl'] = sprintf('http://%s/%s/%s/playlist.m3u8',
                    $video['server']['url'],
                    $video['video_path'],
                    $video['video_file']
                );

                $video['androidUrl'] = sprintf('rtsp://%s/%s/%s',
                    $video['server']['url'],
                    $video['video_path'],
                    $video['video_file']
                );

                $video['rtspUrl'] = sprintf('rtsp://%s/%s/%s',
                    $video['server']['url'],
                    $video['video_path'],
                    $video['video_file']
                );

                $video['rtmpUrl'] = sprintf('rtmp://%s/%s/%s',
                    $video['server']['url'],
                    $video['video_path'],
                    $video['video_file']
                );
                break;
        }
        // Set video duration
        $video['video_duration_view'] = $this->videoDuration($video['video_duration']);
        // Set category information
        $video['category'] = Json::decode($video['category']);
        foreach ($video['category'] as $category) {
            if (!empty($categoryList[$category]['title'])) {
                $video['categories'][$category]['title'] = $categoryList[$category]['title'];
                $video['categories'][$category]['url'] = Pi::url(Pi::service('url')->assemble('video', array(
                    'module' => $this->getModule(),
                    'controller' => 'category',
                    'slug' => $categoryList[$category]['slug'],
                )));
            }
        }
        // Set image url
        if ($video['image']) {
            // Set image original url
            $video['originalUrl'] = Pi::url(
                sprintf('upload/%s/original/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                ));
            // Set image large url
            $video['largeUrl'] = Pi::url(
                sprintf('upload/%s/large/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                ));
            // Set image medium url
            $video['mediumUrl'] = Pi::url(
                sprintf('upload/%s/medium/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                ));
            // Set image thumb url
            $video['thumbUrl'] = Pi::url(
                sprintf('upload/%s/thumb/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                ));
        }
        // Set category_main information
        $video['categoryMainTitle'] = $categoryList[$video['category_main']]['title'];
        // Set attribute
        if ($video['attribute'] && $config['view_attribute']) {
            $attributes = Pi::api('attribute', 'video')->Video($video['id']);
            //$videoSingle['attributes'] = $attributes['all'];
            foreach ($attributes['all'] as $attribute) {
                $video['attribute-' . $attribute['id']] = $attribute['data'];
            }
        }
        // return video
        return $video;
    }

    public function canonizeVideoFilter($video, $categoryList = array(), $filterList = array())
    {
        // Check
        if (empty($video)) {
            return '';
        }
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Get category list
        $categoryList = (empty($categoryList)) ? Pi::registry('categoryList', 'video')->read() : $categoryList;
        // Get server list
        $serverList = (empty($serverList)) ? Pi::registry('serverList', 'video')->read() : $serverList;
        // Set server information
        $video['server'] = $serverList[$video['video_server']];
        // boject to array
        $video = $video->toArray();
        // Make setting
        // $video['setting'] = json::decode($video['setting'], true);
        // Set text_summary
        // $video['text_summary'] = Pi::service('markup')->render($video['text_summary'], 'html', 'html');
        // Set text_description
        // $video['text_description'] = Pi::service('markup')->render($video['text_description'], 'html', 'html');
        // Set times
        $video['time_create_view'] = _date($video['time_create']);
        $video['time_update_view'] = _date($video['time_update']);
        // Set video url
        $video['videoUrl'] = Pi::url(Pi::service('url')->assemble('video', array(
            'module' => $this->getModule(),
            'controller' => 'watch',
            'slug' => $video['slug'],
        )));
        // Set channel url
        $video['channelUrl'] = Pi::url(Pi::service('url')->assemble('video', array(
            'module' => $this->getModule(),
            'controller' => 'channel',
            'id' => $video['uid'],
        )));
        // Set category information
        if (isset($video['category']) && !empty($video['category'])) {
            $video['category'] = Json::decode($video['category']);
            foreach ($video['category'] as $category) {
                $video['categories'][$category]['id'] = $categoryList[$category]['id'];
                $video['categories'][$category]['title'] = $categoryList[$category]['title'];
                $video['categories'][$category]['url'] = Pi::url(Pi::service('url')->assemble('video', array(
                    'module' => $this->getModule(),
                    'controller' => 'category',
                    'slug' => $categoryList[$category]['slug'],
                )));
            }
        }
        // Set image url
        if ($video['image']) {
            // Set image original url
            $video['originalUrl'] = Pi::url(
                sprintf('upload/%s/original/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                ));
            // Set image large url
            $video['largeUrl'] = Pi::url(
                sprintf('upload/%s/large/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                ));
            // Set image medium url
            $video['mediumUrl'] = Pi::url(
                sprintf('upload/%s/medium/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                ));
            // Set image thumb url
            $video['thumbUrl'] = Pi::url(
                sprintf('upload/%s/thumb/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                ));
        }
        // Set attribute
        $filterList = isset($filterList) ? $filterList : Pi::api('attribute', 'video')->filterList();
        $attribute = Pi::api('attribute', 'video')->filterData($video['id'], $filterList);
        $video = array_merge($video, $attribute);

        // unset
        unset($video['text_summary']);
        unset($video['text_description']);
        unset($video['seo_title']);
        unset($video['seo_keywords']);
        unset($video['seo_description']);
        unset($video['point']);
        unset($video['count']);
        unset($video['attribute']);
        unset($video['recommended']);
        unset($video['uid']);
        unset($video['setting']);
        // return video
        return $video;
    }

    public function sitemap()
    {
        if (Pi::service('module')->isActive('sitemap')) {
            // Remove old links
            Pi::api('sitemap', 'sitemap')->removeAll($this->getModule(), 'video');
            // find and import
            $columns = array('id', 'slug', 'status');
            $select = Pi::model('video', $this->getModule())->select()->columns($columns);
            $rowset = Pi::model('video', $this->getModule())->selectWith($select);
            foreach ($rowset as $row) {
                // Make url
                $loc = Pi::url(Pi::service('url')->assemble('video', array(
                    'module' => $this->getModule(),
                    'controller' => 'watch',
                    'slug' => $row->slug,
                )));
                // Add to sitemap
                Pi::api('sitemap', 'sitemap')->groupLink($loc, $row->status, $this->getModule(), 'video', $row->id);
            }
        }
    }
}