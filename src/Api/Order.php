<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <hossein@azizabadi.com>
 */

namespace Module\Video\Api;

use Pi;
use Pi\Application\Api\AbstractApi;

/*
 * Pi::api('order', 'video')->
 */

class Order extends AbstractApi
{
    /*
     * Start Order module needed functions
     */
    public function checkProduct($id, $type = null)
    {
        // Get video
        $video = Pi::api('video', 'video')->getVideo($id);

        // Check
        if ($video) {
            return true;
        } else {
            return false;
        }
    }

    public function getInstallmentDueDate($cart = [], $composition = [100])
    {
        return null;
    }

    public function getInstallmentComposition($extra = [])
    {
        return [100];
    }

    public function getProductDetails($id, $extra)
    {

        if (isset($extra['list_id']) && intval($extra['list_id']) > 0) {
            // Get event
            $playlist = Pi::api('playlist', 'video')->getPlaylist($id);

            // Set result
            return [
                'title'      => $playlist['title'],
                'thumbUrl'   => '',
                'productUrl' => $playlist['videoUrl'],
            ];

        } elseif (isset($extra['list_id']) && intval($extra['list_id']) > 0) {
            // Get event
            $video = Pi::api('video', 'video')->getVideo($id);

            // Set result
            return [
                'title'      => $video['title'],
                'thumbUrl'   => $video['thumbUrl'],
                'productUrl' => $video['videoUrl'],
            ];
        }
    }

    public function postPaymentUpdate($order, $detail)
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());

        // Update company
        if (empty($detail)) {
            return false;
        }

        // Get basket
        $detail = array_shift($detail);

        // Check product_type
        switch ($detail['product_type']) {
            case 'video':
                // Get video
                $video = Pi::api('video', 'video')->getVideo(intval($detail['product']));

                // Check video
                if (!$video || empty($video) || $video['status'] == 0) {
                    Pi::engine()->application()->getResponse()->setStatusCode(403);
                    die();
                }

                // Set Access
                Pi::api('video', 'video')->setAccess($video, $order['uid']);

                // Set url
                return $video['videoUrl'];
                break;

            case 'playlist':
                // Get playlist
                $playlist = Pi::api('playlist', 'video')->getPlaylist(intval($detail['product']));

                // Check video
                if (!$playlist || empty($playlist) || $playlist['status'] == 0) {
                    Pi::engine()->application()->getResponse()->setStatusCode(403);
                    die();
                }

                // Get videos
                $videoList = Pi::api('playlist', 'video')->getVideosForPlaylists($playlist);

                foreach ($videoList as $videoSingle) {
                    // Set video
                    $video = [
                        'id' => $videoSingle['video_id']
                    ];

                    // Set Access
                    Pi::api('video', 'video')->setAccess($video, $order['uid']);
                }

                // set back url
                $playlist['back_url'] = isset($playlist['back_url']) && !empty($playlist['back_url']) ? $playlist['back_url'] : Pi::url('video');

                // Set url
                return $playlist['back_url'];
                break;
        }
    }

    public function createExtraDetailForProduct($values)
    {
        return json_encode(
            [
                'item' => $values['module_item'],
            ]
        );
    }

    public function getExtraFieldsFormForOrder()
    {
        return [];
    }

    public function isAlwaysAvailable($order)
    {
        return [
            'status' => true,
        ];
    }

    public function showInInvoice($order, $product, $third = false)
    {
        return false;
    }

    public function postCancelUpdate($order, $detail)
    {
        return true;
    }
}
