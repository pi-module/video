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

use Module\Video\Form\VideoAdditionalFilter;
use Module\Video\Form\VideoAdditionalForm;
use Module\Video\Form\VideoFilter;
use Module\Video\Form\VideoForm;
use Module\Video\Form\VideoUploadFilter;
use Module\Video\Form\VideoUploadForm;
use Pi;
use Pi\File\Transfer\Upload;
use Pi\Filter;
use Pi\Mvc\Controller\ActionController;
use Laminas\Math\Rand;

class SubmitController extends ActionController
{
    protected $mediaPrefix = 'media-';

    public function indexAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();

        // Get info from url
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check active
        if (!$config['user_submit']) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate(__('Submit video by users inactive'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // check category
        $categoryCount = Pi::api('category', 'video')->categoryCount();
        if (!$categoryCount) {
            $message = __('No category set by admin');
            $this->jump(['controller' => 'index', 'action' => 'index'], $message);
        }

        // Get server default
        $serverDefault = Pi::registry('serverDefault', 'video')->read();
        if (empty($serverDefault)) {
            $message = __('No server set by admin');
            $this->jump(['controller' => 'index', 'action' => 'index'], $message);
        }

        // Set option
        $option = [
            'side' => 'front',
        ];

        // Set form
        $form = new VideoUploadForm('video', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $file = $this->request->getFiles();
            $form->setInputFilter(new VideoUploadFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();

                // upload video
                if (!empty($file['video']['name'])) {
                    // Set upload path
                    $values['video_path'] = sprintf('upload/video/file/%s/%s', date('Y'), date('m'));
                    $streamPath           = Pi::path($values['video_path']);

                    // Upload
                    $uploader = new Upload;
                    $uploader->setDestination($streamPath);
                    $uploader->setRename($this->mediaPrefix . '%random%');
                    $uploader->setExtension($this->config('media_extension'));
                    $uploader->setSize($this->config('media_size'));
                    if ($uploader->isValid()) {
                        $uploader->receive();
                        // Get video_file
                        $values['video_file'] = $uploader->getUploaded('video');
                    } else {
                        $this->jump(['action' => 'index'], __('Problem in upload video. please try again'));
                    }
                } else {
                    $this->jump(['action' => 'index'], __('Problem in upload video. please try again'));
                }

                // Set server
                $values['video_server'] = $serverDefault['id'];
                $serverType             = $serverDefault['type'];

                // Set time
                $values['time_create'] = time();
                $values['time_update'] = time();

                // Set uid
                $values['uid'] = Pi::user()->getId();

                // Set status
                $values['status'] = 2;

                // Check status, need convert and set hls videos to stream servers
                $values['video_status'] = 0;

                // Save values
                $row = $this->getModel('video')->createRow();
                $row->assign($values);
                $row->save();

                // Send video
                return [
                    'url' => Pi::url($this->url('', ['action' => 'update', 'id' => $row->id])),
                ];
            }
        }

        $filter = new Filter\Slug;

        $slug = Rand::getString(16, 'abcdefghijklmnopqrstuvwxyz123456789', true);
        $slug = $filter($slug);

        // Set video
        $video = [
            'slug' => $slug,
        ];
        $form->setData($video);

        // set nav
        $nav = [
            'page' => 'upload',
        ];

        // Set header and title
        $title = __('Upload video');

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
            Pi::api('log', 'statistics')->save('video', 'submit');
        }

        // Set view
        $this->view()->headTitle($title);
        $this->view()->headDescription($title, 'set');
        $this->view()->headKeywords($seoKeywords, 'set');
        $this->view()->setTemplate('submit-index');
        $this->view()->assign('form', $form);
        $this->view()->assign('config', $config);
        $this->view()->assign('title', __('Upload new video'));
        $this->view()->assign('nav', $nav);
    }

    public function updateAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();

        // Get info from url
        $id     = $this->params('id');
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check active
        if (!$config['user_submit']) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate(__('Submit video by users inactive'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // check category
        $categoryCount = Pi::api('category', 'video')->categoryCount();
        if (!$categoryCount) {
            $message = __('No category set by admin');
            $this->jump(['controller' => 'index', 'action' => 'index'], $message);
        }

        // Check id
        if (!$id) {
            // Jump
            $message = __('Please submit video');
            $this->jump(['action' => 'index'], $message);
        }

        // Get video
        $video = Pi::api('video', 'video')->getVideo($id);

        // Check
        if (
            empty($video)
            || $video['status'] != 2
            || $video['uid'] != Pi::user()->getId()
            || (time() - 3600) > $video['time_update']
        ) {
            $message = __('Please submit video');
            $this->jump(['action' => 'index'], $message);
        }

        // Get config
        $option = [
            'id'               => $id,
            'brand_system'     => 0,
            'sale_video'       => 0,
            'dashboard_active' => 0,
            'side'             => 'front',
        ];

        // Set form
        $form = new VideoForm('video', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new VideoFilter($option));
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();

                // Tag
                if (!empty($values['tag'])) {
                    $tag = explode('|', $values['tag']);
                }

                // Category
                $values['category'] = json_encode(array_unique($values['category']));

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

                // Set time_update
                $values['time_update'] = time();

                // Save values
                $row = $this->getModel('video')->find($values['id']);
                $row->assign($values);
                $row->save();

                // Category
                Pi::api('category', 'video')->setLink(
                    $row->id,
                    $row->category,
                    $row->time_create,
                    $row->time_update,
                    $row->status,
                    $row->uid,
                    $row->hits,
                    $row->recommended,
                    $row->company_id
                );

                // Tag
                if (isset($tag) && is_array($tag) && Pi::service('module')->isActive('tag')) {
                    Pi::service('tag')->update($module, $row->id, '', $tag);
                }

                // Jump
                $message = __('Video data saved successfully.');
                $this->jump(['action' => 'additional', 'id' => $row->id], $message);
            }
        } else {
            // Get tag list
            if (Pi::service('module')->isActive('tag')) {
                $tag = Pi::service('tag')->get($module, $video['id'], '');
                if (is_array($tag)) {
                    $video['tag'] = implode('|', $tag);
                }
            }

            // Set form data
            $form->setData($video);
        }

        // set nav
        $nav = [
            'page' => 'update',
        ];

        // Set header and title
        $title = __('Edit basic information');

        // Set seo_keywords
        $filter = new Filter\HeadKeywords;
        $filter->setOptions(
            [
                'force_replace_space' => true,
            ]
        );
        $seoKeywords = $filter($title);

        // Set view
        $this->view()->headTitle($title);
        $this->view()->headDescription($title, 'set');
        $this->view()->headKeywords($seoKeywords, 'set');
        $this->view()->setTemplate('submit-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('video', $video);
        $this->view()->assign('config', $config);
        $this->view()->assign('title', __('Video information'));
        $this->view()->assign('nav', $nav);
    }

    public function additionalAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();

        // Get id
        $id     = $this->params('id');
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check active
        if (!$config['user_submit']) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate(__('Submit video by users inactive'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Find video
        if (!$id) {
            // Jump
            $message = __('Please submit video');
            $this->jump(['action' => 'index'], $message);
        }

        // Get video
        $video = Pi::api('video', 'video')->getVideo($id);

        // Check
        if (
            empty($video)
            || $video['status'] != 2
            || $video['uid'] != Pi::user()->getId()
            || (time() - 3600) > $video['time_update']
        ) {
            $message = __('Please submit video');
            $this->jump(['action' => 'index'], $message);
        }

        // Get attribute field
        $fields = Pi::api('attribute', 'video')->Get($video['category_main']);

        // Set option
        $option = [
            'field' => $fields['attribute'],
            'side'  => 'front',
        ];

        // Check attribute is empty
        if (empty($fields['attribute'])) {
            $message = __('Video data saved successfully.');
            $this->jump(['action' => 'finish', 'id' => $video['id']], $message);
        }

        // Check post
        if ($this->request->isPost()) {
            $data = $this->request->getPost();

            // Set form
            $form = new VideoAdditionalForm('video', $option);
            $form->setAttribute('enctype', 'multipart/form-data');
            $form->setInputFilter(new VideoAdditionalFilter($option));
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();

                // Set attribute data array
                if (!empty($fields['field'])) {
                    foreach ($fields['field'] as $field) {
                        $attribute[$field]['field'] = $field;
                        $attribute[$field]['data']  = $values[$field];
                    }
                }

                // Set time
                $values['time_update'] = time();

                // Save
                $row = $this->getModel('video')->find($values['id']);
                $row->assign($values);
                $row->save();

                // Set attribute
                if (isset($attribute) && !empty($attribute)) {
                    Pi::api('attribute', 'video')->Set($attribute, $row->id);
                }

                // Jump
                $message = __('Video data saved successfully.');
                $this->jump(['action' => 'finish', 'id' => $row->id], $message);
            }
        } else {

            // Get attribute
            $video = Pi::api('attribute', 'video')->Form($video);

            // Set form
            $form = new VideoAdditionalForm('video', $option);
            $form->setAttribute('enctype', 'multipart/form-data');
            $form->setData($video);
        }

        // set nav
        $nav = [
            'page' => 'additional',
        ];

        // Set header and title
        $title = __('Edit additional information');

        // Set seo_keywords
        $filter = new Filter\HeadKeywords;
        $filter->setOptions(
            [
                'force_replace_space' => true,
            ]
        );
        $seoKeywords = $filter($title);

        // Set view
        $this->view()->headTitle($title);
        $this->view()->headDescription($title, 'set');
        $this->view()->headKeywords($seoKeywords, 'set');
        $this->view()->setTemplate('submit-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('video', $video);
        $this->view()->assign('config', $config);
        $this->view()->assign('title', __('Video additional information'));
        $this->view()->assign('nav', $nav);
    }

    public function finishAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();

        // Get info from url
        $id     = $this->params('id');
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check active
        if (!$config['user_submit']) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate(__('Submit video by users inactive'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Check
        if (!$id) {
            $message = __('Please submit video');
            $this->jump(['action' => 'index'], $message);
        }

        // Get video
        $video = Pi::api('video', 'video')->getVideo($id);

        // Check
        if (
            empty($video)
            || $video['status'] != 2
            || $video['uid'] != Pi::user()->getId()
            || (time() - 3600) > $video['time_update']
        ) {
            $message = __('Please submit video');
            $this->jump(['action' => 'index'], $message);
        }

        // set nav
        $nav = [
            'page' => 'finish',
        ];

        // Set header and title
        $title = __('Watch submitted video');

        // Set seo_keywords
        $filter = new Filter\HeadKeywords;
        $filter->setOptions(
            [
                'force_replace_space' => true,
            ]
        );
        $seoKeywords = $filter($title);

        // Set view
        $this->view()->headTitle($title);
        $this->view()->headDescription($title, 'set');
        $this->view()->headKeywords($seoKeywords, 'set');
        $this->view()->setTemplate('submit-finish');
        $this->view()->assign('video', $video);
        $this->view()->assign('config', $config);
        $this->view()->assign('title', $title);
        $this->view()->assign('nav', $nav);
    }
}
