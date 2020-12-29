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

use Module\Video\Form\PlaylistFilter;
use Module\Video\Form\PlaylistForm;
use Pi;
use Pi\Filter;
use Pi\Mvc\Controller\ActionController;
use Pi\Paginator\Paginator;
use Laminas\Db\Sql\Predicate\Expression;

class PlaylistController extends ActionController
{
    public function indexAction()
    {
        // Get page
        $module = $this->params('module');
        $page   = $this->params('page', 1);

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Get info
        $list   = [];
        $order  = ['time_create DESC', 'id DESC'];
        $limit  = intval($this->config('admin_perpage'));
        $offset = (int)($page - 1) * $limit;

        // Select
        $select = $this->getModel('playlist_inventory')->select()->order($order)->offset($offset)->limit($limit);
        $rowSet = $this->getModel('playlist_inventory')->selectWith($select);

        // Make list
        foreach ($rowSet as $row) {
            $list[$row->id] = Pi::api('playlist', 'video')->canonizePlaylist($row);
        }

        // Set log
        $count  = ['count' => new Expression('count(*)')];
        $select = $this->getModel('playlist_inventory')->select()->columns($count);
        $count  = $this->getModel('playlist_inventory')->selectWith($select)->current()->count;

        // Set paginator
        $paginator = Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(
            [
                'router' => $this->getEvent()->getRouter(),
                'route'  => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
                'params' => array_filter(
                    [
                        'module'     => $this->getModule(),
                        'controller' => 'playlist',
                        'action'     => 'index',
                    ]
                ),
            ]
        );

        // Set view
        $this->view()->setTemplate('playlist-index');
        $this->view()->assign('config', $config);
        $this->view()->assign('list', $list);
        $this->view()->assign('paginator', $paginator);
    }

    public function updateAction()
    {
        // Get id
        $id     = $this->params('id');
        $option = [];

        // Set form
        $form = new PlaylistForm('playlist', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new PlaylistFilter($option));
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();

                // Set seo_title
                $title               = ($values['seo_title']) ? $values['seo_title'] : $values['title'];
                $filter              = new Filter\HeadTitle;
                $values['seo_title'] = $filter($title);

                // Set seo_keywords
                $keywords = ($values['seo_keywords']) ? $values['seo_keywords'] : $values['title'];
                $filter   = new Filter\HeadKeywords;
                $filter->setOptions(
                    [
                        'force_replace_space' => (bool)$this->config('force_replace_space'),
                    ]
                );
                $values['seo_keywords'] = $filter($keywords);

                // Set seo_description
                $description               = ($values['seo_description']) ? $values['seo_description'] : $values['title'];
                $filter                    = new Filter\HeadDescription;
                $values['seo_description'] = $filter($description);

                // Set time
                if (empty($values['id'])) {
                    $values['time_create'] = time();
                    $values['uid']         = Pi::user()->getId();
                }
                $values['time_update'] = time();

                // Save values
                if (isset($id) && !empty($id)) {
                    $row = $this->getModel('playlist_inventory')->find($id);
                } else {
                    $row = $this->getModel('playlist_inventory')->createRow();
                }
                $row->assign($values);
                $row->save();

                // Clean cache
                Pi::registry('playlist', 'video')->clear();

                // Jump
                $message = __('Playlist data saved successfully.');
                $this->jump(['action' => 'index'], $message);
            }
        } else {
            if ($id) {
                $playlist = $this->getModel('playlist_inventory')->find($id)->toArray();
                $form->setData($playlist);
            }
        }

        // Set view
        $this->view()->setTemplate('playlist-update');
        $this->view()->assign('form', $form);
    }
}
