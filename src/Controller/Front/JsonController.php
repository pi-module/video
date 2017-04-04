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
    public function searchAction()
    {
        // Get info from url
        $module = $this->params('module');
        $page = $this->params('page', 1);
        $title = $this->params('title');
        $category = $this->params('category');
        $tag = $this->params('tag');
        $favourite = $this->params('favourite');
        $recommended = $this->params('recommended');
        $limit = $this->params('limit');
        $order = $this->params('order');
        $channel = $this->params('channel');

        // Set has search result
        $hasSearchResult = true;

        // Clean title
        if (Pi::service('module')->isActive('search') && isset($title) && !empty($title)) {
            $title = Pi::api('api', 'search')->parseQuery(urldecode($title));
        } elseif (isset($title) && !empty($title)) {
            $title = _strip(urldecode($title));
        } else {
            $title = '';
        }

        // Clean params
        $paramsClean = array();
        foreach ($_GET as $key => $value) {
            $key = _strip(urldecode($key));
            $value = _strip(urldecode($value));
            $paramsClean[$key] = $value;
        }

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Set empty result
        $result = array(
            'videos' => array(),
            'filterList' => array(),
            'paginator' => array(),
            'condition' => array(),
        );

        // Set where link
        $whereLink = array('status' => 1);
        if (!empty($recommended) && $recommended == 1) {
            $whereLink['recommended'] = 1;
        }

        // Set page title
        $pageTitle = __('List of videos');

        // Set order
        switch ($order) {
            case 'title':
                $order = array('title DESC', 'id DESC');
                break;

            case 'titleASC':
                $order = array('title ASC', 'id ASC');
                break;

            case 'hits':
                $order = array('hits DESC', 'id DESC');
                break;

            case 'hitsASC':
                $order = array('hits ASC', 'id ASC');
                break;

            case 'create':
                $order = array('time_create DESC', 'id DESC');
                break;

            case 'createASC':
                $order = array('time_create ASC', 'id ASC');
                break;

            case 'update':
                $order = array('time_update DESC', 'id DESC');
                break;

            case 'recommended':
                $order = array('recommended DESC', 'time_create DESC', 'id DESC');
                break;

            default:
                $order = array('time_create DESC', 'id DESC');
                break;
        }

        // Get category information from model
        if (!empty($category)) {
            // Get category
            $category = Pi::api('category', 'video')->getCategory(urldecode($category), 'slug');
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
            $pageTitle = sprintf(__('List of videos on %s category'), $category['title']);
        }

        // Get tag list
        if (!empty($tag)) {
            $videoIDTag = array();
            // Check favourite
            if (!Pi::service('module')->isActive('tag')) {
                return $result;
            }
            // Get id from tag module
            $tagList = Pi::service('tag')->getList(urldecode($tag), $module);
            foreach ($tagList as $tagSingle) {
                $videoIDTag[] = $tagSingle['item'];
            }
            // Set header and title
            $pageTitle = sprintf(__('All videos from %s'), $tag);
        }

        // Get favourite list
        if (!empty($favourite) && $favourite == 1) {
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
            $columns = array('id');
            $select = $this->getModel('video')->select()->columns($columns)->where(function ($where) use ($titles, $recommended) {
                $whereMain = clone $where;
                $whereKey = clone $where;
                $whereMain->equalTo('status', 1);
                if (!empty($recommended) && $recommended == 1) {
                    $whereMain->equalTo('recommended', 1);
                }
                foreach ($titles as $title) {
                    $whereKey->like('title', '%' . $title . '%')->and;
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
        $count = 0;

        $columns = array('video' => new Expression('DISTINCT video'));
        $offset = (int)($page - 1) * $config['view_perpage'];
        $limit = (intval($limit) > 0) ? intval($limit) : intval($config['view_perpage']);

        // Set category on where link
        if (isset($categoryIDList) && !empty($categoryIDList)) {
            $whereLink['category'] = $categoryIDList;
        }

        // Set video on where link from title and attribute
        if ($checkTitle && $checkAttribute) {
            if (!empty($videoIDList['title']) && !empty($videoIDList['attribute'])) {
                $whereLink['video'] = array_intersect($videoIDList['title'], $videoIDList['attribute']);
            } else {
                $hasSearchResult = false;
            }
        } elseif ($checkTitle) {
            if (!empty($videoIDList['title'])) {
                $whereLink['video'] = $videoIDList['title'];
            } else {
                $hasSearchResult = false;
            }
        } elseif ($checkAttribute) {
            if (!empty($videoIDList['attribute'])) {
                $whereLink['video'] = $videoIDList['attribute'];
            } else {
                $hasSearchResult = false;
            }
        }

        // Set favourite videos on where link
        if (!empty($favourite) && $favourite == 1 && isset($videoIDFavourite)) {
            if (isset($whereLink['video']) && !empty($whereLink['video'])) {
                $whereLink['video'] = array_intersect($videoIDFavourite, $whereLink['video']);
            } elseif (!isset($whereLink['video']) || empty($whereLink['video'])) {
                $whereLink['video'] = $videoIDFavourite;
            } else {
                $hasSearchResult = false;
            }
        }

        // Set tag videos on where link
        if (!empty($tag) && isset($videoIDTag)) {
            if (isset($whereLink['video']) && !empty($whereLink['video'])) {
                $whereLink['video'] = array_intersect($videoIDTag, $whereLink['video']);
            } elseif (!isset($whereLink['video']) || empty($whereLink['video'])) {
                $whereLink['video'] = $videoIDTag;
            } else {
                $hasSearchResult = false;
            }
        }

        // Check has Search Result
        if ($hasSearchResult) {
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
        }

        // Set result
        $result = array(
            'videos' => $video,
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