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
namespace Module\Video\Controller\Admin;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Module\Guide\Form\SitemapForm;
use Module\Guide\Form\RegenerateImageForm;

class JsonController extends ActionController
{
    public function indexAction()
    {
        // Get info from url
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get config
        $links = array();

        $links['videoAll'] = Pi::url($this->url('video', array(
            'module' => $module,
            'controller' => 'json',
            'action' => 'videoAll',
            'update' => strtotime("11-12-10"),
            'password' => (!empty($config['json_password'])) ? $config['json_password'] : '',
        )));

        $links['videoCategory'] = Pi::url($this->url('video', array(
            'module' => $module,
            'controller' => 'json',
            'action' => 'videoCategory',
            'id' => 1,
            'update' => strtotime("11-12-10"),
            'password' => (!empty($config['json_password'])) ? $config['json_password'] : '',
        )));

        $links['videoSingle'] = Pi::url($this->url('video', array(
            'module' => $module,
            'controller' => 'json',
            'action' => 'videoSingle',
            'id' => 1,
            'password' => (!empty($config['json_password'])) ? $config['json_password'] : '',
        )));

        // Set template
        $this->view()->setTemplate('json-index');
        $this->view()->assign('links', $links);
    }
}