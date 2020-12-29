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
use Pi\Filter;
use Pi\Mvc\Controller\ActionController;

class ChannelController extends IndexController
{
    public function indexAction()
    {
        // Get info from url
        $module = $this->params('module');
        $uid    = $this->params('id');
        // Check id
        if (!isset($uid) && empty($uid)) {
            $uid = Pi::user()->getId();
        }
        // Get user info
        $user = Pi::api('channel', 'video')->user($uid);
        // Check tag
        if (empty($user)) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('This channel not exist.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // category list
        $categoriesJson = Pi::api('category', 'video')->categoryListJson();
        // Set header and title
        $title = sprintf(__('All videos from %s channel'), $user['name']);
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
            Pi::api('log', 'statistics')->save('video', 'channel', $uid);
        }

        // Set view
        $this->view()->headTitle($title);
        $this->view()->headDescription($title, 'set');
        $this->view()->headKeywords($seoKeywords, 'set');
        $this->view()->setTemplate('video-angular');
        $this->view()->assign('config', $config);
        $this->view()->assign('categoriesJson', $categoriesJson);
        $this->view()->assign('uid', $uid);
        $this->view()->assign('pageType', 'channel');
    }
}
