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
use Zend\Db\Sql\Predicate\Expression;

/*
 * Pi::api('category', 'video')->getCategory($parameter, $type = 'id');
 * Pi::api('category', 'video')->setLink($video, $category, $create, $update, $status, $uid, $hits);
 * Pi::api('category', 'video')->findFromCategory($category);
 * Pi::api('category', 'video')->categoryList($parent);
 * Pi::api('category', 'video')->categoryListJson();
 * Pi::api('category', 'video')->categoryCount();
 * Pi::api('category', 'video')->canonizeCategory($category);
 * Pi::api('category', 'video')->sitemap();
 * Pi::api('category', 'video')->makeTree($elements, $parentId);
 * Pi::api('category', 'video')->makeTreeOrder($elements, $parentId = 0);
 */

class Category extends AbstractApi
{
    public function getCategory($parameter, $type = 'id')
    {
        $category = Pi::model('category', $this->getModule())->find($parameter, $type);
        $category = $this->canonizeCategory($category);
        return $category;
    }

    public function setLink($video, $category, $create, $update, $status, $uid, $hits)
    {
        //Remove
        Pi::model('link', $this->getModule())->delete(array('video' => $video));
        // Add
        $allCategory = json_decode($category);
        foreach ($allCategory as $category) {
            // Set array
            $values['video'] = $video;
            $values['category'] = $category;
            $values['time_create'] = $create;
            $values['time_update'] = $update;
            $values['status'] = $status;
            $values['uid'] = $uid;
            $values['hits'] = $hits;
            // Save
            $row = Pi::model('link', $this->getModule())->createRow();
            $row->assign($values);
            $row->save();
        }
    }

    public function findFromCategory($category)
    {
        $list = array();
        $where = array('category' => $category);
        $select = Pi::model('link', $this->getModule())->select()->where($where);
        $rowset = Pi::model('link', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $row = $row->toArray();
            $list[] = $row['video'];
        }
        return array_unique($list);
    }

    public function categoryList($parent = null)
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());

        // Check type
        if (is_null($parent)) {
            $where = array('status' => 1);
        } else {
            $where = array('status' => 1, 'parent' => $parent);
        }
        $return = array();
        $order = array('display_order ASC');
        // Make list
        $select = Pi::model('category', $this->getModule())->select()->where($where)->order($order);
        $rowset = Pi::model('category', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $return[$row->id] = $row->toArray();
            $return[$row->id]['url'] = Pi::url(Pi::service('url')->assemble('video', array(
                'module' => $this->getModule(),
                'controller' => 'category',
                'slug' => $return[$row->id]['slug'],
            )));
            if ($row->image) {
                $return[$row->id]['thumbUrl'] = Pi::url(
                    sprintf('upload/%s/thumb/%s/%s',
                        $config['image_path'],
                        $return[$row->id]['path'],
                        $return[$row->id]['image']
                    ));
            }

        }
        return $return;
    }

    public function categoryListJson()
    {
        $return = array();
        $where = array('status' => 1);
        $order = array('display_order ASC');
        // Make list
        $select = Pi::model('category', $this->getModule())->select()->where($where)->order($order);
        $rowset = Pi::model('category', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $return[] = array(
                'id' => $row->id,
                'parent' => $row->parent,
                'text' => $row->title,
                'href' => Pi::url(Pi::service('url')->assemble('video', array(
                    'module' => $this->getModule(),
                    'controller' => 'category',
                    'slug' => $row->slug,
                ))),
            );
        }
        $return = $this->makeTree($return);
        $return =  json_encode($return);
        return $return;
    }

    public function categoryCount()
    {
        $columns = array('count' => new Expression('count(*)'));
        $select = Pi::model('category', $this->getModule())->select()->columns($columns);
        $count = Pi::model('category', $this->getModule())->selectWith($select)->current()->count;
        return $count;
    }

    public function canonizeCategory($category)
    {
        // Check
        if (empty($category)) {
            return '';
        }
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // boject to array
        $category = $category->toArray();
        // Set text_description
        $category['text_description'] = Pi::service('markup')->render($category['text_description'], 'html', 'html');
        // Set times
        $category['time_create_view'] = _date($category['time_create']);
        $category['time_update_view'] = _date($category['time_update']);
        // Set item url
        $category['categoryUrl'] = Pi::url(Pi::service('url')->assemble('video', array(
            'module' => $this->getModule(),
            'controller' => 'category',
            'slug' => $category['slug'],
        )));
        // Set image url
        if ($category['image']) {
            // Set image original url
            $category['originalUrl'] = Pi::url(
                sprintf('upload/%s/original/%s/%s',
                    $config['image_path'],
                    $category['path'],
                    $category['image']
                ));
            // Set image large url
            $category['largeUrl'] = Pi::url(
                sprintf('upload/%s/large/%s/%s',
                    $config['image_path'],
                    $category['path'],
                    $category['image']
                ));
            // Set image medium url
            $category['mediumUrl'] = Pi::url(
                sprintf('upload/%s/medium/%s/%s',
                    $config['image_path'],
                    $category['path'],
                    $category['image']
                ));
            // Set image thumb url
            $category['thumbUrl'] = Pi::url(
                sprintf('upload/%s/thumb/%s/%s',
                    $config['image_path'],
                    $category['path'],
                    $category['image']
                ));
        }
        // return category
        return $category;
    }

    public function sitemap()
    {
        if (Pi::service('module')->isActive('sitemap')) {
            // Remove old links
            Pi::api('sitemap', 'sitemap')->removeAll($this->getModule(), 'category');
            // find and import
            $columns = array('id', 'slug', 'status');
            $select = Pi::model('category', $this->getModule())->select()->columns($columns);
            $rowset = Pi::model('category', $this->getModule())->selectWith($select);
            foreach ($rowset as $row) {
                // Make url
                $loc = Pi::url(Pi::service('url')->assemble('video', array(
                    'module' => $this->getModule(),
                    'controller' => 'category',
                    'slug' => $row->slug,
                )));
                // Add to sitemap
                Pi::api('sitemap', 'sitemap')->groupLink($loc, $row->status, $this->getModule(), 'category', $row->id);
            }
        }
    }

    public function makeTree($elements, $parentId = 0)
    {
        $branch = array();
        foreach ($elements as $element) {
            if ($element['parent'] == $parentId) {
                $children = $this->makeTree($elements, $element['id']);
                if ($children) {
                    $element['nodes'] = $children;
                }
                $branch[] = $element;
                unset($elements[$element['id']]);
                unset($depth);
            }
        }
        return $branch;
    }

    public function makeTreeOrder($elements, $parentId = 0)
    {
        $branch = array();
        // Set category list as tree
        foreach ($elements as $element) {
            if ($element['parent'] == $parentId) {
                $depth = 0;
                $branch[$element['id']] = $element;
                $children = $this->makeTreeOrder($elements, $element['id']);
                if ($children) {
                    $depth++;
                    foreach ($children as $key => $value) {
                        $branch[$key] = $value;
                    }
                }
                unset($elements[$element['id']]);
                unset($depth);
            }
        }
        return $branch;
    }
}