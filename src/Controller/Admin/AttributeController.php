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
use Pi\Filter;
use Module\Video\Form\AttributeForm;
use Module\Video\Form\AttributeFilter;

class AttributeController extends ActionController
{
    public function indexAction()
    {
        // Get position list
        $position = Pi::api('attribute', 'video')->attributePositionForm();
        // Get info
        $select = $this->getModel('field')->select()->order(array('order ASC'));
        $rowset = $this->getModel('field')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $field[$row->position][$row->id] = $row->toArray();
            $field[$row->position][$row->id]['position_view'] = $position[$row->position];
        }
        // Set view
        $this->view()->setTemplate('attribute-index');
        $this->view()->assign('fields', $field);
        $this->view()->assign('positions', $position);
    }

    /**
     * Attribute Action
     */
    public function updateAction()
    {
        // Get id
        $id = $this->params('id');
        $type = $this->params('type');
        $options = array();
        // check type
        if (!in_array($type, array('text', 'link', 'currency', 'date', 'number', 'select', 'video', 'audio', 'file', 'checkbox'))) {
            $message = __('Attribute field type not set.');
            $url = array('action' => 'index');
            $this->jump($url, $message);
        }
        $options['type'] = $type;
        if ($id) {
            $attribute = $this->getModel('field')->find($id)->toArray();
            $attribute['category'] = Pi::api('attribute', 'video')->getCategory($attribute['id']);
            // Set value
            $value = json_decode($attribute['value'], true);
            $attribute['data'] = $value['data'];
            $attribute['default'] = $value['default'];
            $attribute['information'] = $value['information'];
        }
        // Set form
        $form = new AttributeForm('attribute', $options);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            // Set name
            $filter = new Filter\Slug;
            $data['name'] = $filter($data['name']);
            // Form filter
            $form->setInputFilter(new AttributeFilter($options));
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // Set value
                $value = array(
                    'data' => (isset($data['data'])) ? $data['data'] : '',
                    'default' => (isset($data['default'])) ? $data['default'] : '',
                    'information' => $data['information'],
                );
                $values['value'] = json_encode($value);
                // Set type
                $values['type'] = $type;
                // Set order
                if (empty($values['id'])) {
                    $columns = array('order');
                    $order = array('order DESC');
                    $select = $this->getModel('field')->select()->columns($columns)->order($order)->limit(1);
                    $values['order'] = $this->getModel('field')->selectWith($select)->current()->order + 1;
                }
                // Save values
                if (!empty($values['id'])) {
                    $row = $this->getModel('field')->find($values['id']);
                } else {
                    $row = $this->getModel('field')->createRow();
                }
                $row->assign($values);
                $row->save();
                //
                Pi::api('attribute', 'video')->setCategory($row->id, $data['category']);
                // Add log
                $operation = (empty($values['id'])) ? 'add' : 'edit';
                Pi::api('log', 'video')->addLog('attribute', $row->id, $operation);
                // Check it save or not
                $message = __('Attribute field data saved successfully.');
                $url = array('action' => 'index');
                $this->jump($url, $message);
            }
        } else {
            if ($id) {
                $form->setData($attribute);
            }
        }
        // Set view
        $this->view()->setTemplate('attribute-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', sprintf(__('Add attribute - type : %s'), $type));
    }

    public function sortAction()
    {
        $order = 1;
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            foreach ($data['mod'] as $id) {
                if ($id > 0) {
                    $row = $this->getModel('field')->find($id);
                    $row->order = $order;
                    $row->save();
                    $order++;
                }
            }
        }
        // Set view
        $this->view()->setTemplate(false);
    }

    public function deleteAction()
    {
        // Get information
        $this->view()->setTemplate(false);
        $id = $this->params('id');
        $row = $this->getModel('field')->find($id);
        if ($row) {
            // Remove all data
            $this->getModel('field_data')->delete(array('field' => $row->id));
            // Remove field
            $row->delete();
            $this->jump(array('action' => 'index'), __('Selected field delete'));
        } else {
            $this->jump(array('action' => 'index'), __('Please select field'));
        }
    }
}