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
        $rowset = $this->getModel('server')->selectWith($select);

        // Make list
        foreach ($rowset as $row) {
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
                $setting             = [];
                $setting['token']    = $values['token'];
                $setting['username'] = $values['username'];
                $setting['password'] = $values['password'];
                $values['setting']   = json_encode($setting);

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
                $server  = Pi::api('server', 'video')->getServer($id);
                $form->setData($server);
            }
        }
        // Set view
        $this->view()->setTemplate('server-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Manage server'));
    }

    /* public function processingAction()
    {
        // Get info from url
        $server = $this->params('server');
        $type   = $this->params('type');
        $page   = $this->params('page', 1);

        // Server list
        $serverList = Pi::registry('serverList', 'video')->read();

        // Check type
        switch ($serverList[$server]['type']) {

            case 'qmery':

                // Check type
                switch ($type) {
                    case 'syncVideoList':
                        // Update video list
                        $count = Pi::api('qmery', 'video')->updateVideoList($serverList[$server], $page);

                        // Check count
                        if ($count > 0) {
                            $url = Pi::url(
                                $this->url(
                                    '', [
                                        'controller' => 'server',
                                        'action'     => 'processing',
                                        'server'     => $server,
                                        'type'       => 'syncVideoList',
                                        'page'       => $page + 1,
                                    ]
                                )
                            );
                        } else {
                            $message = __('Update information form qmery server finished');
                            $this->jump(['action' => 'index'], $message);
                        }
                        break;

                    case 'syncVideoSingle':
                        // Get video list
                        $count  = 0;
                        $limit  = 25;
                        $offset = (int)($page - 1) * $limit;
                        $order  = ['time_create DESC', 'id DESC'];
                        $where  = ['video_server' => $server];
                        $select = $this->getModel('video')->select()->where($where)->order($order)->offset($offset)->limit($limit);
                        $rowset = $this->getModel('video')->selectWith($select);

                        // Update viceo
                        foreach ($rowset as $row) {
                            $video = Pi::api('video', 'video')->canonizeVideo($row);
                            Pi::api('qmery', 'video')->updateVideo($video);
                            $count++;
                        }

                        // Check count
                        if ($count > 0) {
                            $url = Pi::url(
                                $this->url(
                                    '', [
                                        'controller' => 'server',
                                        'action'     => 'processing',
                                        'server'     => $server,
                                        'type'       => 'syncVideoSingle',
                                        'page'       => $page + 1,
                                    ]
                                )
                            );
                        } else {
                            $message = __('Update information form qmery server finished');
                            $this->jump(['action' => 'index'], $message);
                        }
                        break;
                }
                break;
        }

        // Set view
        $this->view()->setTemplate('server-processing');
        $this->view()->assign('url', $url);
    } */
}