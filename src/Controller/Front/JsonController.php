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
use Laminas\Db\Sql\Predicate\Expression;

class JsonController extends IndexController
{
    public function searchAction()
    {
        // Get info from url
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Get info from url
        $params = [
            'page'        => $this->params('page', 1),
            'title'       => $this->params('title'),
            'category'    => $this->params('category'),
            'tag'         => $this->params('tag'),
            'favourite'   => $this->params('favourite'),
            'recommended' => $this->params('recommended'),
            'limit'       => $this->params('limit'),
            'order'       => $this->params('order'),
            'channel'     => $this->params('channel'),
        ];

        // Get video list
        $videoList = Pi::api('api', 'video')->videoList($params);

        // Set column class
        switch ($config['view_column']) {
            case 1:
                $videoList['condition']['columnSize'] = 'col-lg-12 col-md-12 col-12';
                break;

            case 2:
                $videoList['condition']['columnSize'] = 'col-lg-6 col-md-6 col-12';
                break;

            case 3:
                $videoList['condition']['columnSize'] = 'col-lg-4 col-md-4 col-12';
                break;

            case 4:
                $videoList['condition']['columnSize'] = 'col-lg-3 col-md-3 col-12';
                break;

            case 6:
                $videoList['condition']['columnSize'] = 'col-lg-2 col-md-2 col-12';
                break;

            default:
                $videoList['condition']['columnSize'] = 'col-lg-3 col-md-3 col-12';
                break;
        }

        return $videoList;
    }
}