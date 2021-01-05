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

namespace Module\Video\Route;

use Pi;
use Pi\Mvc\Router\Http\Standard;

class Video extends Standard
{
    /**
     * Default values.
     *
     * @var array
     */
    protected $defaults
        = [
            'module'     => 'video',
            'controller' => 'index',
            'action'     => 'index',
        ];

    protected $controllerList
        = [
            'category', 'channel', 'favourite', 'index', 'dashboard', 'tag', 'watch', 'order', 'json'
        ];

    /**
     * {@inheritDoc}
     */
    protected $structureDelimiter = '/';

    /**
     * {@inheritDoc}
     */
    protected function parse($path)
    {
        $matches = [];
        $parts   = array_filter(explode($this->structureDelimiter, $path));

        // Set controller
        $matches = array_merge($this->defaults, $matches);
        if (isset($parts[0]) && in_array($parts[0], $this->controllerList)) {
            $matches['controller'] = $this->decode($parts[0]);
            // Make Match
            if (isset($matches['controller'])) {
                switch ($matches['controller']) {

                    case 'category':
                        if (!isset($parts[1])) {
                            $matches['action'] = 'list';
                        }
                        break;

                    case 'watch':
                    case 'index':
                        $matches['action'] = 'index';
                        break;

                    case 'order':
                        if (urldecode($parts[1]) == 'playlist') {
                            $matches['action'] = 'playlist';
                            $matches['id']   = urldecode($parts[2]);
                        } else {
                            $matches['action'] = 'index';
                            $matches['slug']   = urldecode($parts[1]);
                        }
                        break;

                    case 'tag':
                        if (isset($parts[1]) && !empty($parts[1])) {
                            $matches['action'] = 'index';
                            $matches['slug']   = urldecode($parts[1]);
                        } else {
                            $matches['action'] = 'list';
                        }
                        break;

                    case 'channel':
                        if ($parts[1] == 'list') {
                            $matches['action'] = 'list';
                        } elseif (is_numeric($parts[1])) {
                            $matches['action'] = 'index';
                            $matches['id']     = intval($parts[1]);
                        }
                        break;

                    case 'dashboard':
                        if (in_array($parts[1], ['index', 'purchased'])) {
                            $matches['action'] = $parts[1];
                            if (is_numeric($parts[2])) {
                                $matches['id'] = intval($parts[2]);
                            }
                        }
                        break;

                    case 'json':
                        $matches['action'] = $this->decode($parts[1]);

                        if ($parts[1] == 'filterCategory') {
                            $matches['slug'] = $this->decode($parts[2]);
                        } elseif ($parts[1] == 'filterTag') {
                            $matches['slug'] = $this->decode($parts[2]);
                        }

                        if (in_array('id', $parts)) {
                            $matches['id'] = intval($this->decode($parts[array_search('id', $parts) + 1]));
                        }

                        if (in_array('slug', $parts)) {
                            $matches['slug'] = urldecode($this->decode($parts[array_search('slug', $parts) + 1]));
                        }

                        if (in_array('update', $parts)) {
                            $matches['update'] = intval($this->decode($parts[array_search('update', $parts) + 1]));
                        }

                        if (in_array('password', $parts)) {
                            $matches['password'] = urldecode($this->decode($parts[array_search('password', $parts) + 1]));
                        }

                        if (in_array('type', $parts)) {
                            $matches['type'] = urldecode($this->decode($parts[array_search('type', $parts) + 1]));
                        }

                        if (in_array('order', $parts)) {
                            $matches['order'] = urldecode($this->decode($parts[array_search('order', $parts) + 1]));
                        }

                        if (in_array('limit', $parts)) {
                            $matches['limit'] = intval($this->decode($parts[array_search('limit', $parts) + 1]));
                        }

                        if (in_array('category', $parts)) {
                            $matches['category'] = intval($this->decode($parts[array_search('category', $parts) + 1]));
                        }

                        if (in_array('keyword', $parts)) {
                            $matches['keyword'] = urldecode($this->decode($parts[array_search('keyword', $parts) + 1]));
                        }

                        /* if (isset($parts[2]) && $parts[2] == 'id') {
                            $matches['id'] = intval($parts[3]);
                        }

                        if (isset($parts[2]) && $parts[2] == 'update') {
                            $matches['update'] = intval($parts[3]);
                        } elseif (isset($parts[4]) && $parts[4] == 'update') {
                            $matches['update'] = intval($parts[5]);
                        }

                        if (isset($parts[4]) && $parts[4] == 'password') {
                            $matches['password'] = $this->decode($parts[5]);
                        } elseif (isset($parts[6]) && $parts[6] == 'password') {
                            $matches['password'] = $this->decode($parts[7]);
                        } */
                        break;
                }
            }
        } elseif (isset($parts[0])) {
            $parts[0]     = urldecode($parts[0]);
            $categorySlug = Pi::registry('categoryRoute', 'video')->read();
            if (in_array($parts[0], $categorySlug)) {
                $matches['controller'] = 'category';
                $matches['action']     = 'index';
                $matches['slug']       = $this->decode($parts[0]);
            } elseif ($parts[0] == 'channel') {
                $matches['controller'] = 'channel';
                $matches['action']     = 'index';
            } elseif ($parts[0] == 'favourite') {
                $matches['controller'] = 'favourite';
                $matches['action']     = 'index';
            } else {
                $matches['controller'] = 'watch';
                $matches['action']     = 'index';
                $matches['slug']       = $this->decode($parts[0]);
            }
        }

        /* echo '<pre>';
        print_r($matches);
        print_r($parts);
        echo '</pre>'; */

        return $matches;
    }

    /**
     * assemble(): Defined by Route interface.
     *
     * @param array $params
     * @param array $options
     *
     * @return string
     * @see    Route::assemble()
     *
     */
    public function assemble(
        array $params = [],
        array $options = []
    ) {
        $mergedParams = array_merge($this->defaults, $params);
        if (!$mergedParams) {
            return $this->prefix;
        }

        // Set module
        if (!empty($mergedParams['module'])) {
            $url['module'] = $mergedParams['module'];
        }

        // Set controller
        if (!empty($mergedParams['controller'])
            && $mergedParams['controller'] != 'index'
            && $mergedParams['controller'] != 'category'
            && $mergedParams['controller'] != 'watch'
            && in_array($mergedParams['controller'], $this->controllerList)
        ) {
            $url['controller'] = $mergedParams['controller'];
        }

        // Set action
        if (!empty($mergedParams['action'])
            && $mergedParams['action'] != 'index'
        ) {
            $url['action'] = $mergedParams['action'];
        }

        // Set category list url
        if ($mergedParams['controller'] == 'category'
            && $mergedParams['action'] == 'index'
            && empty($mergedParams['slug'])
        ) {
            $url['controller'] = 'category';
        }

        // Set slug
        if (!empty($mergedParams['slug'])) {
            $url['slug'] = $mergedParams['slug'];
        }

        // Set id
        if (!empty($mergedParams['id']) && $mergedParams['controller'] == 'json') {
            $url['id'] = 'id' . $this->paramDelimiter . $mergedParams['id'];
        } elseif (!empty($mergedParams['id'])) {
            $url['id'] = $mergedParams['id'];
        }

        // Set update
        if (!empty($mergedParams['update'])) {
            $url['update'] = 'update' . $this->paramDelimiter . $mergedParams['update'];
        }

        // Set password
        if (!empty($mergedParams['password'])) {
            $url['password'] = 'password' . $this->paramDelimiter . $mergedParams['password'];
        }

        // Make url
        $url = implode($this->paramDelimiter, $url);

        if (empty($url)) {
            return $this->prefix;
        }
        return $this->paramDelimiter . $url;
    }
}
