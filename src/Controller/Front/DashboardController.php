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

use Module\Video\Form\CustomerManageFilter;
use Module\Video\Form\CustomerManageForm;
use Module\Video\Form\CustomerAdditionalFilter;
use Module\Video\Form\CustomerAdditionalForm;
use Pi;
use Pi\Mvc\Controller\ActionController;
use Pi\Filter;
use Laminas\Math\Rand;

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

        // Check dashboard is active
        if (!$config['dashboard_active']) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate(__('Dashboard in inactive'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }




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

    public function listAction()
    {
        $authentication = Pi::api('authentication', 'company')->check();
        if (!$authentication['result']) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate($authentication['error']['message'], '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Get info from url
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check dashboard is active
        if (!$config['dashboard_active']) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate(__('Dashboard in inactive'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Set params
        $params = [
            'company_id' => $authentication['data']['company_id']
        ];

        // Get video list
        $videoList = Pi::api('company', 'video')->getVideoList($params);

        // Set header and title
        $title = __('List of Videos');

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
            Pi::api('log', 'statistics')->save('video', 'dashboard-list');
        }

        // Set view
        $this->view()->headTitle($title);
        $this->view()->headDescription($title, 'set');
        $this->view()->headKeywords($seoKeywords, 'set');
        $this->view()->setTemplate('dashboard-list');
        $this->view()->assign('authentication', $authentication);
        $this->view()->assign('config', $config);
        $this->view()->assign('title', $title);
        $this->view()->assign('videoList', $videoList);
    }

    public function manageAction()
    {
        $authentication = Pi::api('authentication', 'company')->check();
        if (!$authentication['result']) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate($authentication['error']['message'], '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Get info from url
        $module = $this->params('module');
        $slug = $this->params('slug');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check dashboard is active
        if (!$config['dashboard_active']) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate(__('Dashboard in inactive'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Get video
        $videoSingle = Pi::api('video', 'video')->getVideo($slug, 'slug');

        // Check company
        if ($videoSingle['company_id'] != $authentication['data']['company_id']) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate(__('This is not your video'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Get config
        $option = [
            'brand_system'     => $config['brand_system'],
            'sale_video'       => $config['sale_video'],
        ];

        // Set form
        $form = new CustomerManageForm('video', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new CustomerManageFilter($option));
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();

                // Category
                $values['category'] = json_encode([$values['category_main']]);

                // Set time_update
                $values['time_update'] = time();

                // Set status
                $values['status'] = 2;

                // Save values
                $row = $this->getModel('video')->find($videoSingle['id']);
                $row->assign($values);
                $row->save();

                // Jump
                $message = __('Video data saved successfully.');
                $this->jump(['action' => 'attribute', 'slug' => $row->slug], $message);
            }
        } else {
            $form->setData($videoSingle);
        }

        // Set header and title
        $title = __('Manage video');

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
            Pi::api('log', 'statistics')->save('video', 'dashboard-manage');
        }

        // Set view
        $this->view()->headTitle($title);
        $this->view()->headDescription($title, 'set');
        $this->view()->headKeywords($seoKeywords, 'set');
        $this->view()->setTemplate('dashboard-manage');
        $this->view()->assign('authentication', $authentication);
        $this->view()->assign('config', $config);
        $this->view()->assign('title', $title);
        $this->view()->assign('videoSingle', $videoSingle);
        $this->view()->assign('form', $form);
    }

    public function addAction()
    {
        $authentication = Pi::api('authentication', 'company')->check();
        if (!$authentication['result']) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate($authentication['error']['message'], '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Get info from url
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check dashboard is active
        if (!$config['dashboard_active']) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate(__('Dashboard in inactive'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Get config
        $option = [
            'brand_system'     => $config['brand_system'],
            'sale_video'       => $config['sale_video'],
        ];

        // Set form
        $form = new CustomerManageForm('video', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new CustomerManageFilter($option));
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();

                // Category
                $values['category'] = json_encode([$values['category_main']]);

                // Set time_update
                $values['time_create'] = time();
                $values['time_update'] = time();

                // Set status
                $values['status'] = 2;

                // Set slug
                $values['slug'] = Rand::getString(128, 'abcdefghijklmnopqrstuvwxyz123456789', true);

                // Set company id
                $values['company_id'] = $authentication['data']['company_id'];

                // Save values
                $row = $this->getModel('video')->createRow();
                $row->assign($values);
                $row->save();

                // Jump
                $message = __('Video data saved successfully.');
                $this->jump(['action' => 'attribute', 'slug' => $row->slug], $message);
            }
        }

        // Set header and title
        $title = __('Add new Video');

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
            Pi::api('log', 'statistics')->save('video', 'dashboard-manage');
        }

        // Set view
        $this->view()->headTitle($title);
        $this->view()->headDescription($title, 'set');
        $this->view()->headKeywords($seoKeywords, 'set');
        $this->view()->setTemplate('dashboard-add');
        $this->view()->assign('authentication', $authentication);
        $this->view()->assign('config', $config);
        $this->view()->assign('title', $title);
        $this->view()->assign('form', $form);
    }

    public function attributeAction()
    {
        $authentication = Pi::api('authentication', 'company')->check();
        if (!$authentication['result']) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate($authentication['error']['message'], '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Get info from url
        $module = $this->params('module');
        $slug = $this->params('slug');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check dashboard is active
        if (!$config['dashboard_active']) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate(__('Dashboard in inactive'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Get video
        $videoSingle = Pi::api('video', 'video')->getVideo($slug, 'slug');

        // Check company
        if ($videoSingle['company_id'] != $authentication['data']['company_id']) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate(__('This is not your video'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Get config
        $option = [];

        // Set form
        $form = new CustomerAdditionalForm('video', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new CustomerAdditionalFilter($option));
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();


                // Jump
                $message = __('Attribute data saved successfully.');
                $this->jump(['action' => 'list'], $message);
            }
        }

        // Set header and title
        $title = __('Attribute information');

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
            Pi::api('log', 'statistics')->save('video', 'dashboard-attribute');
        }

        // Set view
        $this->view()->headTitle($title);
        $this->view()->headDescription($title, 'set');
        $this->view()->headKeywords($seoKeywords, 'set');
        $this->view()->setTemplate('dashboard-attribute');
        $this->view()->assign('authentication', $authentication);
        $this->view()->assign('config', $config);
        $this->view()->assign('title', $title);
        $this->view()->assign('form', $form);
    }

    public function saleAction()
    {
        $authentication = Pi::api('authentication', 'company')->check();
        if (!$authentication['result']) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate($authentication['error']['message'], '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Get info from url
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check dashboard is active
        if (!$config['dashboard_active']) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate(__('Dashboard in inactive'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Set params
        $params = [
            'company_id' => $authentication['data']['company_id']
        ];

        // Get video list
        $videoIdList = Pi::api('company', 'video')->getVideoIdList($params);

        // Set params
        $params = [
            'videoList' => $videoIdList,
        ];

        // Get order list
        $orderList = Pi::api('company', 'video')->getOrders($params);

        // Set header and title
        $title = __('List of sales and order');

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
            Pi::api('log', 'statistics')->save('video', 'dashboard-manage');
        }

        // Set view
        $this->view()->headTitle($title);
        $this->view()->headDescription($title, 'set');
        $this->view()->headKeywords($seoKeywords, 'set');
        $this->view()->setTemplate('dashboard-sale');
        $this->view()->assign('authentication', $authentication);
        $this->view()->assign('config', $config);
        $this->view()->assign('title', $title);
        $this->view()->assign('orderList', $orderList);
    }
}