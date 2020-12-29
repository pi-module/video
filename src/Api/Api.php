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
use Laminas\Db\Sql\Predicate\Expression;

/*
 * Pi::api('api', 'video')->videoList($params);
 * Pi::api('api', 'video')->videoSingle($params);
 * Pi::api('api', 'video')->categoryList($params);
 * Pi::api('api', 'video')->viewPrice($price)
 */

class Api extends AbstractApi
{
    public function videoList($params)
    {
        // Get info from url
        $module = $this->getModule();

        // Set has search result
        $hasSearchResult = true;

        // Clean title
        if (Pi::service('module')->isActive('search') && isset($params['title']) && !empty($params['title'])) {
            $title = Pi::api('api', 'search')->parseQuery($params['title']);
        } elseif (isset($params['title']) && !empty($params['title'])) {
            $title = _strip($params['title']);
        } else {
            $title = '';
        }

        // Clean params
        $paramsClean = [];
        foreach ($_GET as $key => $value) {
            $key               = _strip($key);
            $value             = _strip($value);
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
        if (!empty($params['recommended']) && $params['recommended'] == 1) {
            $whereLink['recommended'] = 1;
        }

        // Set page title
        $pageTitle = __('List of videos');

        // Set order
        switch ($params['order']) {
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
        if (isset($params['category']) && !empty($params['category'])) {
            // Get category
            if (is_numeric($params['category']) && intval($params['category']) > 0) {
                $category = Pi::api('category', 'video')->getCategory(intval($params['category']));
            } else {
                $category = Pi::api('category', 'video')->getCategory($params['category'], 'slug');
            }
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
            //$pageTitle = sprintf(__('List of videos on %s category'), $category['title']);
            $pageTitle = $category['title'];
        }

        // Get tag list
        if (isset($params['tag']) && !empty($params['tag'])) {
            $videoIDTag = [];
            // Check favourite
            if (!Pi::service('module')->isActive('tag')) {
                return $result;
            }
            // Get id from tag module
            $tagList = Pi::service('tag')->getList($params['tag'], $module);
            foreach ($tagList as $tagSingle) {
                $videoIDTag[] = $tagSingle['item'];
            }
            // Set header and title
            $pageTitle = sprintf(__('All videos from %s'), $params['tag']);
        }

        // Get favourite list
        if (isset($params['favourite']) && !empty($params['favourite']) && $params['favourite'] == 1) {
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
        if (isset($params['channel']) && !empty($params['channel'])) {
            if (intval($params['channel']) > 0) {
                // Get user id
                $user      = Pi::api('channel', 'video')->user(intval($params['channel']));
                $pageTitle = sprintf(__('All videos from %s channel'), $user['name']);
                // Set where link
                $whereLink['uid'] = intval($params['channel']);
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
            $checkTitle  = true;
            $titles      = is_array($title) ? $title : [$title];
            $columns     = ['id'];
            $recommended = isset($params['recommended']) ? $params['recommended'] : 0;
            $select      = Pi::model('video', $this->getModule())->select()->columns($columns)->where(
                function ($where) use ($titles, $recommended) {
                    $whereMain = clone $where;
                    $whereKey  = clone $where;
                    $whereMain->equalTo('status', 1);
                    if (isset($recommended) && $recommended == 1) {
                        $whereMain->equalTo('recommended', 1);
                    }
                    foreach ($titles as $title) {
                        $whereKey->like('title', '%' . $title . '%')->and;
                    }
                    $where->andPredicate($whereMain)->andPredicate($whereKey);
                }
            )->order($order);
            $rowSet      = Pi::model('video', $this->getModule())->selectWith($select);
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
                    $select = Pi::model('field_data', $this->getModule())->select()->where($where)->columns($column);
                    $rowSet = Pi::model('field_data', $this->getModule())->selectWith($select);
                    foreach ($rowSet as $row) {
                        $videoIDList['attribute'][$row->video] = $row->video;
                    }
                }
            }
        }

        // Set info
        $video = [];
        $count = 0;

        $columns = ['product' => new Expression('DISTINCT video'), '*'];
        $limit   = (isset($params['limit']) && intval($params['limit']) > 0) ? intval($params['limit']) : intval($config['view_perpage']);
        $page    = (isset($params['page']) && intval($params['page']) > 0) ? intval($params['page']) : 1;
        $offset  = (int)($page - 1) * $limit;

        // Set category on where link
        if (isset($categoryIDList) && !empty($categoryIDList)) {
            $whereLink['category'] = $categoryIDList;
        }

        // Set video on where link from title and attribute
        if ($checkTitle && $checkAttribute) {
            if (isset($videoIDList['title']) && !empty($videoIDList['title']) && isset($videoIDList['attribute']) && !empty($videoIDList['attribute'])) {
                $whereLink['video'] = array_intersect($videoIDList['title'], $videoIDList['attribute']);
            } else {
                $hasSearchResult = false;
            }
        } elseif ($checkTitle) {
            if (isset($videoIDList['title']) && !empty($videoIDList['title'])) {
                $whereLink['video'] = $videoIDList['title'];
            } else {
                $hasSearchResult = false;
            }
        } elseif ($checkAttribute) {
            if (isset($videoIDList['attribute']) && !empty($videoIDList['attribute'])) {
                $whereLink['video'] = $videoIDList['attribute'];
            } else {
                $hasSearchResult = false;
            }
        }

        // Set favourite videos on where link
        if (isset($params['favourite']) && !empty($params['favourite']) && $params['favourite'] == 1 && isset($videoIDFavourite)) {
            if (isset($whereLink['video']) && !empty($whereLink['video'])) {
                $whereLink['video'] = array_intersect($videoIDFavourite, $whereLink['video']);
            } elseif (!isset($whereLink['video']) || empty($whereLink['video'])) {
                $whereLink['video'] = $videoIDFavourite;
            } else {
                $hasSearchResult = false;
            }
        }

        // Set tag videos on where link
        if (isset($params['tag']) && !empty($params['tag']) && isset($videoIDTag)) {
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
            $select = Pi::model('link', $this->getModule())->select()->where($whereLink)->columns($columns)->order($order)->offset($offset)->limit($limit);
            $rowSet = Pi::model('link', $this->getModule())->selectWith($select)->toArray();
            foreach ($rowSet as $id) {
                $videoIDSelect[] = $id['video'];
            }

            // Get list of video
            if (isset($videoIDSelect) && !empty($videoIDSelect)) {
                $where  = ['status' => 1, 'id' => $videoIDSelect];
                $select = Pi::model('video', $this->getModule())->select()->where($where)->order($order);
                $rowSet = Pi::model('video', $this->getModule())->selectWith($select);
                foreach ($rowSet as $row) {
                    $singleVideo           = Pi::api('video', 'video')->canonizeVideoFilter($row, $categoryList, $filterList);
                    $singleVideo['access'] = Pi::api('video', 'video')->getAccess($video);
                    $video[]               = $singleVideo;
                }
            }

            // Get count
            $columnsCount = ['count' => new Expression('count(DISTINCT `video`)')];
            $select       = Pi::model('link', $this->getModule())->select()->where($whereLink)->columns($columnsCount);
            $count        = Pi::model('link', $this->getModule())->selectWith($select)->current()->count;
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

    public function videoSingle($params)
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());

        // Get video
        if (!empty($params['slug'])) {
            $singleVideo = Pi::api('video', 'video')->getVideoJson($params['slug'], 'slug');
        } elseif (!empty($params['id'])) {
            $singleVideo = Pi::api('video', 'video')->getVideoJson($params['id']);
        } else {
            return false;
        }

        // Set related videos
        $where            = [
            'status'     => 1,
            'category'   => $singleVideo['category'],
            'video != ?' => $singleVideo['id'],
        ];
        $videoRelated     = [];
        $videoRelatedList = Pi::api('video', 'video')->getList($where, $config['view_related_number']);
        foreach ($videoRelatedList as $videoRelatedSingle) {
            $videoRelated[] = [
                'id'        => $videoRelatedSingle['id'],
                'title'     => $videoRelatedSingle['title'],
                'slug'      => $videoRelatedSingle['slug'],
                'mediumUrl' => $videoRelatedSingle['mediumUrl'],
                'thumbUrl'  => $videoRelatedSingle['thumbUrl'],
                'video_url' => $videoRelatedSingle['video_url'],
            ];
        }

        // Set video
        $video   = [];
        $video[] = [
            'id'                  => $singleVideo['id'],
            'title'               => $singleVideo['title'],
            'slug'                => $singleVideo['slug'],
            'time_create'         => $singleVideo['time_create'],
            'time_create_view'    => $singleVideo['time_create_view'],
            'categories'          => $singleVideo['categories'],
            'hits'                => $singleVideo['hits'],
            'recommended'         => $singleVideo['recommended'],
            'favourite'           => $singleVideo['favourite'],
            'video_duration_view' => $singleVideo['video_duration_view'],
            'text_summary'        => $singleVideo['text_summary'],
            'text_description'    => $singleVideo['text_description'],
            'channelUrl'          => $singleVideo['channelUrl'],
            'videoUrl'            => $singleVideo['videoUrl'],
            'largeUrl'            => $singleVideo['largeUrl'],
            'video_url'           => $singleVideo['video_url'],
            'videoRelated'        => $videoRelated,
        ];

        return $video;
    }

    public function categoryList()
    {
        $category = [];

        $where  = ['status' => 1, 'type' => 'category'];
        $order  = ['title ASC', 'id DESC'];
        $select = Pi::model('category', $this->getModule())->select()->where($where)->order($order);
        $rowSet = Pi::model('category', $this->getModule())->selectWith($select);
        foreach ($rowSet as $row) {
            $categorySingle = Pi::api('category', 'video')->canonizeCategory($row);
            $category[]     = [
                'id'        => $categorySingle['id'],
                'slug'      => $categorySingle['slug'],
                'parent'    => $categorySingle['parent'],
                'title'     => $categorySingle['title'],
                'mediumUrl' => $categorySingle['mediumUrl'],
                'thumbUrl'  => $categorySingle['thumbUrl'],
            ];
        }

        $category = Pi::api('category', 'video')->makeTree($category);

        // Get count
        $columnsCount = ['count' => new Expression('count(*)')];
        $select       = Pi::model('category', $this->getModule())->select()->where($where)->columns($columnsCount);
        $count        = Pi::model('category', $this->getModule())->selectWith($select)->current()->count;

        // Set result
        $result = [
            'categories' => $category,
            'paginator'  => [
                'count' => $count,
            ],
        ];

        return $result;
    }

    public function viewPrice($price)
    {
        if (Pi::service('module')->isActive('order')) {
            // Load language
            Pi::service('i18n')->load(['module/order', 'default']);
            // Set price
            $viewPrice = Pi::api('api', 'order')->viewPrice($price);
        } else {
            $viewPrice = _currency($price);
        }
        return $viewPrice;
    }

    // ToDo : rebuild function when need use it on mobile app
    /* public function setAccess($params)
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());

        // Check VAS payment
        if ($config['sale_video'] == 'single') {

            // Get video
            $video = Pi::api('video', 'video')->getVideoLight($params['id']);

            return Pi::api('video', 'video')->setAccess($video, $params['uid']);
        } else {
            return [
                'status'  => 0,
                'message' => '',
            ];
        }
    } */
}
