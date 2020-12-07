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

class OrderController extends IndexController
{
    public function indexAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();

        // Get info from url
        $slug   = $this->params('slug');
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check sale video active
        if ($config['sale_video'] != 'single') {
            $this->getResponse()->setStatusCode(403);
            $this->terminate(__('Sale video option not enable !'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Find video
        $video = Pi::api('video', 'video')->getVideo($slug, 'slug');

        // Check video
        if (!$video || $video['status'] != 1) {
            $this->jump(['', 'module' => $module, 'controller' => 'index'], __('The video not found.'), 'error');
        }

        // Check video access
        $access = Pi::api('video', 'video')->getAccess($video);

        // Set pay url
        if ($access) {
            $this->jump($video['videoUrl'], __('You have access to watch this video'));
        }

        // Set single product
        $service = [
            'product'        => $video['id'],
            'product_price'  => $video['sale_price'],
            'discount_price' => 0,
            'shipping_price' => 0,
            'packing_price'  => 0,
            'vat_price'      => 0,
            'number'         => 1,
            'title'          => $video['title'],
            'extra'          => json_encode(
                [
                    'type_payment' => 'onetime',
                    'video_id'     => $video['id'],
                    'list_id'      => 0,
                ]
            ),
        ];

        // Set order array
        $order = [
            'module_name'    => $module,
            'module_table'   => 'video',
            'type_payment'   => 'onetime',
            'type_commodity' => 'service',
            'total_discount' => 0,
            'total_shipping' => 0,
            'total_packing'  => 0,
            'total_setup'    => 0,
            'total_vat'      => 0,
            'can_pay'        => 1,
            'product'        => [
                $video['id'] => $service,
            ],
        ];

        // Set and go to order
        $url = Pi::api('order', 'order')->setOrderInfo($order);
        Pi::service('url')->redirect($url);
    }

    public function playlistAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();

        // Get info from url
        $id   = $this->params('id');
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check sale video active
        if ($config['sale_video'] != 'single') {
            $this->getResponse()->setStatusCode(403);
            $this->terminate(__('Sale video option not enable !'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        $playlist = Pi::api('playlist', 'video')->getPlaylist($id);

        // Check video
        if (!$playlist || $playlist['status'] != 1) {
            $this->jump(['', 'module' => $module, 'controller' => 'index'], __('The playlist not found.'), 'error');
        }

        // Set single product
        $service = [
            'product'        => $playlist['id'],
            'product_price'  => $playlist['sale_price'],
            'discount_price' => 0,
            'shipping_price' => 0,
            'packing_price'  => 0,
            'vat_price'      => 0,
            'number'         => 1,
            'title'          => $playlist['title'],
            'extra'          => json_encode(
                [
                    'type_payment' => 'onetime',
                    'video_id'     => 0,
                    'list_id'      => $playlist['id'],
                ]
            ),
        ];

        // Set order array
        $order = [
            'module_name'    => $module,
            'module_table'   => 'playlist',
            'type_payment'   => 'onetime',
            'type_commodity' => 'service',
            'total_discount' => 0,
            'total_shipping' => 0,
            'total_packing'  => 0,
            'total_setup'    => 0,
            'total_vat'      => 0,
            'can_pay'        => 1,
            'product'        => [
                $playlist['id'] => $service,
            ],
        ];


        // Set and go to order
        $url = Pi::api('order', 'order')->setOrderInfo($order);
        Pi::service('url')->redirect($url);
    }
}