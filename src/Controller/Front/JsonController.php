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

    public function filterSearchAction() {
        // Get info from url
        $module = $this->params('module');
        $keyword = $this->params('keyword');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Check keyword not empty
        if (empty($keyword)) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The keyword not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Set list
        $list = array();
        // Set info
        $where = array('status' => 1);
        $where['title LIKE ?'] = '%' . $keyword . '%';
        $order = array('time_create DESC', 'id DESC');
        // Item list header
        $list[] = array(
            'class' => ' class="dropdown-header"',
            'title' => sprintf(__('Videos related to %s'), $keyword),
            'url' => '#',
            'image' => Pi::service('asset')->logo(),
        );
        // Get list of video
        $select = $this->getModel('video')->select()->where($where)->order($order)->limit(10);
        $rowset = $this->getModel('video')->selectWith($select);
        foreach ($rowset as $row) {
            $video = Pi::api('video', 'video')->canonizeVideoLight($row);
            $list[] = array(
                'class' => '',
                'title' => $video['title'],
                'url' => $video['videoUrl'],
                'image' =>  $video['thumbUrl'],
            );
        }
        // Location list header
        $list[] = array(
            'class' => ' class="dropdown-header"',
            'title' => sprintf(__('Categories related to %s'), $keyword),
            'url' => '#',
            'image' => Pi::service('asset')->logo(),
        );
        // Get list of categories
        $select = $this->getModel('category')->select()->where($where)->order($order)->limit(5);
        $rowset = $this->getModel('category')->selectWith($select);
        foreach ($rowset as $row) {
            $category = Pi::api('category', 'video')->canonizeCategory($row);
            $list[] = array(
                'class' => '',
                'title' => $category['title'],
                'url' => $category['categoryUrl'],
                'image' => isset($category['thumbUrl']) ? $category['thumbUrl'] : Pi::service('asset')->logo(),
            );
        }
        // Set view
        return $list;
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