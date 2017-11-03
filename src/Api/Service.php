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

/*
 * Pi::api('service', 'video')->getVideo($module, $table, $item, $hits);
 * Pi::api('service', 'video')->setVideo($videos, $module, $table, $item);
 */

class Service extends AbstractApi
{
    public function getVideo($module, $table = '', $item = '', $hits = true)
    {
        $list = [];
        $where = ['module_name' => $module];
        if (isset($table) && !empty($table)) {
            $where['module_table'] = $table;
        }
        if (isset($item) && !empty($item)) {
            $where['module_item'] = $item;
        }
        $select = Pi::model('service', $this->getModule())->select()->where($where);
        $rowset = Pi::model('service', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            // Canonize video
            $list[$row->id] = Pi::api('video', 'video')->getVideo($row->id);
            // Update Hits
            if ($hits) {
                Pi::model('video', $this->getModule())->increment('hits', ['id' => $row->id]);
            }
        }
        return $list;
    }

    public function setVideo($videos, $module, $table, $item)
    {
        // Set where
        $where = [
            'module_name'  => $module,
            'module_table' => $table,
            'module_item'  => $item,
        ];
        //Remove
        Pi::model('service', $this->getModule())->delete($where);
        // Set videos
        if (!empty($videos)) {
            $videos = is_array($videos) ? $videos : [$videos];
            foreach ($videos as $video) {
                // Set array
                $values['video'] = $video;
                $values['module_name'] = $module;
                $values['module_table'] = $table;
                $values['module_item'] = $item;
                // Save
                $row = Pi::model('service', $this->getModule())->createRow();
                $row->assign($values);
                $row->save();
            }
        }
    }
}