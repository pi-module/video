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

use Pi;
use Pi\Mvc\Controller\ActionController;

class WatchController extends IndexController
{
    public function indexAction()
    {
        // Get info from url
        $slug   = $this->params('slug');
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Find video
        $video = Pi::api('video', 'video')->getVideo($slug, 'slug');

        // Check video
        if (!$video || $video['status'] != 1) {
            $this->jump(['', 'module' => $module, 'controller' => 'index'], __('The video not found.'), 'error');
        }

        // Update Hits
        $this->getModel('link')->increment('hits', ['video' => $video['id']]);
        $this->getModel('video')->increment('hits', ['id' => $video['id']]);

        // Get attribute
        if ($video['attribute']) {
            $attribute = Pi::api('attribute', 'video')->Video($video['id'], $video['category_main']);
            $this->view()->assign('attribute', $attribute);
        }

        // Tag
        if ($config['view_tag'] && Pi::service('module')->isActive('tag')) {
            $tag = Pi::service('tag')->get($module, $video['id'], '');
            $this->view()->assign('tag', $tag);
        }

        // Set vote
        if ($config['vote_bar'] && Pi::service('module')->isActive('vote')) {
            $vote['point']  = $video['point'];
            $vote['count']  = $video['count'];
            $vote['item']   = $video['id'];
            $vote['table']  = 'video';
            $vote['module'] = $module;
            $vote['type']   = 'plus';
            $this->view()->assign('vote', $vote);
        }

        // favourite
        if ($config['favourite_bar'] && Pi::service('module')->isActive('favourite')) {
            $favourite['is']     = Pi::api('favourite', 'favourite')->loadFavourite($module, 'video', $video['id']);
            $favourite['item']   = $video['id'];
            $favourite['table']  = 'video';
            $favourite['module'] = $module;
            $this->view()->assign('favourite', $favourite);
        }

        // Get new items in category
        if ($config['view_related'] && $config['view_related_number'] > 0) {
            $where        = [
                'status'     => 1,
                'category'   => $video['category'],
                'video != ?' => $video['id'],
            ];
            $videoRelated = $this->videoList($where, $config['view_related_number']);
            $this->view()->assign('videoRelated', $videoRelated);
        }

        // Check video access
        $access = Pi::api('video', 'video')->getAccess($video);

        // Set pay url
        if (!$access) {
            $video['payUrl'] = Pi::api('video', 'video')->getPayUrl($video);
        }

        // Set submitter
        $submitter           = Pi::api('channel', 'video')->user($video['uid']);
        $submitter['avatar'] = Pi::avatar()->get(
            $video['uid'],
            'small',
            [
                'alt'   => _escape($submitter['name']),
                'class' => 'rounded-circle',
            ]
        );

        // Set main category info
        if ($config['view_description_video']) {
            $categorySingle = Pi::api('category', 'video')->getCategory($video['category_main']);
            $this->view()->assign('categorySingle', $categorySingle);
        }

        // Get playlist
        $playlists = [];
        if (isset($video['playlist']) && !empty($video['playlist'])) {
            $playlists = Pi::api('playlist', 'video')->getPlaylistsForVideo($video);
        }

        // Save statistics
        if (Pi::service('module')->isActive('statistics')) {
            Pi::api('log', 'statistics')->save('video', 'watch', $video['id']);
        }

        // Set view
        $this->view()->headTitle($video['seo_title']);
        $this->view()->headDescription($video['seo_description'], 'set');
        $this->view()->headKeywords($video['seo_keywords'], 'set');
        $this->view()->setTemplate('video-watch');
        $this->view()->assign('videoSingle', $video);
        $this->view()->assign('categoryItem', $video['categories']);
        $this->view()->assign('config', $config);
        $this->view()->assign('submitter', $submitter);
        $this->view()->assign('access', $access);
        $this->view()->assign('playlists', $playlists);
    }
}
