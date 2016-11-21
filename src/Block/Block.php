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
namespace Module\Video\Block;

use Pi;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Json\Json;

class Block
{
    public static function videoNew($options = array(), $module = null)
    {
        // Set options
        $block = array();
        $block = array_merge($block, $options);
        $video = array();
        // Set info
        $order = array('time_create DESC', 'id DESC');
        $limit = intval($block['number']);
        if (isset($block['category']) &&
            !empty($block['category']) &&
            !in_array(0, $block['category'])
        ) {
            // Set info
            $where = array(
                'status' => 1,
                'category' => $block['category'],
            );
            // Set info
            $columns = array('video' => new Expression('DISTINCT video'));
            // Get info from link table
            $select = Pi::model('link', $module)->select()->where($where)->columns($columns)->order($order)->limit($limit);
            $rowset = Pi::model('link', $module)->selectWith($select)->toArray();
            // Make list
            foreach ($rowset as $id) {
                $videoId[] = $id['video'];
            }
            // Set info
            $where = array('status' => 1, 'id' => $videoId);
        } else {
            $where = array('status' => 1);
        }
        // Get list of video
        $select = Pi::model('video', $module)->select()->where($where)->order($order)->limit($limit);
        $rowset = Pi::model('video', $module)->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $video[$row->id] = Pi::api('video', 'video')->canonizeVideo($row);
        }
        // Set block array
        $block['resources'] = $video;
        return $block;
    }

    public static function videoRandom($options = array(), $module = null)
    {
        // Set options
        $block = array();
        $block = array_merge($block, $options);
        $video = array();
        // Set info
        $order = array(new Expression('RAND()'));
        $limit = intval($block['number']);
        if (isset($block['category']) &&
            !empty($block['category']) &&
            !in_array(0, $block['category'])
        ) {
            // Set info
            $where = array(
                'status' => 1,
                'category' => $block['category'],
            );
            // Set info
            $columns = array('video' => new Expression('DISTINCT video'));
            // Get info from link table
            $select = Pi::model('link', $module)->select()->where($where)->columns($columns)->order($order)->limit($limit);
            $rowset = Pi::model('link', $module)->selectWith($select)->toArray();
            // Make list
            foreach ($rowset as $id) {
                $videoId[] = $id['video'];
            }
            // Set info
            $where = array('status' => 1, 'id' => $videoId);
        } else {
            $where = array('status' => 1);
        }
        // Get list of video
        $select = Pi::model('video', $module)->select()->where($where)->order($order)->limit($limit);
        $rowset = Pi::model('video', $module)->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $video[$row->id] = Pi::api('video', 'video')->canonizeVideo($row);
        }
        // Set block array
        $block['resources'] = $video;
        return $block;
    }

    public static function videoTag($options = array(), $module = null)
    {
        // Set options
        $block = array();
        $block = array_merge($block, $options);
        $video = array();
        // Check tag term
        if (!empty($block['tag-term'])) {
            // Get video ides from tag term
            $tags = Pi::service('tag')->getList($block['tag-term'], 'video');
            foreach ($tags as $tag) {
                $tagId[] = $tag['item'];
            }
            // get videos
            if (!empty($tagId)) {
                // Set info
                $where = array('status' => 1, 'id' => $tagId);
                $order = array(new Expression('RAND()'));
                $limit = intval($block['number']);
                // Get list of video
                $select = Pi::model('video', $module)->select()->where($where)->order($order)->limit($limit);
                $rowset = Pi::model('video', $module)->selectWith($select);
                // Make list
                foreach ($rowset as $row) {
                    $video[$row->id] = Pi::api('video', 'video')->canonizeVideo($row);
                }
            }
        }
        // Set block array
        $block['resources'] = $video;
        // Load language
        Pi::service('i18n')->load(array('module/video', 'default'));
        return $block;
    }

    public static function videoHits($options = array(), $module = null)
    {
        // Set options
        $block = array();
        $block = array_merge($block, $options);
        $video = array();
        // Set day
        $time = time() - (60 * 60 * 24 * $block['day']);
        // Set info
        $order = array('hits DESC', 'time_update DESC' , 'id DESC');
        $limit = intval($block['number']);
        if (isset($block['category']) &&
            !empty($block['category']) &&
            !in_array(0, $block['category'])
        ) {
            // Set info
            $where = array(
                'status' => 1,
                'category' => $block['category'],
                'time_update >= ?' => $time,
            );
            // Set info
            $columns = array('video' => new Expression('DISTINCT video'));
            // Get info from link table
            $select = Pi::model('link', $module)->select()->where($where)->columns($columns)->order($order)->limit($limit);
            $rowset = Pi::model('link', $module)->selectWith($select)->toArray();
            // Make list
            foreach ($rowset as $id) {
                $videoId[] = $id['video'];
            }
            // Set info
            $where = array('status' => 1, 'id' => $videoId);
        } else {
            $where = array('status' => 1, 'time_update >= ?' => $time,);
        }
        // Get list of video
        $select = Pi::model('video', $module)->select()->where($where)->order($order)->limit($limit);
        $rowset = Pi::model('video', $module)->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $video[$row->id] = Pi::api('video', 'video')->canonizeVideo($row);
        }
        // Set block array
        $block['resources'] = $video;
        return $block;
    }

    public static function videoSelect($options = array(), $module = null)
    {
        // Set options
        $block = array();
        $block = array_merge($block, $options);
        // Get video
        if (isset($block['vid']) && intval($block['vid']) > 0) {
            $video = Pi::api('video', 'video')->getVideo(intval($block['vid']));
        } else {
            return false;
        }
        // Set player url
        switch ($block['size']) {
            case 'responsive':
                $video['playerUrl'] = $video['qmeryScriptResponsive'];
                break;

            case 'custom':
                $video['playerUrl'] = sprintf('%s&w=%s&h=%s',
                    $video['qmeryScriptResponsive'],
                    $block['width'],
                    $block['height']);
                break;
        }
        // Set block array
        $block['resources'] = $video;
        return $block;
    }
}