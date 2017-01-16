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
use Zend\Db\Sql\Predicate\Expression;

class JsonController extends IndexController
{
    /* public function indexAction()
    {
        // Set return
        $return = array(
            'website' => Pi::url(),
            'module' => $this->params('module'),
        );
        // Set view
        return $return;
    } */

    public function searchAction()
    {
        // Get info from url
        $module = $this->params('module');
        $page = $this->params('page', 1);
        $category = $this->params('category');
        $tag = $this->params('tag');
        $channel = $this->params('channel');
        $favourite = $this->params('favourite');
        $title = $this->params('title');

        // Clean title
        if (Pi::service('module')->isActive('search') && isset($title) && !empty($title)) {
            $title = Pi::api('api', 'search')->parseQuery($title);
        } elseif (isset($title) && !empty($title)) {
            $title = _strip($title);
        } else {
            $title = '';
        }

        // Clean params
        $paramsClean = array();
        foreach ($_GET as $key => $value) {
            $key = _strip($key);
            $value = _strip($value);
            $paramsClean[$key] = $value;
        }

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Set empty result
        $result = array(
            'videos' => array(),
            'category' => array(),
            'filterList' => array(),
            'paginator' => array(),
            'condition' => array(),
        );

        // Set where link
        $whereLink = array('status' => 1);

        // Set page title
        $pageTitle = __('New videos');

        // Get category information from model
        if (!empty($category)) {
            // Get category
            $category = Pi::api('category', 'video')->getCategory($category, 'slug');
            // Check category
            if (!$category || $category['status'] != 1) {
                return $result;
            }
            // category list
            $categories = Pi::api('category', 'video')->categoryList($category['id']);
            // Get id list
            $categoryIDList = array();
            $categoryIDList[] = $category['id'];
            foreach ($categories as $singleCategory) {
                $categoryIDList[] = $singleCategory['id'];
            }
            // Set page title
            $pageTitle = sprintf(__('New videos from %s'), $category['title']);
        }

        // Get favourite list
        if (!empty($favourite)) {
            // Check favourite
            if (!Pi::service('module')->isActive('favourite')) {
                return $result;
            }
            // Get uid
            $uid = Pi::user()->getId();
            // Check user
            if (!$uid) {
                return $result;
            }
            // Get id from favourite module
            $videoIDFavourite = Pi::api('favourite', 'favourite')->userFavourite($uid, $module);
            // Set page title
            $pageTitle = ('All favourite videos by you');
        }

        // Get channel list
        if (!empty($channel)) {
            if (intval($channel) > 0) {
                // Get user id
                $user = Pi::api('channel', 'video')->user(intval($channel));
                $pageTitle = sprintf(__('All videos from %s channel'), $user['name']);
                // Set where link
                $whereLink['uid'] = intval($channel);
            } else {
                return $result;
            }
        }

        // Get search form
        $filterList = Pi::api('attribute', 'video')->filterList();
        $categoryList = Pi::registry('categoryList', 'video')->read();

        // Set video ID list
        $checkTitle = false;
        $checkAttribute = false;
        $videoIDList = array(
            'title' => array(),
            'attribute' => array(),
        );

        // Check title from video table
        if (isset($title) && !empty($title)) {
            $checkTitle = true;
            $titles = is_array($title) ? $title : array($title);
            $order = array('recommended DESC', 'time_create DESC', 'id DESC');
            $columns = array('id');
            $select = $this->getModel('video')->select()->columns($columns)->where(function ($where) use ($titles) {
                $whereMain = clone $where;
                $whereKey = clone $where;
                $whereMain->equalTo('status', 1);
                foreach ($titles as $title) {
                    $whereKey->like('title', '%' . $title . '%')->or;
                }
                $where->andPredicate($whereMain)->andPredicate($whereKey);
            })->order($order);
            $rowset = $this->getModel('video')->selectWith($select);
            foreach ($rowset as $row) {
                $videoIDList['title'][$row->id] = $row->id;
            }
        }

        // Check attribute
        if (!empty($paramsClean)) {
            // Make attribute list
            $attributeList = array();
            foreach ($filterList as $filterSingle) {
                if (isset($paramsClean[$filterSingle['name']]) && !empty($paramsClean[$filterSingle['name']])) {
                    $attributeList[$filterSingle['name']] = array(
                        'field' => $filterSingle['id'],
                        'data' => $paramsClean[$filterSingle['name']],
                    );
                }
            }
            // Search on attribute
            if (!empty($attributeList)) {
                $checkAttribute = true;
                $column = array('video');
                foreach ($attributeList as $attributeSingle) {
                    $where = array(
                        'field' => $attributeSingle['field'],
                        'data' => $attributeSingle['data'],
                    );
                    $select = $this->getModel('field_data')->select()->where($where)->columns($column);
                    $rowset = $this->getModel('field_data')->selectWith($select);
                    foreach ($rowset as $row) {
                        $videoIDList['attribute'][$row->video] = $row->video;
                    }
                }
            }
        }

        // Set info
        $video = array();
        $order = array('recommended DESC', 'time_create DESC', 'id DESC');
        $columns = array('video' => new Expression('DISTINCT video'));
        $offset = (int)($page - 1) * $config['view_perpage'];
        $limit = intval($config['view_perpage']);
        // Set where link
        if (isset($categoryIDList) && !empty($categoryIDList)) {
            $whereLink['category'] = $categoryIDList;
        }
        if ($checkTitle && $checkAttribute) {
            $id = array_intersect($videoIDList['title'], $videoIDList['attribute']);
            $whereLink['video'] = !empty($id) ? $id : '';
        } elseif ($checkTitle) {
            $whereLink['video'] = !empty($videoIDList['title']) ? $videoIDList['title'] : '';
        } elseif ($checkAttribute) {
            $whereLink['video'] = !empty($videoIDList['attribute']) ? $videoIDList['attribute'] : '';
        }
        if (isset($favourite)) {
            $whereLink['video'] = array_intersect($videoIDFavourite, $whereLink['video']);
        }

        // Get info from link table
        $select = $this->getModel('link')->select()->where($whereLink)->columns($columns)->order($order)->offset($offset)->limit($limit);
        $rowset = $this->getModel('link')->selectWith($select)->toArray();
        foreach ($rowset as $id) {
            $videoIDSelect[] = $id['video'];
        }

        // Get list of video
        if (!empty($videoIDSelect)) {
            $where = array('status' => 1, 'id' => $videoIDSelect);
            $select = $this->getModel('video')->select()->where($where)->order($order);
            $rowset = $this->getModel('video')->selectWith($select);
            foreach ($rowset as $row) {
                $video[] = Pi::api('video', 'video')->canonizeVideoFilter($row, $categoryList, $filterList);
            }
        }

        // Get count
        $columnsCount = array('count' => new Expression('count(DISTINCT `video`)'));
        $select = $this->getModel('link')->select()->where($whereLink)->columns($columnsCount);
        $count = $this->getModel('link')->selectWith($select)->current()->count;

        // Set result
        $result = array(
            //'paramsClean' => $paramsClean,
            //'whereLink' => $whereLink,
            //'categoryIDList' => $categoryIDList,
            //'videoIDList' => $videoIDList,

            'videos' => $video,
            'category' => $category,
            'tag' => $tag,
            'filterList' => $filterList,
            'paginator' => array(
                'count' => $count,
                'limit' => intval($config['view_perpage']),
                'page' => $page,
            ),
            'condition' => array(
                'title' => $pageTitle,
             ),
        );

        return $result;
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
        $id = $this->params('id', 0);
        $update = $this->params('update', 0);
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get category list
        $categoryList = Pi::registry('categoryList', 'video')->read();
        // Get server list
        $serverList = Pi::registry('serverList', 'video')->read();
        // Set info
        $video = array();
        $where = array('status' => 1);
        if ($update > 0) {
            $where['time_update > ?'] = $update;
        }
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
                'body' => '',
                'category' => $singleVideo['category_main'],
                'categoryMainTitle' => '',
                'image' => $singleVideo['image'],
                'recommended' => $singleVideo['recommended'],
                'time_create' => $singleVideo['time_create'],
                'time_create_view' => $singleVideo['time_create_view'],
                'time_update' => $singleVideo['time_create'],
                'time_update_view' => $singleVideo['time_create_view'],
                'videoUrl' => $singleVideo['videoUrl'],
                'largeUrl' => $singleVideo['largeUrl'],
                'qmeryDirect' => $singleVideo['qmeryDirect'],
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

    /* public function filterIndexAction()
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
        $order = array('recommended DESC', 'time_create DESC', 'id DESC');
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
    } */

    /* public function filterCategoryAction()
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
        $order = array('recommended DESC', 'time_create DESC', 'id DESC');
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
    } */

    /* public function filterTagAction()
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
        $order = array('recommended DESC', 'time_create DESC', 'id DESC');
        // Get list of video
        $select = $this->getModel('video')->select()->where($where)->order($order);
        $rowset = $this->getModel('video')->selectWith($select);
        foreach ($rowset as $row) {
            $video[] = Pi::api('video', 'video')->canonizeVideoFilter($row, $categoryList, $filterList);
        }
        // Set view
        return $video;
    } */

    /* public function filterFavouriteAction()
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
        $order = array('recommended DESC', 'time_create DESC', 'id DESC');
        // Get list of video
        $select = $this->getModel('video')->select()->where($where)->order($order);
        $rowset = $this->getModel('video')->selectWith($select);
        foreach ($rowset as $row) {
            $video[] = Pi::api('video', 'video')->canonizeVideoFilter($row, $categoryList, $filterList);
        }
        // Set view
        return $video;
    } */

    /* public function filterChannelAction()
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
        $order = array('recommended DESC', 'time_create DESC', 'id DESC');
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
    } */

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