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

use Module\Video\Form\AdminSearchFilter;
use Module\Video\Form\AdminSearchForm;
use Module\Video\Form\VideoAdditionalFilter;
use Module\Video\Form\VideoAdditionalForm;
use Module\Video\Form\VideoUrlFilter;
use Module\Video\Form\VideoUrlForm;
use Module\Video\Form\VideoFilter;
use Module\Video\Form\VideoForm;
use Module\Video\Form\VideoPutFilter;
use Module\Video\Form\VideoPutForm;
use Module\Video\Form\VideoUploadFilter;
use Module\Video\Form\VideoUploadForm;
use Pi;
use Pi\File\Transfer\Upload;
use Pi\Filter;
use Pi\Mvc\Controller\ActionController;
use Pi\Paginator\Paginator;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Math\Rand;

class VideoController extends ActionController
{
    protected $mediaPrefix = 'media-';

    public function indexAction()
    {
        // Get page
        $module      = $this->params('module');
        $page        = $this->params('page', 1);
        $status      = $this->params('status');
        $category    = $this->params('category');
        $brand       = $this->params('brand');
        $recommended = $this->params('recommended');
        $title       = $this->params('title');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Set info
        $offset = (int)($page - 1) * $this->config('admin_perpage');
        $order  = ['time_create DESC', 'id DESC'];
        $limit  = intval($this->config('admin_perpage'));
        $video  = [];

        // Set where
        $whereVideo = [];
        if (!empty($recommended)) {
            $whereVideo['recommended'] = 1;
        }
        if (!empty($brand)) {
            $whereVideo['brand'] = $brand;
        }
        if (!empty($category)) {
            $videoId    = [];
            $whereLink  = ['category' => $category];
            $selectLink = $this->getModel('link')->select()->where($whereLink);
            $rowLink    = $this->getModel('link')->selectWith($selectLink);
            foreach ($rowLink as $link) {
                $videoId[] = $link['video'];
            }
            if (!empty($videoId)) {
                $whereVideo['id'] = $videoId;
            } else {
                $whereVideo['id'] = 0;
            }
        }
        if (!empty($status) && in_array($status, [1, 2, 3, 4, 5])) {
            $whereVideo['status'] = $status;
        } else {
            $whereVideo['status'] = [1, 2, 3, 4];
        }
        if (!empty($title)) {
            // Set title
            if (Pi::service('module')->isActive('search') && isset($title) && !empty($title)) {
                $title = Pi::api('api', 'search')->parseQuery($title);
            } elseif (isset($title) && !empty($title)) {
                $title = _strip($title);
            }
            $title      = is_array($title) ? $title : [$title];
            $titleWhere = function ($where) use ($title) {
                $whereKey = clone $where;
                foreach ($title as $term) {
                    $whereKey->like('title', '%' . $term . '%')->and;
                }
                $where->andPredicate($whereKey);
            };
        }

        // Get list of video
        $select = $this->getModel('video')->select();
        if (!empty($title)) {
            $select->where($titleWhere);
        }
        $select->where($whereVideo)->order($order)->offset($offset)->limit($limit);
        $rowset = $this->getModel('video')->selectWith($select);

        // Make list
        foreach ($rowset as $row) {
            $video[$row->id] = Pi::api('video', 'video')->canonizeVideo($row);
        }

        // Set count
        $columnsLink = ['count' => new Expression('count(*)')];
        $select      = $this->getModel('video')->select();
        if (!empty($title)) {
            $select->where($titleWhere);
        }
        $select->where($whereVideo)->columns($columnsLink);
        $count = $this->getModel('video')->selectWith($select)->current()->count;

        // Set title
        $title = is_array($title) ? implode(' ', $title) : $title;

        // Set paginator
        $paginator = Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($this->config('admin_perpage'));
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(
            [
                'router' => $this->getEvent()->getRouter(),
                'route'  => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
                'params' => array_filter(
                    [
                        'module'      => $this->getModule(),
                        'controller'  => 'video',
                        'action'      => 'index',
                        'category'    => $category,
                        'brand'       => $brand,
                        'status'      => $status,
                        'title'       => $title,
                        'recommended' => $recommended,
                    ]
                ),
            ]
        );

        // Set form
        $values = [
            'title'       => $title,
            'category'    => $category,
            'brand'       => $brand,
            'status'      => $status,
            'recommended' => $recommended,
        ];

        // Set search form
        $form   = new AdminSearchForm('search');
        $form->setAttribute('action', $this->url('', ['action' => 'process']));
        $form->setData($values);

        // Server list
        $serverList = Pi::registry('serverList', 'video')->read();

        // Set view
        $this->view()->setTemplate('video-index');
        $this->view()->assign('list', $video);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('form', $form);
        $this->view()->assign('config', $config);
        $this->view()->assign('serverList', $serverList);
    }

    public function processAction()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form = new AdminSearchForm('search');
            $form->setInputFilter(new AdminSearchFilter());
            $form->setData($data);
            if ($form->isValid()) {
                $values  = $form->getData();
                $message = __('View filtered videos');
                $url     = [
                    'action'      => 'index',
                    'title'       => $values['title'],
                    'category'    => $values['category'],
                    'brand'       => $values['brand'],
                    'status'      => $values['status'],
                    'recommended' => $values['recommended'],
                ];
            } else {
                $message = __('Not valid');
                $url     = [
                    'action' => 'index',
                ];
            }
        } else {
            $message = __('Not set');
            $url     = [
                'action' => 'index',
            ];
        }
        return $this->jump($url, $message);
    }

    public function urlAction()
    {
        // check category
        $categoryCount = Pi::api('category', 'video')->categoryCount();
        if (!$categoryCount) {
            return $this->redirect()->toRoute(
                '', [
                'controller' => 'category',
                'action'     => 'update',
            ]
            );
        }

        // Server list
        $serverList = Pi::registry('serverList', 'video')->read();
        if (empty($serverList)) {
            return $this->redirect()->toRoute(
                '', [
                'controller' => 'server',
                'action'     => 'update',
            ]
            );
        }

        // Get info from url
        $module = $this->params('module');
        $server = $this->params('server');

        // Check server
        if (!isset($serverList[$server]) || empty($serverList[$server])) {
            $message = __('please select true server');
            $this->jump(['action' => 'index'], $message, 'error');
        }

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Set option
        $option = [];

        // Set form
        $form = new VideoUrlForm('video', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new VideoUrlFilter($option));
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();

                // Set time_create
                $values['time_create'] = time();

                // Set time_update
                $values['time_update'] = time();

                // Set uid
                $values['uid'] = Pi::user()->getId();

                // Set status
                $values['status'] = 2;

                // Set server
                $values['video_server'] = $serverList[$server]['id'];

                // Save values
                $row = $this->getModel('video')->createRow();
                $row->assign($values);
                $row->save();

                // Jump
                $message = __('Video source url add successfully. Please complete update');
                $this->jump(['action' => 'update', 'id' => $row->id], $message);
             }
        } else {
            $video = [];

            // Set slug
            $slug          = Rand::getString(16, 'abcdefghijklmnopqrstuvwxyz123456789', true);
            $filter        = new Filter\Slug;
            $video['slug'] = $filter($slug);

            // Set form
            $form->setData($video);

            // set nav
            $nav = [
                'page' => 'url',
            ];
        }

        // Set view
        $this->view()->setTemplate('video-link');
        $this->view()->assign('form', $form);
        $this->view()->assign('config', $config);
        $this->view()->assign('title', __('Manage video link'));
        $this->view()->assign('nav', $nav);
        $this->view()->assign('video', $video);
    }

    public function putAction()
    {
        // check category
        $categoryCount = Pi::api('category', 'video')->categoryCount();
        if (!$categoryCount) {
            return $this->redirect()->toRoute(
                '', [
                'controller' => 'category',
                'action'     => 'update',
            ]
            );
        }

        // Server list
        $serverList = Pi::registry('serverList', 'video')->read();
        if (empty($serverList)) {
            return $this->redirect()->toRoute(
                '', [
                'controller' => 'server',
                'action'     => 'update',
            ]
            );
        }

        // Get info from url
        $id     = $this->params('id');
        $module = $this->params('module');
        $server = $this->params('server');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check and get video
        if ($id) {
            $video  = Pi::api('video', 'video')->getVideo($id);
            $server = $video['video_server'];
        }

        // Check server
        if (!isset($serverList[$server]) || empty($serverList[$server])) {
            $message = __('please select true server');
            $this->jump(['action' => 'index'], $message, 'error');
        }

        // Set option
        $option = [
            'server' => $serverList[$server],
        ];

        // Set form
        $form = new VideoPutForm('video', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new VideoPutFilter($option));
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();

                // Set time
                if (empty($id)) {
                    $values['time_create'] = time();
                }
                $values['time_update'] = time();

                // Set uid
                $values['uid'] = Pi::user()->getId();

                // Set status
                if (empty($id)) {
                    $values['status'] = 2;
                }

                // Set server
                $values['video_server'] = $serverList[$server]['id'];
                if (!$values['video_server']) {
                    $serverDefault          = Pi::registry('serverDefault', 'video')->read();
                    $values['video_server'] = $serverDefault['id'];
                }

                // Save values
                if ($id) {
                    $row = $this->getModel('video')->find($id);
                } else {
                    $row = $this->getModel('video')->createRow();
                }
                $row->assign($values);
                $row->save();

                // Jump
                $message = __('Video source url add successfully. Please complete update');
                $this->jump(['action' => 'update', 'id' => $row->id], $message);
            }
        } else {
            if (!$id) {
                $video = [];

                // Set sluf
                $slug          = Rand::getString(16, 'abcdefghijklmnopqrstuvwxyz123456789', true);
                $filter        = new Filter\Slug;
                $video['slug'] = $filter($slug);

                // Set
                if (!empty($config['link_path'])) {
                    $video['video_path'] = $config['link_path'];
                }

                $form->setData($video);
            }

            // set nav
            $nav = [
                'page' => 'link',
            ];
        }

        // Set view
        $this->view()->setTemplate('video-link');
        $this->view()->assign('form', $form);
        $this->view()->assign('config', $config);
        $this->view()->assign('title', __('Manage video link'));
        $this->view()->assign('nav', $nav);
        $this->view()->assign('video', $video);
    }

    public function uploadAction()
    {
        // check category
        $categoryCount = Pi::api('category', 'video')->categoryCount();
        if (!$categoryCount) {
            return $this->redirect()->toRoute(
                '', [
                    'controller' => 'category',
                    'action'     => 'update',
                ]
            );
        }

        // Server list
        $serverList = Pi::registry('serverList', 'video')->read();
        if (empty($serverList)) {
            return $this->redirect()->toRoute(
                '', [
                    'controller' => 'server',
                    'action'     => 'update',
                ]
            );
        }

        // Get info from url
        $module = $this->params('module');
        $server = $this->params('server');

        // Check server
        if (!isset($serverList[$server]) || empty($serverList[$server])) {
            $message = __('please select true server');
            $this->jump(['action' => 'index'], $message, 'error');
        }

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
                    $videoPath            = Pi::path($values['video_path']);
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
                        $this->jump(['action' => 'upload'], __('Problem in upload video. please try again'));
                    }
                } else {
                    $this->jump(['action' => 'upload'], __('Problem in upload video. please try again'));
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
                if (isset($server) and intval($server) > 0) {
                    $values['video_server'] = $serverList[$server]['id'];
                    $serverType             = $serverList[$server]['type'];
                } else {
                    $serverDefault          = Pi::registry('serverDefault', 'video')->read();
                    $values['video_server'] = $serverDefault['id'];
                    $serverType             = $serverDefault['type'];
                }

                // Save values
                $row = $this->getModel('video')->createRow();
                $row->assign($values);
                $row->save();

                // Send video
                    return [
                        'url' => Pi::url($this->url('', ['action' => 'update', 'id' => $row->id])),
                    ];
            }
        } else {
            $video = [];

            // Set slug
            $slug          = Rand::getString(16, 'abcdefghijklmnopqrstuvwxyz123456789', true);
            $filter        = new Filter\Slug;
            $video['slug'] = $filter($slug);

            // Set form
            $form->setData($video);

            // set nav
            $nav = [
                'page' => 'upload',
            ];
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
            return $this->redirect()->toRoute(
                '', [
                'controller' => 'category',
                'action'     => 'update',
            ]
            );
        }

        // Server list
        $serverList = Pi::registry('serverList', 'video')->read();
        if (empty($serverList)) {
            return $this->redirect()->toRoute(
                '', [
                'controller' => 'server',
                'action'     => 'update',
            ]
            );
        }

        // Get info from url
        $id     = $this->params('id');
        $module = $this->params('module');

        // Get config
        $config                 = Pi::service('registry')->config->read($module);
        $option                 = [];
        $option['brand_system'] = $config['brand_system'];

        // Find video
        if ($id) {
            $video = Pi::api('video', 'video')->getVideo($id);
            if ($video['image']) {
                $option['thumbUrl']  = Pi::url($video['thumbUrl']);
                $option['removeUrl'] = $this->url('', ['action' => 'remove', 'id' => $video['id']]);
            }
            $option['side']            = 'admin';
            $option['video_size']      = $video['video_size'];
            $option['sale_video']      = $config['sale_video'];
        } else {
            // Jump
            $message = __('Please select video');
            $this->jump(['action' => 'index'], $message);
        }

        // Set form
        $form = new VideoForm('video', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $file = $this->request->getFiles();

            // Set slug
            $slug         = ($data['slug']) ? $data['slug'] : $data['title'];
            $filter       = new Filter\Slug;
            $data['slug'] = $filter($slug);

            // Form filter
            $form->setInputFilter(new VideoFilter($option));
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
                    $originalPath   = Pi::path(sprintf('upload/%s/original/%s', $this->config('image_path'), $values['path']));
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
                        $this->jump(['action' => 'update'], __('Problem in upload image. please try again'));
                    }
                } elseif (!isset($values['image'])) {
                    $values['image'] = '';
                }

                // Category
                $values['category'][] = $values['category_main'];
                $values['category']   = json_encode(array_unique($values['category']));

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
                $row = $this->getModel('video')->find($id);
                $row->assign($values);
                $row->save();

                // Category
                Pi::api('category', 'video')->setLink(
                    $row->id, $row->category, $row->time_create, $row->time_update, $row->status, $row->uid, $row->hits, $row->recommended
                );

                // Tag
                if (isset($tag) && is_array($tag) && Pi::service('module')->isActive('tag')) {
                    Pi::service('tag')->update($module, $row->id, '', $tag);
                }

                // Add / Edit sitemap
                if (Pi::service('module')->isActive('sitemap')) {
                    // Set loc
                    $loc = Pi::url(
                        $this->url(
                            'video', [
                            'module'     => $module,
                            'controller' => 'watch',
                            'slug'       => $values['slug'],
                        ]
                        )
                    );
                    // Update sitemap
                    Pi::api('sitemap', 'sitemap')->singleLink($loc, $row->status, $module, 'video', $row->id);
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

            // set nav
            $nav = [
                'page' => 'update',
            ];
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
        $id     = $this->params('id');
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Find video
        if ($id) {
            $video = Pi::api('video', 'video')->getVideo($id);
        } else {
            $this->jump(['action' => 'index'], __('Please select video'));
        }
        // Get attribute field
        $fields          = Pi::api('attribute', 'video')->Get($video['category_main']);
        $option['field'] = $fields['attribute'];
        $option['side']  = 'admin';
        // Check attribute is empty
        if (empty($fields['attribute'])) {
            $message = __('Video data saved successfully.');
            $this->jump(['action' => 'watch', 'id' => $video['id']], $message);
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
                $row = $this->getModel('video')->find($id);
                $row->assign($values);
                $row->save();
                // Set attribute
                if (isset($attribute) && !empty($attribute)) {
                    Pi::api('attribute', 'video')->Set($attribute, $row->id);
                }
                // Jump
                $message = __('Video data saved successfully.');
                $this->jump(['action' => 'watch', 'id' => $row->id], $message);
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
        // Set view
        $this->view()->setTemplate('video-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('video', $video);
        $this->view()->assign('config', $config);
        $this->view()->assign('title', __('Video additional information'));
        $this->view()->assign('nav', $nav);
    }

    public function watchAction()
    {
        // Get info from url
        $id     = $this->params('id');
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get video
        if ($id) {
            $video = Pi::api('video', 'video')->getVideo($id);
        } else {
            $message = __('Please select video');
            $this->jump(['action' => 'index'], $message);
        }
        // set nav
        $nav = [
            'page' => 'view',
        ];
        // Set view
        $this->view()->setTemplate('video-watch');
        $this->view()->assign('title', __('View video'));
        $this->view()->assign('video', $video);
        $this->view()->assign('config', $config);
        $this->view()->assign('nav', $nav);
    }

    public function recommendAction()
    {
        // Get id and recommended
        $id          = $this->params('id');
        $recommended = $this->params('recommended');
        $return      = [];
        // set video
        $video = $this->getModel('video')->find($id);
        // Check
        if ($video && in_array($recommended, [0, 1])) {
            // Accept
            $video->recommended = $recommended;
            // Save
            if ($video->save()) {
                $return['message']     = sprintf(__('%s set recommended successfully'), $video->title);
                $return['ajaxstatus']  = 1;
                $return['id']          = $video->id;
                $return['recommended'] = $video->recommended;
                // Update recommended
                $this->getModel('link')->update(
                    ['recommended' => $video->recommended],
                    ['video' => $video->id]
                );
                // Add log
                Pi::api('log', 'video')->addLog('video', $video->id, 'recommend');
            } else {
                $return['message']     = sprintf(__('Error in set recommended for %s video'), $video->title);
                $return['ajaxstatus']  = 0;
                $return['id']          = 0;
                $return['recommended'] = $video->recommended;
            }
        } else {
            $return['message']     = __('Please select video');
            $return['ajaxstatus']  = 0;
            $return['id']          = 0;
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
            $video->path  = '';
            // Save
            if ($video->save()) {
                $message = sprintf(__('Image of %s removed'), $video->title);
                $status  = 1;
            } else {
                $message = __('Image not remove');
                $status  = 0;
            }
        } else {
            $message = __('Please select video');
            $status  = 0;
        }
        return [
            'status'  => $status,
            'message' => $message,
        ];
    }

    public function deleteAction()
    {
        // Get information
        $this->view()->setTemplate(false);
        $module = $this->params('module');
        $id     = $this->params('id');
        $row    = $this->getModel('video')->find($id);
        if ($row) {
            $row->status = 5;
            $row->save();
            // update links
            $this->getModel('link')->update(['status' => $row->status], ['video' => $row->id]);
            // Remove sitemap
            if (Pi::service('module')->isActive('sitemap')) {
                $loc = Pi::url(
                    $this->url(
                        'video', [
                        'module'     => $module,
                        'controller' => 'video',
                        'slug'       => $row->slug,
                    ]
                    )
                );
                Pi::api('sitemap', 'sitemap')->remove($loc);
            }
            // Remove page
            $this->jump(['action' => 'index'], __('This video deleted'));
        }
        $this->jump(['action' => 'index'], __('Please select video'));
    }
}