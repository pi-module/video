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
use Module\Video\Form\ServerForm;
use Module\Video\Form\ServerFilter;
use Zend\Json\Json;

class ServerController extends ActionController
{
    public function indexAction()
    {
        // Get info
        $list = array();
        $order = array('id DESC');
        $select = $this->getModel('server')->select()->order($order);
        $rowset = $this->getModel('server')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = $row->toArray();
            switch ($row->type) {
                case 'file':
                    $list[$row->id]['type_view'] = __('File server');
                    break;

                case 'wowza':
                    $list[$row->id]['type_view'] = __('Wowza server');
                    break;

                case 'qmery':
                    $list[$row->id]['type_view'] = __('Qmery server');
                    break;
            }
        }
        // Set view
        $this->view()->setTemplate('server-index');
        $this->view()->assign('list', $list);
    }

    public function updateAction()
    {
        // Get id
        $id = $this->params('id');
        // Set form
        $form = new ServerForm('server');
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new ServerFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // Check default
                if ($values['default']) {
                    $this->getModel('server')->update(array('default' => 0));
                    $values['default'] = 1;
                }
                // Set setting
                $setting = array();
                $setting['qmery_upload_token'] = $values['qmery_upload_token'];
                $setting['qmery_update_token'] = $values['qmery_update_token'];
                $setting['qmery_group_id'] = $values['qmery_group_id'];
                $values['setting'] = Json::encode($setting);
                // Save values
                if (!empty($values['id'])) {
                    $row = $this->getModel('server')->find($values['id']);
                } else {
                    $row = $this->getModel('server')->createRow();
                }
                $row->assign($values);
                $row->save();
                // Clear registry
                Pi::registry('serverList', 'video')->clear();
                Pi::registry('serverDefault', 'video')->clear();
                // Add log
                $operation = (empty($values['id'])) ? 'add' : 'edit';
                Pi::api('log', 'video')->addLog('server', $row->id, $operation);
                $message = __('Server data saved successfully.');
                $this->jump(array('action' => 'index'), $message);
            }
        } else {
            if ($id) {
                $server = $this->getModel('server')->find($id)->toArray();
                $setting = Json::decode($server['setting'], true);
                $server = array_merge($server, $setting);
                $form->setData($server);
            }
        }
        // Set view
        $this->view()->setTemplate('server-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Manage server'));
    }
}