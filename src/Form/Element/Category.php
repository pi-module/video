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

namespace Module\Video\Form\Element;

use Pi;
use Laminas\Form\Element\Select;

class Category extends Select
{
    /**
     * @return array
     */
    public function getValueOptions()
    {
        if (empty($this->valueOptions)) {
            // Get topic list
            $list    = [];
            $columns = ['id', 'parent', 'title'];
            $where   = ['status' => 1, 'type' => 'category'];
            $order   = ['title ASC', 'id DESC'];
            $select  = Pi::model('category', 'video')->select()->columns($columns)->where($where)->order($order);
            $rowSet  = Pi::model('category', 'video')->selectWith($select);
            foreach ($rowSet as $row) {
                $list[$row->id] = $row->toArray();
            }
            $this->valueOptions = $this->getTree($list);
        }
        return $this->valueOptions;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        $this->Attributes = [
            'size'     => 5,
            'multiple' => 1,
            'class'    => 'form-control',
        ];
        // check form size
        if (isset($this->attributes['size'])) {
            $this->Attributes['size'] = $this->attributes['size'];
        }
        // check form multiple
        if (isset($this->attributes['multiple'])) {
            $this->Attributes['multiple'] = $this->attributes['multiple'];
        }
        return $this->Attributes;
    }

    /**
     * @return array
     */
    public function getTree($elements, $parentId = 0)
    {
        $branch = [];

        // Set default category options
        if ($parentId == 0) {
            if (isset($this->options['category']) && !empty($this->options['category'])) {
                foreach ($this->options['category'] as $key => $value) {
                    $branch[$key] = $value;
                }
            } elseif (isset($this->options['category']) && empty($this->options['category']) && $this->attributes['size'] == 1) {
                $branch[0] = '';
            } else {
                $branch[0] = __('All Category');
            }
        }

        // Set category list as tree
        foreach ($elements as $element) {
            if ($element['parent'] == $parentId) {
                $depth                  = 0;
                $branch[$element['id']] = $element['title'];
                $children               = $this->getTree($elements, $element['id']);
                if ($children) {
                    $depth++;
                    foreach ($children as $key => $value) {
                        $branch[$key] = sprintf('%s%s', str_repeat('-', $depth), $value);
                    }
                }
                unset($elements[$element['id']]);
                unset($depth);
            }
        }

        return $branch;
    }
}
