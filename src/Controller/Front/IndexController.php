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
namespace Module\Video\Controller\Front;

use Pi;
use Pi\Filter;
use Pi\Mvc\Controller\ActionController;
use Pi\Paginator\Paginator;
use Zend\Json\Json;
use Zend\Db\Sql\Predicate\Expression;

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
        // Set filter url
        $filterUrl = Pi::url($this->url('', array(
            'controller' => 'json',
            'action' => 'filterIndex'
        )));
        // Set filter list
        $filterList = Pi::api('attribute', 'video')->filterList();
        // Set view
        $this->view()->setTemplate('video-angular');

        $this->view()->assign('config', $config);
        $this->view()->assign('categoriesJson', $categoriesJson);
        $this->view()->assign('filterUrl', $filterUrl);
        $this->view()->assign('filterList', $filterList);
        $this->view()->assign('videoTitleH1', __('List of videos'));
        $this->view()->assign('showIndexDesc', 1);
        $this->view()->assign('isHomepage', 1);
    }

    public function videoList($where, $limit = 0)
    {
        // Set limit
        if ($limit == 0) {
            $limit = $this->config('view_perpage');
        }
        // Set info
        $id = array();
        $page = $this->params('page', 1);
        $module = $this->params('module');
        $sort = $this->params('sort', 'create');
        $offset = (int)($page - 1) * $limit;
        $limit = intval($limit);
        $order = $this->setOrder($sort);
        // Set info
        $columns = array('video' => new Expression('DISTINCT video'));
        // Get info from link table
        $select = $this->getModel('link')->select()->where($where)->columns($columns)
        ->order($order)->offset($offset)->limit($limit);
        $rowset = $this->getModel('link')->selectWith($select)->toArray();
        // Make list
        foreach ($rowset as $id) {
            $videoId[] = $id['video'];
        }
        // Set info
        $where = array('status' => 1, 'id' => $videoId);
        // Get list of video
        $select = $this->getModel('video')->select()->where($where)->order($order);
        $rowset = $this->getModel('video')->selectWith($select);
        foreach ($rowset as $row) {
            $video[$row->id] = Pi::api('video', 'video')->canonizeVideo($row);
        }
        // return video
        return $video;
    }

    public function channelList($where)
    {
        // Set info
        $id = array();
        $page = $this->params('page', 1);
        $module = $this->params('module');
        $sort = $this->params('sort', 'create');
        $offset = (int)($page - 1) * $this->config('view_perpage');
        $limit = intval($this->config('view_perpage'));
        $order = $this->setOrder($sort);
        // Get list of video
        $select = $this->getModel('video')->select()->where($where)->order($order);
        $rowset = $this->getModel('video')->selectWith($select);
        foreach ($rowset as $row) {
            $video[$row->id] = Pi::api('video', 'video')->canonizeVideo($row);
        }
        // return video
        return $video;
    }

    public function videoPaginator($template, $where)
    {
        $template['module'] = $this->params('module');
        $template['sort'] = $this->params('sort');
        $template['page'] = $this->params('page', 1);
        // get count     
        $columns = array('count' => new Expression('count(DISTINCT `video`)'));
        $select = $this->getModel('link')->select()->where($where)->columns($columns);
        $template['count'] = $this->getModel('link')->selectWith($select)->current()->count;
        // paginator
        $paginator = $this->canonizePaginator($template);
        return $paginator;
    }

    public function channelPaginator($template, $where)
    {
        $template['module'] = $this->params('module');
        $template['sort'] = $this->params('sort');
        $template['page'] = $this->params('page', 1);
        // get count     
        $columns = array('count' => new Expression('count(*)'));
        $select = $this->getModel('video')->select()->where($where)->columns($columns);
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
        $paginator->setUrlOptions(array(
            'router'    => $this->getEvent()->getRouter(),
            'route'     => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'params'    => array_filter(array(
                'module'        => $this->getModule(),
                'controller'    => $template['controller'],
                'action'        => $template['action'],
                'slug'          => $template['slug'],
                'sort'          => $template['sort'],
            )),
        ));
        return $paginator;
    }

    public function setOrder($sort = 'create')
    {
        // Set order
        switch ($sort) {
            case 'update':
                $order = array('time_update DESC', 'id DESC');
                break; 

            case 'create':
            default:
                $order = array('time_create DESC', 'id DESC');
                break;
        } 
        return $order;
    }
}