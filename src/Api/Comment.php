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
use Pi\Application\Api\AbstractComment;

class Comment extends AbstractComment
{
    /** @var string */
    protected $module = 'video';

    /**
     * Get target data
     *
     * @param int|int[] $item Item id(s)
     *
     * @return array
     */
    public function get($item)
    {
        $result = [];
        $items  = (array)$item;

        // Set options
        $video = Pi::api('video', 'video')->getListFromId($items);

        foreach ($items as $id) {
            $result[$id] = [
                'title' => $video[$id]['title'],
                'url'   => $video[$id]['videoUrl'],
                'uid'   => $video[$id]['uid'],
                'time'  => $video[$id]['time_create'],
            ];
        }

        if (is_scalar($item)) {
            $result = $result[$item];
        }

        return $result;
    }

    /**
     * Locate source id via route
     *
     * @param RouteMatch|array $params
     *
     * @return mixed|bool
     */
    public function locate($params = null)
    {
        if (null == $params) {
            $params = Pi::engine()->application()->getRouteMatch();
        }
        if ($params instanceof RouteMatch) {
            $params = $params->getParams();
        }
        if ('video' == $params['module']
            && !empty($params['slug'])
        ) {
            $video = Pi::api('video', 'video')->getVideo($params['slug'], 'slug');
            $item  = $video['id'];
        } else {
            $item = false;
        }
        return $item;
    }

    public function canonize($id)
    {
        $data = Pi::api('video', 'video')->getVideo($id);
        return [
            'url'   => $data['videoUrl'],
            'title' => $data['title'],
        ];
    }
}
