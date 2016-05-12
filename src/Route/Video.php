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
namespace Module\Video\Route;

use Pi\Mvc\Router\Http\Standard;

class Video extends Standard
{
    /**
     * Default values.
     * @var array
     */
    protected $defaults = array(
        'module'        => 'video',
        'controller'    => 'index',
        'action'        => 'index'
    );

    protected $controllerList = array(
        'category', 'channel', 'favourite', 'index', 'submit', 'tag', 'watch', 'json'
    );

    /**
     * {@inheritDoc}
     */
    protected $structureDelimiter = '/';

    /**
     * {@inheritDoc}
     */
    protected function parse($path)
    {
        $matches = array();
        $parts = array_filter(explode($this->structureDelimiter, $path));

        // Set controller
        $matches = array_merge($this->defaults, $matches);
        if (isset($parts[0]) && in_array($parts[0], $this->controllerList)) {
            $matches['controller'] = $this->decode($parts[0]);
        } elseif (isset($parts[0]) && !in_array($parts[0], $this->controllerList)) {
            if (in_array($parts[0], array('index', 'filter'))) {
                $matches['controller'] = 'index';
            } else {
                return '';
            }
        }

        // Make Match
        if (isset($matches['controller'])) {
            switch ($matches['controller']) {

                case 'watch':
                    $matches['action'] = 'index';
                    $matches['slug'] = $this->decode($parts[1]);
                    break;

                case 'category':
                    if (isset($parts[1]) && $parts[1] == 'filter') {
                        $matches['action'] = 'filter';
                        $matches['slug'] = $this->decode($parts[2]);
                    } elseif (isset($parts[1]) && !empty($parts[1])) {
                        $matches['action'] = 'index';
                        $matches['slug'] = $this->decode($parts[1]);
                    } else {
                        $matches['action'] = 'list';
                    }
                    break;

                case 'index':
                    $matches['action'] = 'index';
                    break;

                case 'tag':
                    if (isset($parts[1]) && !empty($parts[1])) {
                        $matches['action'] = 'index';
                        $matches['slug'] = urldecode($parts[1]);
                    } else {
                        $matches['action'] = 'list';
                    }
                    break;

                case 'channel':
                    if ($parts[1] == 'list') {
                        $matches['action'] = 'list';
                    } elseif(is_numeric($parts[1])) {
                        $matches['action'] = 'index';
                        $matches['id'] = intval($parts[1]);
                    }
                    break;

                case 'submit':
                    if (in_array($parts[1], array('index', 'update', 'additional', 'finish'))) {
                        $matches['action'] = $parts[1];
                        if(is_numeric($parts[2])) {
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
                    } elseif ($parts[1] == 'filterSearch') {
                        $keyword = _get('keyword');
                        if (isset($keyword) && !empty($keyword)) {
                            $matches['keyword'] = $keyword;
                        }
                    }

                    if (isset($parts[2]) && $parts[2] == 'id') {
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
                    }

                    break;
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
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return string
     */
    public function assemble(
        array $params = array(),
        array $options = array()
    )
    {
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