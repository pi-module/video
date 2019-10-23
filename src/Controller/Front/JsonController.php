<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
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
        $module      = $this->params('module');
        $page        = $this->params('page', 1);
        $title       = $this->params('title');
        $category    = $this->params('category');
        $tag         = $this->params('tag');
        $favourite   = $this->params('favourite');
        $recommended = $this->params('recommended');
        $limit       = $this->params('limit');
        $order       = $this->params('order');
        $channel     = $this->params('channel');

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
        $paramsClean = [];
        foreach ($_GET as $key => $value) {
            $key               = _strip(urldecode($key));
            $value             = _strip(urldecode($value));
            $paramsClean[$key] = $value;
        }

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Set empty result
        $result = [
            'videos'     => [],
            'filterList' => [],
            'paginator'  => [],
            'condition'  => [],
        ];

        // Set where link
        $whereLink = ['status' => 1];
        if (!empty($recommended) && $recommended == 1) {
            $whereLink['recommended'] = 1;
        }

        // Set page title
        $pageTitle = __('List of videos');

        // Set order
        switch ($order) {
            case 'title':
                $order = ['title DESC', 'id DESC'];
                break;

            case 'titleASC':
                $order = ['title ASC', 'id ASC'];
                break;

            case 'hits':
                $order = ['hits DESC', 'id DESC'];
                break;

            case 'hitsASC':
                $order = ['hits ASC', 'id ASC'];
                break;

            case 'create':
                $order = ['time_create DESC', 'id DESC'];
                break;

            case 'createASC':
                $order = ['time_create ASC', 'id ASC'];
                break;

            case 'update':
                $order = ['time_update DESC', 'id DESC'];
                break;

            case 'recommended':
                $order = ['recommended DESC', 'time_create DESC', 'id DESC'];
                break;

            default:
                $order = ['time_create DESC', 'id DESC'];
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
            $categoryIDList   = [];
            $categoryIDList[] = $category['id'];
            foreach ($categories as $singleCategory) {
                $categoryIDList[] = $singleCategory['id'];
            }
            // Set page title
            $pageTitle = sprintf(__('List of videos on %s category'), $category['title']);
        }

        // Get tag list
        if (!empty($tag)) {
            $videoIDTag = [];
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
                $user      = Pi::api('channel', 'video')->user(intval($channel));
                $pageTitle = sprintf(__('All videos from %s channel'), $user['name']);
                // Set where link
                $whereLink['uid'] = intval($channel);
            } else {
                return $result;
            }
        }

        // Get search form
        $filterList   = Pi::api('attribute', 'video')->filterList();
        $categoryList = Pi::registry('categoryList', 'video')->read();

        // Set video ID list
        $checkTitle     = false;
        $checkAttribute = false;
        $videoIDList    = [
            'title'     => [],
            'attribute' => [],
        ];

        // Check title from video table
        if (isset($title) && !empty($title)) {
            $checkTitle = true;
            $titles     = is_array($title) ? $title : [$title];
            $columns    = ['id'];
            $select     = $this->getModel('video')->select()->columns($columns)->where(
                function ($where) use ($titles, $recommended) {
                    $whereMain = clone $where;
                    $whereKey  = clone $where;
                    $whereMain->equalTo('status', 1);
                    if (!empty($recommended) && $recommended == 1) {
                        $whereMain->equalTo('recommended', 1);
                    }
                    foreach ($titles as $title) {
                        $whereKey->like('title', '%' . $title . '%')->and;
                    }
                    $where->andPredicate($whereMain)->andPredicate($whereKey);
                }
            )->order($order);
            $rowSet     = $this->getModel('video')->selectWith($select);
            foreach ($rowSet as $row) {
                $videoIDList['title'][$row->id] = $row->id;
            }
        }

        // Check attribute
        if (!empty($paramsClean)) {
            // Make attribute list
            $attributeList = [];
            foreach ($filterList as $filterSingle) {
                if (isset($paramsClean[$filterSingle['name']]) && !empty($paramsClean[$filterSingle['name']])) {
                    $attributeList[$filterSingle['name']] = [
                        'field' => $filterSingle['id'],
                        'data'  => $paramsClean[$filterSingle['name']],
                    ];
                }
            }
            // Search on attribute
            if (!empty($attributeList)) {
                $checkAttribute = true;
                $column         = ['video'];
                foreach ($attributeList as $attributeSingle) {
                    $where  = [
                        'field' => $attributeSingle['field'],
                        'data'  => $attributeSingle['data'],
                    ];
                    $select = $this->getModel('field_data')->select()->where($where)->columns($column);
                    $rowSet = $this->getModel('field_data')->selectWith($select);
                    foreach ($rowSet as $row) {
                        $videoIDList['attribute'][$row->video] = $row->video;
                    }
                }
            }
        }

        // Set info
        $video = [];
        $count = 0;

        $columns = ['video' => new Expression('DISTINCT video'), '*'];
        $limit   = (intval($limit) > 0) ? intval($limit) : intval($config['view_perpage']);
        $offset  = (int)($page - 1) * $limit;


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
            $rowSet = $this->getModel('link')->selectWith($select)->toArray();
            foreach ($rowSet as $id) {
                $videoIDSelect[] = $id['video'];
            }

            // Get list of video
            if (!empty($videoIDSelect)) {
                $where  = ['status' => 1, 'id' => $videoIDSelect];
                $select = $this->getModel('video')->select()->where($where)->order($order);
                $rowSet = $this->getModel('video')->selectWith($select);
                foreach ($rowSet as $row) {
                    $video[] = Pi::api('video', 'video')->canonizeVideoFilter($row, $categoryList, $filterList);
                }
            }

            // Get count
            $columnsCount = ['count' => new Expression('count(DISTINCT `video`)')];
            $select       = $this->getModel('link')->select()->where($whereLink)->columns($columnsCount);
            $count        = $this->getModel('link')->selectWith($select)->current()->count;
        }

        // Set result
        $result = [
            'videos'     => $video,
            'filterList' => $filterList,
            'paginator'  => [
                'count' => $count,
                'limit' => $limit,
                'page'  => $page,
            ],
            'condition'  => [
                'title' => $pageTitle,
            ],
        ];

        return $result;
    }
}