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
use Pi\Paginator\Paginator;
use Pi\File\Transfer\Upload;
use Module\Video\Form\VideoForm;
use Module\Video\Form\VideoFilter;
use Module\Video\Form\VideoAdditionalForm;
use Module\Video\Form\VideoAdditionalFilter;
use Module\Video\Form\VideoUploadForm;
use Module\Video\Form\VideoUploadFilter;
use Zend\Json\Json;
use Zend\Math\Rand;
use Zend\Db\Sql\Predicate\Expression;

class SubmitController extends IndexController
{
    protected $mediaPrefix = 'media-';

    public function indexAction()
    {
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
        // Check user is login or not
        Pi::service('authentication')->requireLogin();
        // check category
        $categoryCount = Pi::api('category', 'video')->categoryCount();
        if (!$categoryCount) {
            $message = __('No category set by admin');
            $this->jump(array('controller' => 'index', 'action' => 'index'), $message);
        }
        // Get server default
        $serverDefault = Pi::registry('serverDefault', 'video')->read();
        if (empty($serverDefault)) {
            $message = __('No server set by admin');
            $this->jump(array('controller' => 'index', 'action' => 'index'), $message);
        }
        // Set option
        $option = array();
        $option['side'] = 'front';
        // Set form
        $form = new VideoUploadForm('video', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $file = $this->request->getFiles()->toArray();
            $form->setInputFilter(new VideoUploadFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // upload video
                if (!empty($file['video']['name'])) {
                    // Set upload path
                    $values['video_path'] = sprintf('upload/video/file/%s/%s', date('Y'), date('m'));
                    $videoPath = Pi::path($values['video_path']);
                    // Upload
                    $uploader = new Upload;
                    $uploader->setDestination($videoPath);
                    $uploader->setRename($this->mediaPrefix . '%random%');
                    $uploader->setExtension($this->config('media_extension'));
                    $uploader->setSize($this->config('media_size'));
                    if ($uploader->isValid()) {
                        $uploader->receive();
                        // Get video_file
                        $values['video_file'] = $uploader->getUploaded('video');
                    } else {
                        $this->jump(array('action' => 'upload'), __('Problem in upload video. please try again'));
                    }
                } else {
                    $this->jump(array('action' => 'upload'), __('Problem in upload video. please try again'));
                }
                // Set time_create
                $values['time_create'] = time();
                // Set time_update
                $values['time_update'] = time();
                // Set uid
                $values['uid'] = Pi::user()->getId();
                // Set status
                $values['status'] = 2;
                // Set server
                $values['video_server'] = $serverDefault['id'];
                // Set type
                $extension = pathinfo($values['video_file'], PATHINFO_EXTENSION);
                switch ($extension) {
                    case 'mp3':
                        $values['video_type'] = 'audio';
                        $values['video_extension'] = 'mp3';
                        break;

                    case 'mp4':
                        $values['video_type'] = 'video';
                        $values['video_extension'] = 'mp4';
                        break;
                }
                // Save values
                $row = $this->getModel('video')->createRow();
                $row->assign($values);
                $row->save();
                // Send video to qmery
                if ($serverDefault['type'] == 'qmery') {
                    $qmery = Pi::api('qmery', 'video')->upload($row);
                    if (!$qmery['status']) {
                        $message = empty($qmery['message']) ?  __('Error to upload file on qmery server') : $qmery['message'];
                        $this->jump(array('controller' => 'index', 'action' => 'index'), $message);
                        exit();
                    } else {
                        return array(
                            'url' => Pi::url($this->url('', array('action' => 'update', 'id' => $row->id))),
                        );
                    }
                } else {
                    return array(
                        'url' => Pi::url($this->url('', array('action' => 'update', 'id' => $row->id))),
                    );
                }
            }
        } else {
            $video = array();
            $slug = Rand::getString(16, 'abcdefghijklmnopqrstuvwxyz123456789', true);
            $filter = new Filter\Slug;
            $video['slug'] = $filter($slug);
            $form->setData($video);
            // set nav
            $nav = array(
                'page' => 'upload',
            );
        }
        // Set header and title
        $title = __('Upload video');
        // Set seo_keywords
        $filter = new Filter\HeadKeywords;
        $filter->setOptions(array(
            'force_replace_space' => true
        ));
        $seoKeywords = $filter($title);
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
        // Get info from url
        $id = $this->params('id');
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
        // Check user is login or not
        Pi::service('authentication')->requireLogin();
        // check category
        $categoryCount = Pi::api('category', 'video')->categoryCount();
        if (!$categoryCount) {
            $message = __('No category set by admin');
            $this->jump(array('controller' => 'index', 'action' => 'index'), $message);
        }
        // Check id
        if ($id) {
            // Get video
            $video = Pi::api('video', 'video')->getVideo($id);
            // Check
            if (empty($video) || $video['status'] != 2 || $video['uid'] != Pi::user()->getId() || (time() - 3600) > $video['time_update']) {
                $message = __('Please submit video');
                $this->jump(array('action' => 'index'), $message);
            }
            // Set information
            if ($video['image']) {
                $video['thumbUrl'] = sprintf('upload/video/image/thumb/%s/%s', $video['path'], $video['image']);
                $option['thumbUrl'] = Pi::url($video['thumbUrl']);
                $option['removeUrl'] = $this->url('', array('action' => 'remove', 'id' => $video['id']));
            }
            $option['side'] = 'front';
            $option['video_type'] = $video['video_type'];
            $option['video_extension'] = $video['video_extension'];
            $option['video_size'] = $video['video_size'];
        } else {
            // Jump
            $message = __('Please submit video');
            $this->jump(array('action' => 'index'), $message);
        }
        // Set form
        $form = new VideoForm('video', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $file = $this->request->getFiles();
            $form->setInputFilter(new VideoFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // Tag
                if (!empty($values['tag'])) {
                    $tag = explode('|', $values['tag']);
                }
                // upload image
                if (!empty($file['image']['name'])) {
                    // Set upload path
                    $values['path'] = sprintf('%s/%s', date('Y'), date('m'));
                    $originalPath = Pi::path(sprintf('upload/%s/original/%s', $this->config('image_path'), $values['path']));
                    // Image name
                    $imageName = Pi::api('image', 'video')->rename($file['image']['name'], $this->mediaPrefix, $values['path']);
                    // Upload
                    $uploader = new Upload;
                    $uploader->setDestination($originalPath);
                    $uploader->setRename($imageName);
                    $uploader->setExtension($this->config('image_extension'));
                    $uploader->setSize($this->config('image_size'));
                    if ($uploader->isValid()) {
                        $uploader->receive();
                        // Get image name
                        $values['image'] = $uploader->getUploaded('image');
                        // process image
                        Pi::api('image', 'video')->process($values['image'], $values['path']);
                    } else {
                        $this->jump(array('action' => 'update'), __('Problem in upload image. please try again'));
                    }
                } elseif (!isset($values['image'])) {
                    $values['image'] = '';
                }
                // Category
                $values['category'] = Json::encode(array_unique($values['category']));
                // Set seo_title
                $title = ($values['seo_title']) ? $values['seo_title'] : $values['title'];
                $filter = new Filter\HeadTitle;
                $values['seo_title'] = $filter($title);
                // Set seo_keywords
                $keywords = ($values['seo_keywords']) ? $values['seo_keywords'] : $values['title'];
                $filter = new Filter\HeadKeywords;
                $filter->setOptions(array(
                    'force_replace_space' => (bool)$this->config('force_replace_space'),
                ));
                $values['seo_keywords'] = $filter($keywords);
                // Set seo_description
                $description = ($values['seo_description']) ? $values['seo_description'] : $values['title'];
                $filter = new Filter\HeadDescription;
                $values['seo_description'] = $filter($description);
                // Set time_update
                $values['time_update'] = time();
                // Save values
                $row = $this->getModel('video')->find($values['id']);
                $row->assign($values);
                $row->save();
                // Category
                // Category
                Pi::api('category', 'video')->setLink($row->id, $row->category, $row->time_create, $row->time_update, $row->status, $row->uid, $row->hits);
                // Tag
                if (isset($tag) && is_array($tag) && Pi::service('module')->isActive('tag')) {
                    Pi::service('tag')->update($module, $row->id, '', $tag);
                }
                // Add / Edit sitemap
                if (Pi::service('module')->isActive('sitemap')) {
                    // Set loc
                    $loc = Pi::url($this->url('video', array(
                        'module' => $module,
                        'controller' => 'watch',
                        'slug' => $values['slug']
                    )));
                    // Update sitemap
                    Pi::api('sitemap', 'sitemap')->singleLink($loc, $row->status, $module, 'video', $row->id);
                }
                // Jump
                $message = __('Video data saved successfully.');
                $this->jump(array('action' => 'additional', 'id' => $row->id), $message);
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
            // set nav
            $nav = array(
                'page' => 'update',
            );
        }
        // Set header and title
        $title = __('Edit basic information');
        // Set seo_keywords
        $filter = new Filter\HeadKeywords;
        $filter->setOptions(array(
            'force_replace_space' => true
        ));
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
        // Get id
        $id = $this->params('id');
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
        // Check user is login or not
        Pi::service('authentication')->requireLogin();
        // Find video
        if ($id) {
            // Get video
            $video = Pi::api('video', 'video')->getVideo($id);
            // Check
            if (empty($video) || $video['status'] != 2 || $video['uid'] != Pi::user()->getId() || (time() - 3600) > $video['time_update']) {
                $message = __('Please submit video');
                $this->jump(array('action' => 'index'), $message);
            }
        } else {
            // Jump
            $message = __('Please submit video');
            $this->jump(array('action' => 'index'), $message);
        }
        // Get attribute field
        $fields = Pi::api('attribute', 'video')->Get($video['category_main']);
        $option['field'] = $fields['attribute'];
        $option['side'] = 'front';
        // Check attribute is empty
        if (empty($fields['attribute'])) {
            $message = __('Video data saved successfully.');
            $this->jump(array('action' => 'finish', 'id' => $video['id']), $message);
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
                        $attribute[$field]['data'] = $values[$field];
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
                $this->jump(array('action' => 'finish', 'id' => $row->id), $message);
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
        $nav = array(
            'page' => 'additional',
        );
        // Set header and title
        $title = __('Edit additional information');
        // Set seo_keywords
        $filter = new Filter\HeadKeywords;
        $filter->setOptions(array(
            'force_replace_space' => true
        ));
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
        // Get info from url
        $id = $this->params('id');
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
        // Check user is login or not
        Pi::service('authentication')->requireLogin();
        // Check
        if ($id) {
            // Get video
            $video = Pi::api('video', 'video')->getVideo($id);
            // Check
            if (empty($video) || $video['status'] != 2 || $video['uid'] != Pi::user()->getId() || (time() - 3600) > $video['time_update']) {
                $message = __('Please submit video');
                $this->jump(array('action' => 'index'), $message);
            }
        } else {
            $message = __('Please submit video');
            $this->jump(array('action' => 'index'), $message);
        }
        // set nav
        $nav = array(
            'page' => 'finish',
        );
        // Set header and title
        $title = __('Watch submitted video');
        // Set seo_keywords
        $filter = new Filter\HeadKeywords;
        $filter->setOptions(array(
            'force_replace_space' => true
        ));
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