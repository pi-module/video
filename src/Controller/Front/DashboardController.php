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

namespace Module\Video\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Pi\Filter;

class DashboardController extends ActionController
{
    public function indexAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();

        // Get info from url
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);


        // Set header and title
        $title = __('Manage videos');

        // Set seo_keywords
        $filter = new Filter\HeadKeywords;
        $filter->setOptions(
            [
                'force_replace_space' => true,
            ]
        );
        $seoKeywords = $filter($title);

        // Save statistics
        if (Pi::service('module')->isActive('statistics')) {
            Pi::api('log', 'statistics')->save('video', 'dashboard-index');
        }

        // Set view
        $this->view()->headTitle($title);
        $this->view()->headDescription($title, 'set');
        $this->view()->headKeywords($seoKeywords, 'set');
        $this->view()->setTemplate('dashboard-index');
        $this->view()->assign('config', $config);
        $this->view()->assign('title', $title);
    }

    public function purchasedAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();

        // Get info from url
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Set params
        $params = ['uid' => Pi::user()->getId()];

        // Get video ides
        $videoIds = Pi::api('order', 'video')->purchasedVideos($params);

        // Get video lists
        $videoList = [];
        if (!empty($videoIds)) {
            $videoList = Pi::api('video', 'video')->getListFromIdLight($videoIds);
        }

        d($videoList);

        // Set header and title
        $title = __('Purchased Videos');

        // Set seo_keywords
        $filter = new Filter\HeadKeywords;
        $filter->setOptions(
            [
                'force_replace_space' => true,
            ]
        );
        $seoKeywords = $filter($title);

        // Save statistics
        if (Pi::service('module')->isActive('statistics')) {
            Pi::api('log', 'statistics')->save('video', 'dashboard-purchased');
        }

        // Set view
        $this->view()->headTitle($title);
        $this->view()->headDescription($title, 'set');
        $this->view()->headKeywords($seoKeywords, 'set');
        $this->view()->setTemplate('dashboard-purchased');
        $this->view()->assign('config', $config);
        $this->view()->assign('title', $title);
        $this->view()->assign('videoList', $videoList);
    }
}