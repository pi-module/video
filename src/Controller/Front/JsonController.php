<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\Video\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Zend\Json\Json;
use Zend\Db\Sql\Predicate\Expression;

class JsonController extends IndexController
{
    public function indexAction()
    {
        // Set return
        $return = array(
            'website' => Pi::url(),
            'module' => $this->params('module'),
        );
        // Set view
        return $return;
    }

    public function videoListAction()
    {
        // Check password
        if (!$this->checkPassword()) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('Password not set or wrong'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get info from url
        $module = $this->params('module');
        $type = $this->params('type');
        $limit = $this->params('limit');
        $id = $this->params('id');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get category list
        $categoryList = Pi::registry('categoryList', 'video')->read();
        // Get server list
        $serverList = Pi::registry('serverList', 'video')->read();
        // Set info
        $video = array();
        $where = array('status' => 1);
        $limit = (!empty($limit)) ? $limit : $config['json_perpage'];
        $order = array('time_create DESC', 'id DESC');
        // Set type
        switch ($type) {
            default:
            case 'latest':
                // Get list of video
                $select = $this->getModel('video')->select()->where($where)->order($order)->limit($limit);
                $rowset = $this->getModel('video')->selectWith($select);
                break;

            case 'recommended':
                // Set info
                $where['recommended'] = 1;
                // Get list of video
                $select = $this->getModel('video')->select()->where($where)->order($order)->limit($limit);
                $rowset = $this->getModel('video')->selectWith($select);
                break;

            case 'hit':
                // Set info
                $order = array('hits DESC', 'time_create DESC', 'id DESC');
                // Get list of video
                $select = $this->getModel('video')->select()->where($where)->order($order)->limit($limit);
                $rowset = $this->getModel('video')->selectWith($select);
                break;

            case 'category':
                // Set info
                $where['category'] = $id;
                $columns = array('video' => new Expression('DISTINCT video'));
                // Get info from link table
                $select = $this->getModel('link')->select()->where($where)->columns($columns)->order($order);
                $rowset = $this->getModel('link')->selectWith($select)->toArray();
                // Make list
                foreach ($rowset as $id) {
                    $videoId[] = $id['video'];
                }
                if (empty($videoId)) {
                    return $video;
                }
                // Set info
                $where = array('status' => 1, 'id' => $videoId);
                // Get list of video
                $select = $this->getModel('video')->select()->where($where)->order($order);
                $rowset = $this->getModel('video')->selectWith($select);
                break;
        }
        // canonize video
        foreach ($rowset as $row) {
            $singleVideo = Pi::api('video', 'video')->canonizeVideoJson($row, $categoryList, $serverList);
            $video[] = array(
                'id' => $singleVideo['id'],
                'title' => $singleVideo['title'],
                'slug' => $singleVideo['slug'],
                'time_create' => $singleVideo['time_create'],
                'time_create_view' => $singleVideo['time_create_view'],
                'videoUrl' => $singleVideo['videoUrl'],
                'largeUrl' => $singleVideo['largeUrl'],
            );
        }
        // Set view
        return $video;
    }

    public function videoSingleAction()
    {
        // Check password
        if (!$this->checkPassword()) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('Password not set or wrong'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get info from url
        $module = $this->params('module');
        $id = $this->params('id');
        $slug = $this->params('slug');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get
        if (!empty($slug)) {
            $singleVideo = Pi::api('video', 'video')->getVideoJson($slug, 'slug');
        } elseif (!empty($id)) {
            $singleVideo = Pi::api('video', 'video')->getVideoJson($id);
        } else {
            return false;
        }
        // Set video
        $video = array();
        $video[] = array(
            'id' => $singleVideo['id'],
            'title' => $singleVideo['title'],
            'slug' => $singleVideo['slug'],
            'time_create' => $singleVideo['time_create'],
            'time_create_view' => $singleVideo['time_create_view'],
            'categories' => $singleVideo['categories'],
            'hits' => $singleVideo['hits'],
            'recommended' => $singleVideo['recommended'],
            'favourite' => $singleVideo['favourite'],
            'video_duration_view' => $singleVideo['video_duration_view'],
            'body' => $singleVideo['body'],
            'channelUrl' => $singleVideo['channelUrl'],
            'videoUrl' => $singleVideo['videoUrl'],
            'largeUrl' => $singleVideo['largeUrl'],
            'qmeryDirect' => $singleVideo['qmeryDirect'],
            'qmeryScript' => $singleVideo['qmeryScript'],
            'video_qmery_id' => $singleVideo['video_qmery_id'],
            'video_qmery_hash' => $singleVideo['video_qmery_hash'],
        );
        return $video;
    }

    public function filterIndexAction()
    {
        // Get info from url
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get search form
        $filterList = Pi::api('attribute', 'video')->filterList();
        $categoryList = Pi::registry('categoryList', 'video')->read();
        // Set info
        $video = array();
        $where = array(
            'status' => 1,
        );
        $order = array('time_create DESC', 'id DESC');
        $columns = array('video' => new Expression('DISTINCT video'));
        // Get info from link table
        $select = $this->getModel('link')->select()->where($where)->columns($columns)->order($order);
        $rowset = $this->getModel('link')->selectWith($select)->toArray();
        // Make list
        foreach ($rowset as $id) {
            $videoId[] = $id['video'];
        }
        if (empty($videoId)) {
            return $video;
        }
        // Set info
        $where = array('status' => 1, 'id' => $videoId);
        // Get list of video
        $select = $this->getModel('video')->select()->where($where)->order($order);
        $rowset = $this->getModel('video')->selectWith($select);
        foreach ($rowset as $row) {
            $video[] = Pi::api('video', 'video')->canonizeVideoFilter($row, $categoryList, $filterList);
        }
        // Set view
        return $video;
    }

    public function filterCategoryAction()
    {
        // Get info from url
        $module = $this->params('module');
        $slug = $this->params('slug');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get category information from model
        $category = $this->getModel('category')->find($slug, 'slug');
        $category = Pi::api('category', 'video')->canonizeCategory($category, 'compact');
        // Check category
        if (!$category || $category['status'] != 1) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The category not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get search form
        $filterList = Pi::api('attribute', 'video')->filterList();
        $categoryList = Pi::registry('categoryList', 'video')->read();
        // category list
        $categories = Pi::api('category', 'video')->categoryList($category['id']);
        // Get id list
        $idList = array();
        $idList[] = $category['id'];
        foreach ($categories as $singleCategory) {
            $idList[] = $singleCategory['id'];
        }
        // Set info
        $video = array();
        $where = array(
            'status' => 1,
            'category' => $idList,
        );
        $order = array('time_create DESC', 'id DESC');
        $columns = array('video' => new Expression('DISTINCT video'));
        // Get info from link table
        $select = $this->getModel('link')->select()->where($where)->columns($columns)->order($order);
        $rowset = $this->getModel('link')->selectWith($select)->toArray();
        // Make list
        foreach ($rowset as $id) {
            $videoId[] = $id['video'];
        }
        if (empty($videoId)) {
            return $video;
        }
        // Set info
        $where = array('status' => 1, 'id' => $videoId);
        // Get list of video
        $select = $this->getModel('video')->select()->where($where)->order($order);
        $rowset = $this->getModel('video')->selectWith($select);
        foreach ($rowset as $row) {
            $video[] = Pi::api('video', 'video')->canonizeVideoFilter($row, $categoryList, $filterList);
        }
        // Set view
        return $video;
    }

    public function filterTagAction()
    {
        // Check tag
        if (!Pi::service('module')->isActive('tag')) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Tag module not installed.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get info from url
        $module = $this->params('module');
        $slug = $this->params('slug');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Check slug
        if (!isset($slug) || empty($slug)) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The tag not set.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get id from tag module
        $tagId = array();
        $tags = Pi::service('tag')->getList($slug, $module);
        foreach ($tags as $tag) {
            $tagId[] = $tag['item'];
        }
        // Check slug
        if (empty($tagId)) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The tag not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get search form
        $filterList = Pi::api('attribute', 'video')->filterList();
        $categoryList = Pi::registry('categoryList', 'video')->read();
        // Set info
        $where = array('status' => 1, 'id' => $tagId);
        $order = array('time_create DESC', 'id DESC');
        // Get list of video
        $select = $this->getModel('video')->select()->where($where)->order($order);
        $rowset = $this->getModel('video')->selectWith($select);
        foreach ($rowset as $row) {
            $video[] = Pi::api('video', 'video')->canonizeVideoFilter($row, $categoryList, $filterList);
        }
        // Set view
        return $video;
    }

    public function filterFavouriteAction()
    {
        // Check tag
        if (!Pi::service('module')->isActive('favourite')) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Favourite module not installed.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get info from url
        $module = $this->params('module');
        $uid = Pi::user()->getId();
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Check user
        if (!$uid) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('Please login to see favourite list.'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get id from favourite module
        $id = Pi::api('favourite', 'favourite')->userFavourite($uid, $module);
        // Get search form
        $filterList = Pi::api('attribute', 'video')->filterList();
        $categoryList = Pi::registry('categoryList', 'video')->read();
        // Set info
        $where = array('status' => 1, 'id' => $id);
        $order = array('time_create DESC', 'id DESC');
        // Get list of video
        $select = $this->getModel('video')->select()->where($where)->order($order);
        $rowset = $this->getModel('video')->selectWith($select);
        foreach ($rowset as $row) {
            $video[] = Pi::api('video', 'video')->canonizeVideoFilter($row, $categoryList, $filterList);
        }
        // Set view
        return $video;
    }

    public function filterChannelAction()
    {
        // Get info from url
        $module = $this->params('module');
        $uid = $this->params('id');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get search form
        $filterList = Pi::api('attribute', 'video')->filterList();
        $categoryList = Pi::registry('categoryList', 'video')->read();
        // Set info
        $video = array();
        $where = array(
            'status' => 1,
            'uid' => $uid,
        );
        $order = array('time_create DESC', 'id DESC');
        $columns = array('video' => new Expression('DISTINCT video'));
        // Get info from link table
        $select = $this->getModel('link')->select()->where($where)->columns($columns)->order($order);
        $rowset = $this->getModel('link')->selectWith($select)->toArray();
        // Make list
        foreach ($rowset as $id) {
            $videoId[] = $id['video'];
        }
        if (empty($videoId)) {
            return $video;
        }
        // Set info
        $where = array('status' => 1, 'id' => $videoId);
        // Get list of video
        $select = $this->getModel('video')->select()->where($where)->order($order);
        $rowset = $this->getModel('video')->selectWith($select);
        foreach ($rowset as $row) {
            $video[] = Pi::api('video', 'video')->canonizeVideoFilter($row, $categoryList, $filterList);
        }
        // Set view
        return $video;
    }

    public function qmeryCallbackAction()
    {
        // Get info from url
        $module = $this->params('module');
        $id = $this->params('id');
        $password = $this->params('password');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Check password
        /* if ($config['json_check_password']) {
            if ($config['json_password'] != $password) {
                $this->getResponse()->setStatusCode(401);
                $this->terminate(__('Password not set or wrong'), '', 'error-denied');
                $this->view()->setLayout('layout-simple');
                return;
            }
        } else {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('Password not set or wrong'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        } */
        // Check post
        if ($this->request->isPost()) {
            //$data = $this->request->getPost();
            //$data = json_encode($data);

            Pi::model('video', $this->getModule())->update(
                array('setting' => array(1)),
                array('id' => $id)
            );

            return array(
                'message' => 'success',
                'status' => 1,
            );
        } else {
            Pi::model('video', $this->getModule())->update(
                array('setting' => array(2)),
                array('id' => $id)
            );

            return array(
                'message' => 'error',
                'status' => 0,
            );
        }
    }

    public function checkPassword() {
        // Get info from url
        $module = $this->params('module');
        $password = $this->params('password');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Check password
        if ($config['json_check_password']) {
            if ($config['json_password'] == $password) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}