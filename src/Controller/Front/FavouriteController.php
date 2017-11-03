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

namespace Module\Video\Controller\Front;

use Pi;
use Pi\Filter;
use Pi\Mvc\Controller\ActionController;

class FavouriteController extends IndexController
{
    public function indexAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();
        // Check tag
        if (!Pi::service('module')->isActive('favourite')) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate(__('Favourite module not installed.'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get info from url
        $module = $this->params('module');
        $uid = Pi::user()->getId();
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // category list
        $categoriesJson = Pi::api('category', 'video')->categoryListJson();
        // Set header and title
        $title = __('All favourite videos by you');
        // Set seo_keywords
        $filter = new Filter\HeadKeywords;
        $filter->setOptions([
            'force_replace_space' => true,
        ]);
        $seoKeywords = $filter($title);
        // load language
        Pi::service('i18n')->load(['module/user', 'default']);
        // Set view
        $this->view()->headTitle($title);
        $this->view()->headDescription($title, 'set');
        $this->view()->headKeywords($seoKeywords, 'set');
        $this->view()->setTemplate('video-angular');
        $this->view()->assign('config', $config);
        $this->view()->assign('categoriesJson', $categoriesJson);
        $this->view()->assign('uid', $uid);
        $this->view()->assign('pageType', 'favourite');
    }
}