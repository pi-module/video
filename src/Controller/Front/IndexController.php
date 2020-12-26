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

namespace Module\Video\Controller\Front;

use Pi;
use Pi\Filter;
use Pi\Mvc\Controller\ActionController;
use Pi\Paginator\Paginator;
use Laminas\Db\Sql\Predicate\Expression;

class IndexController extends ActionController
{
    public function indexAction()
    {
        // Get info from url
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // category list
        $categoriesJson = Pi::api('category', 'video')->categoryListJson();

        // Save statistics
        if (Pi::service('module')->isActive('statistics')) {
            Pi::api('log', 'statistics')->save('video', 'index');
        }

        // Check homepage type
        switch ($config['homepage_type']) {
            default:
            case 'list':
                $this->view()->setTemplate('video-angular');
                $this->view()->assign('config', $config);
                $this->view()->assign('categoriesJson', $categoriesJson);
                $this->view()->assign('pageType', 'all');
                break;

            case 'category':
            case 'custom':
                // Set title
                $title = (!empty($config['homepage_title'])) ? $config['homepage_title'] : __('List of videos');

                // Set info
                $categories = [];
                $where      = ['status' => 1];
                $order      = ['display_order DESC', 'title ASC', 'id DESC'];
                $select     = $this->getModel('category')->select()->where($where)->order($order);
                $rowSet     = $this->getModel('category')->selectWith($select);

                // Make list
                foreach ($rowSet as $row) {
                    $categories[$row->id] = Pi::api('category', 'video')->canonizeCategory($row);
                }

                // Set category tree
                $categoryTree = [];
                if (!empty($categories)) {
                    $categoryTree = Pi::api('category', 'video')->makeTreeOrder($categories);
                }

                // Set view
                $this->view()->setTemplate('video-category-index');
                $this->view()->assign('config', $config);
                $this->view()->assign('categories', $categories);
                $this->view()->assign('categoryTree', $categoryTree);
                $this->view()->assign('productTitleH1', $title);
                $this->view()->assign('showIndexDesc', 1);
                $this->view()->assign('isHomepage', 1);
                break;
        }
    }

    public function videoList($where, $limit = 0)
    {
        // Set limit
        if ($limit == 0) {
            $limit = $this->config('view_perpage');
        }

        // Set info
        $video   = [];
        $videoId = [];
        $page    = $this->params('page', 1);
        $sort    = $this->params('sort', 'create');
        $offset  = (int)($page - 1) * $this->config('view_perpage');
        $limit   = empty($limit) ? intval($this->config('view_perpage')) : $limit;
        $order   = $this->setOrder($sort);

        // Set info
        $columns = ['video' => new Expression('DISTINCT video'), '*'];

        // Get info from link table
        $select = $this->getModel('link')->select()->where($where)->columns($columns)->order($order)->offset($offset)->limit($limit);
        $rowSet = $this->getModel('link')->selectWith($select);

        // Make list
        if (!empty($rowSet)) {
            $rowSet = $rowSet->toArray();
            foreach ($rowSet as $id) {
                $videoId[] = $id['video'];
            }
        }

        // Check not empty
        if (!empty($videoId)) {
            // Set info
            $where = ['status' => 1, 'id' => $videoId];

            // Get list of video
            $select = $this->getModel('video')->select()->where($where)->order($order);
            $rowSet = $this->getModel('video')->selectWith($select);
            foreach ($rowSet as $row) {
                $video[$row->id] = Pi::api('video', 'video')->canonizeVideo($row);
            }
        }

        // return video
        return $video;
    }

    public function channelList($where)
    {
        // Set info
        $page  = $this->params('page', 1);
        $sort  = $this->params('sort', 'create');
        $order = $this->setOrder($sort);

        // Get list of video
        $select = $this->getModel('video')->select()->where($where)->order($order);
        $rowSet = $this->getModel('video')->selectWith($select);
        foreach ($rowSet as $row) {
            $video[$row->id] = Pi::api('video', 'video')->canonizeVideo($row);
        }

        // return video
        return $video;
    }

    public function videoPaginator($template, $where)
    {
        $template['module'] = $this->params('module');
        $template['sort']   = $this->params('sort');
        $template['page']   = $this->params('page', 1);

        // get count     
        $columns           = ['count' => new Expression('count(DISTINCT `video`)')];
        $select            = $this->getModel('link')->select()->where($where)->columns($columns);
        $template['count'] = $this->getModel('link')->selectWith($select)->current()->count;

        // paginator
        $paginator = $this->canonizePaginator($template);
        return $paginator;
    }

    public function channelPaginator($template, $where)
    {
        $template['module'] = $this->params('module');
        $template['sort']   = $this->params('sort');
        $template['page']   = $this->params('page', 1);

        // get count     
        $columns           = ['count' => new Expression('count(*)')];
        $select            = $this->getModel('video')->select()->where($where)->columns($columns);
        $template['count'] = $this->getModel('video')->selectWith($select)->current()->count;

        // paginator
        $paginator = $this->canonizePaginator($template);
        return $paginator;
    }

    public function canonizePaginator($template)
    {
        $template['slug'] = (isset($template['slug'])) ? $template['slug'] : '';

        // paginator
        $paginator = Paginator::factory(intval($template['count']));
        $paginator->setItemCountPerPage(intval($this->config('view_perpage')));
        $paginator->setCurrentPageNumber(intval($template['page']));
        $paginator->setUrlOptions(
            [
                'router' => $this->getEvent()->getRouter(),
                'route'  => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
                'params' => array_filter(
                    [
                        'module'     => $this->getModule(),
                        'controller' => $template['controller'],
                        'action'     => $template['action'],
                        'slug'       => $template['slug'],
                        'sort'       => $template['sort'],
                    ]
                ),
            ]
        );

        return $paginator;
    }

    public function setOrder($sort = 'create')
    {
        // Set order
        switch ($sort) {
            case 'update':
                $order = ['time_update DESC', 'id DESC'];
                break;

            case 'create':
            default:
                $order = ['time_create DESC', 'id DESC'];
                break;
        }
        return $order;
    }
}