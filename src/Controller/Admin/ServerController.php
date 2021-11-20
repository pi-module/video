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

namespace Module\Video\Controller\Admin;

use Module\Video\Form\ServerFilter;
use Module\Video\Form\ServerForm;
use Pi;
use Pi\Mvc\Controller\ActionController;

class ServerController extends ActionController
{
    public function indexAction()
    {
        // Get info
        $list   = [];
        $order  = ['id DESC'];
        $select = $this->getModel('server')->select()->order($order);
        $rowSet = $this->getModel('server')->selectWith($select);

        // Make list
        foreach ($rowSet as $row) {
            $list[$row->id] = Pi::api('server', 'video')->canonizeServer($row);
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
                    $this->getModel('server')->update(['default' => 0]);
                    $values['default'] = 1;
                }

                // Set setting
                $setting = json_encode(
                    [
                        'uri'      => $values['uri'],
                        'token'    => $values['token'],
                        'username' => $values['username'],
                        'password' => $values['password'],
                    ]
                );
                $values['setting'] = Pi::service('encryption')->process($setting, 'encrypt');

                // Save values
                if (!empty($id)) {
                    $row = $this->getModel('server')->find($id);
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

                // Jump
                $message = __('Server data saved successfully.');
                $this->jump(['action' => 'index'], $message);
            }
        } else {
            if ($id) {
                $server = Pi::api('server', 'video')->getServer($id);
                $form->setData($server);
            }
        }
        // Set view
        $this->view()->setTemplate('server-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Manage server'));
    }

    public function typeAction()
    {
        // Get info
        $list   = [];
        $where  = ['type' => 'server'];
        $order  = ['id DESC'];
        $select = $this->getModel('type')->select()->where($where)->order($order);
        $rowSet = $this->getModel('type')->selectWith($select);

        // Make list
        foreach ($rowSet as $row) {
            $list[$row->id] = $row->toArray();
        }

        // Set view
        $this->view()->setTemplate('server-type');
        $this->view()->assign('list', $list);
    }
}
