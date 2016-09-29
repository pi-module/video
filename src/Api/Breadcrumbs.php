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
namespace Module\Video\Api;

use Pi;
use Pi\Application\Api\AbstractBreadcrumbs;

class Breadcrumbs extends AbstractBreadcrumbs
{
    /**
     * {@inheritDoc}
     */
    public function load()
    {
        // Get params
        $params = Pi::service('url')->getRouteMatch()->getParams();
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Check breadcrumbs
        if ($config['view_breadcrumbs']) {
            // Set module link
            $moduleData = Pi::registry('module')->read($this->getModule());
            // Make tree
            if (!empty($params['controller']) && $params['controller'] != 'index') {
                // Set index
                $result = array(
                    array(
                        'label' => $moduleData['title'],
                        'href' => Pi::url(Pi::service('url')->assemble('video', array(
                            'module' => $this->getModule(),
                        ))),
                    ),
                );
                // Set
                switch ($params['controller']) {
                    case 'category':
                        switch ($params['action']) {
                            case 'list':
                                $result[] = array(
                                    'label' => __('Category list'),
                                );
                                break;

                            case 'index':
                                /* $result[] = array(
                                    'label' => __('Category list'),
                                    'href' => Pi::url(Pi::service('url')->assemble('video', array(
                                        'module' => $this->getModule(),
                                        'controller' => 'category',
                                    ))),
                                ); */

                                $category = Pi::api('category', 'video')->getCategory($params['slug'], 'slug');
                                $result = $this->makeCategoryList($category['parent'], $result);
                                $result[] = array(
                                    'label' => $category['title'],
                                );
                                break;
                        }
                        break;

                    case 'watch':
                        $video = Pi::api('video', 'video')->getVideoLight($params['slug'], 'slug');
                        // Category list
                        /* $result[] = array(
                            'label' => __('Category list'),
                            'href' => Pi::url(Pi::service('url')->assemble('video', array(
                                'module' => $this->getModule(),
                                'controller' => 'category',
                            ))),
                        ); */
                        // Check have category_main
                        if ($video['category_main'] > 0) {
                            $category = Pi::api('category', 'video')->getCategory($video['category_main']);
                            $result = $this->makeCategoryList($category['parent'], $result);
                            $result[] = array(
                                'label' => $category['title'],
                                'href' => $category['categoryUrl'],
                            );
                        }
                        // Set video title
                        $result[] = array(
                            'label' => $video['title'],
                        );
                        break;

                    case 'tag':
                        if (!empty($params['slug'])) {
                            $result[] = array(
                                'label' => __('Tag list'),
                                'href' => Pi::url(Pi::service('url')->assemble('video', array(
                                    'controller' => 'tag',
                                    'action' => 'index',
                                ))),
                            );
                            $result[] = array(
                                'label' => $params['slug'],
                            );
                        } else {
                            $result[] = array(
                                'label' => __('Tag list'),
                            );
                        }
                        break;

                    case 'submit':
                        switch ($params['action']) {
                            case 'index':
                                $result[] = array(
                                    'label' => __('Upload new video'),
                                );
                                break;

                            case 'update':
                                $result[] = array(
                                    'label' => __('Edit basic information'),
                                );
                                break;

                            case 'additional':
                                $result[] = array(
                                    'label' => __('Edit additional information'),
                                );
                                break;

                            case 'finish':
                                $result[] = array(
                                    'label' => __('Watch video'),
                                );
                                break;
                        }
                        break;
                }
            } else {
                $result = array(
                    array(
                        'label' => $moduleData['title'],
                    ),
                );
            }
            return $result;
        } else {
            return '';
        }
    }

    public function makeCategoryList($parent, $result)
    {
        if ($parent > 0) {
            $category = Pi::api('category', 'video')->getCategory($parent);
            $result = $this->makeCategoryList($category['parent'], $result);
            $result[] = array(
                'label' => $category['title'],
                'href' => $category['categoryUrl'],
            );

        }
        return $result;
    }
}