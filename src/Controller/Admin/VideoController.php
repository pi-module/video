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
use Module\Video\Form\AdminSearchForm;
use Module\Video\Form\AdminSearchFilter;
use Zend\Json\Json;
use Zend\Math\Rand;
use Zend\Db\Sql\Predicate\Expression;

class VideoController extends ActionController
{
    protected $mediaPrefix = 'media-';

    public function indexAction()
    {
        // Get page
        $page = $this->params('page', 1);
        $status = $this->params('status');
        $category = $this->params('category');
        $title = $this->params('title');
        // Set info
        $offset = (int)($page - 1) * $this->config('admin_perpage');
        $order = array('time_create DESC', 'id DESC');
        $limit = intval($this->config('admin_perpage'));
        $where = array();
        $video = array();
        // Get
        if (!empty($title)) {
            $where['title LIKE ?'] = '%' . $title . '%';
        }
        // Get list of video
        $select = $this->getModel('video')->select()->where($where)->order($order)->offset($offset)->limit($limit);
        $rowset = $this->getModel('video')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $video[$row->id] = Pi::api('video', 'video')->canonizeVideo($row);
        }
        // Set count
        $columnsLink = array('count' => new Expression('count(*)'));
        $select = $this->getModel('video')->select()->where($where)->columns($columnsLink);
        $count = $this->getModel('video')->selectWith($select)->current()->count;
        // Set paginator
        $paginator = Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($this->config('admin_perpage'));
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(array(
            'router' => $this->getEvent()->getRouter(),
            'route' => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'params' => array_filter(array(
                'module' => $this->getModule(),
                'controller' => 'video',
                'action' => 'index',
                'category' => $category,
                'status' => $status,
                'title' => $title,
            )),
        ));
        // Set form
        $values = array(
            'title' => $title,
        );
        $form = new AdminSearchForm('search');
        $form->setAttribute('action', $this->url('', array('action' => 'process')));
        $form->setData($values);
        // Set view
        $this->view()->setTemplate('video-index');
        $this->view()->assign('list', $video);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('form', $form);
    }

    public function processAction()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form = new AdminSearchForm('search');
            $form->setInputFilter(new AdminSearchFilter());
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                $message = __('View filtered videos');
                $url = array(
                    'action' => 'index',
                    'title' => $values['title'],
                );
            } else {
                $message = __('Not valid');
                $url = array(
                    'action' => 'index',
                );
            }
        } else {
            $message = __('Not set');
            $url = array(
                'action' => 'index',
            );
        }
        return $this->jump($url, $message);
    }

    public function uploadAction()
    {
        // check category
        $categoryCount = Pi::api('category', 'video')->categoryCount();
        if (!$categoryCount) {
            return $this->redirect()->toRoute('', array(
                'controller' => 'category',
                'action' => 'update'
            ));
        }
        // Get info from url
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Set form
        $form = new VideoUploadForm('video');
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
                        // Set video_url
                        $values['video_url'] = Pi::url();
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
                // Jump
                // $message = __('Video file upload successfully. Please complete update');
                // $this->jump(array('action' => 'update', 'id' => $row->id), $message);
                // result
                return array(
                    'url' => Pi::url($this->url('', array('action' => 'update', 'id' => $row->id))),
                );
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
        // Set view
        $this->view()->setTemplate('video-upload');
        $this->view()->assign('form', $form);
        $this->view()->assign('config', $config);
        $this->view()->assign('title', __('Upload new video'));
        $this->view()->assign('nav', $nav);
    }

    public function updateAction()
    {
        // check category
        $categoryCount = Pi::api('category', 'video')->categoryCount();
        if (!$categoryCount) {
            return $this->redirect()->toRoute('', array(
                'controller' => 'category',
                'action' => 'update'
            ));
        }
        // Get info from url
        $id = $this->params('id');
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        $option = array();
        // Find video
        if ($id) {
            $video = $this->getModel('video')->find($id)->toArray();
            $video['category'] = Json::decode($video['category']);
            if ($video['image']) {
                $video['thumbUrl'] = sprintf('upload/video/image/thumb/%s/%s', $video['path'], $video['image']);
                $option['thumbUrl'] = Pi::url($video['thumbUrl']);
                $option['removeUrl'] = $this->url('', array('action' => 'remove', 'id' => $video['id']));
            }
            $option['side'] = 'admin';
            $option['video_type'] = $video['video_type'];
            $option['video_extension'] = $video['video_extension'];
            $option['video_size'] = $video['video_size'];
        } else {
            // Jump
            $message = __('Please select video');
            $this->jump(array('action' => 'index'), $message);
        }
        // Set form
        $form = new VideoForm('video', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $file = $this->request->getFiles();
            // Set slug
            $slug = ($data['slug']) ? $data['slug'] : $data['title'];
            $filter = new Filter\Slug;
            $data['slug'] = $filter($slug);
            // Form filter
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
        // Set view
        $this->view()->setTemplate('video-update');
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
        // Find video
        if ($id) {
            $video = $this->getModel('video')->find($id)->toArray();
        } else {
            $this->jump(array('action' => 'index'), __('Please select video'));
        }
        // Get attribute field
        $fields = Pi::api('attribute', 'video')->Get($video['category_main']);
        $option['field'] = $fields['attribute'];
        $option['side'] = 'admin';
        // Check attribute is empty
        if (empty($fields['attribute'])) {
            $message = __('Video data saved successfully.');
            $this->jump(array('action' => 'view', 'id' => $video['id']), $message);
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
                $this->jump(array('action' => 'view', 'id' => $row->id), $message);
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
        // Set view
        $this->view()->setTemplate('video-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('video', $video);
        $this->view()->assign('config', $config);
        $this->view()->assign('title', __('Video additional information'));
        $this->view()->assign('nav', $nav);
    }

    public function viewAction()
    {
        // Get info from url
        $id = $this->params('id');
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get video
        if ($id) {
            $video = Pi::api('video', 'video')->getVideo($id);
        } else {
            $message = __('Please select video');
            $this->jump(array('action' => 'index'), $message);
        }
        // set nav
        $nav = array(
            'page' => 'view',
        );
        // Set view
        $this->view()->setTemplate('video-view');
        $this->view()->assign('title', __('View video'));
        $this->view()->assign('video', $video);
        $this->view()->assign('config', $config);
        $this->view()->assign('nav', $nav);
    }

    public function recommendAction()
    {
        // Get id and recommended
        $id = $this->params('id');
        $recommended = $this->params('recommended');
        $return = array();
        // set video
        $video = $this->getModel('video')->find($id);
        // Check
        if ($video && in_array($recommended, array(0, 1))) {
            // Accept
            $video->recommended = $recommended;
            // Save
            if ($video->save()) {
                $return['message'] = sprintf(__('%s set recommended successfully'), $video->title);
                $return['ajaxstatus'] = 1;
                $return['id'] = $video->id;
                $return['recommended'] = $video->recommended;
                // Add log
                Pi::api('log', 'video')->addLog('video', $video->id, 'recommend');
            } else {
                $return['message'] = sprintf(__('Error in set recommended for %s video'), $video->title);
                $return['ajaxstatus'] = 0;
                $return['id'] = 0;
                $return['recommended'] = $video->recommended;
            }
        } else {
            $return['message'] = __('Please select video');
            $return['ajaxstatus'] = 0;
            $return['id'] = 0;
            $return['recommended'] = 0;
        }
        return $return;
    }

    public function removeAction()
    {
        // Get id and status
        $id = $this->params('id');
        // set video
        $video = $this->getModel('video')->find($id);
        // Check
        if ($video && !empty($id)) {
            // remove file
            /* $files = array(
                Pi::path(sprintf('upload/%s/original/%s/%s', $this->config('image_path'), $video->path, $video->image)),
                Pi::path(sprintf('upload/%s/large/%s/%s', $this->config('image_path'), $video->path, $video->image)),
                Pi::path(sprintf('upload/%s/medium/%s/%s', $this->config('image_path'), $video->path, $video->image)),
                Pi::path(sprintf('upload/%s/thumb/%s/%s', $this->config('image_path'), $video->path, $video->image)),
            );
            Pi::service('file')->remove($files); */
            // clear DB
            $video->image = '';
            $video->path = '';
            // Save
            if ($video->save()) {
                $message = sprintf(__('Image of %s removed'), $video->title);
                $status = 1;
            } else {
                $message = __('Image not remove');
                $status = 0;
            }
        } else {
            $message = __('Please select video');
            $status = 0;
        }
        return array(
            'status' => $status,
            'message' => $message,
        );
    }
}