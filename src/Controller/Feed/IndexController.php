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

namespace Module\Video\Controller\Feed;

use Pi;
use Pi\Mvc\Controller\FeedController;

class IndexController extends FeedController
{
    public function indexAction()
    {
        $feed = $this->getDataModel([
            'title'        => __('Video feed'),
            'description'  => __('Recent videos and audios.'),
            'date_created' => time(),
        ]);
        $order = ['time_create DESC', 'id DESC'];
        $where = ['status' => 1];
        $select = $this->getModel('video')->select()->where($where)->order($order)->limit(10);
        $rowset = $this->getModel('video')->selectWith($select);
        foreach ($rowset as $row) {
            $entry = [];
            $entry['title'] = $row->title;
            $description = (empty($row->description)) ? __('Click to see video') : $row->description;
            $description = strtolower(trim($description));
            $entry['description'] = (empty($description)) ? ' ' : $description;
            $entry['date_modified'] = (int)$row->time_create;
            $entry['link'] = Pi::url(Pi::service('url')->assemble('video', [
                'module'     => $this->getModule(),
                'controller' => 'video',
                'slug'       => $row->slug,
            ]));
            $feed->entry = $entry;
        }
        return $feed;
    }
}