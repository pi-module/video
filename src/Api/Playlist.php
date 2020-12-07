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

namespace Module\Video\Api;

use Pi;
use Pi\Application\Api\AbstractApi;

/*
 * Pi::api('playlist', 'video')->getPlaylist($id);
 * Pi::api('playlist', 'video')->canonizePlaylist($playlist);
 * Pi::api('playlist', 'video')->getPlaylistsForVideo($video);
 * Pi::api('playlist', 'video')->getVideosForPlaylists($playlist);
 * Pi::api('playlist', 'video')->setVideo($video, $playlists);
 */

class Playlist extends AbstractApi
{
    public function getPlaylist($id)
    {
        $playlist = Pi::model('playlist_inventory', $this->getModule())->find($id);
        return $this->canonizePlaylist($playlist);
    }

    public function canonizePlaylist($playlist)
    {
        // Check
        if (empty($playlist)) {
            return '';
        }

        // object to array
        $playlist = $playlist->toArray();

        // Set text
        $playlist['text_description'] = Pi::service('markup')->render($playlist['text_description'], 'html', 'html');

        // Set times
        $playlist['time_create_view'] = _date($playlist['time_create']);
        $playlist['time_update_view'] = _date($playlist['time_update']);

        // Set price view
        $playlist['sale_price_view'] = __('Free');
        if ($playlist['sale_price'] > 0) {
            $playlist['sale_price_view'] = Pi::api('api', 'video')->viewPrice($playlist['sale_price']);;
        }

        //
        $playlist['payUrl'] = '';
        if ($playlist['sale_price'] > 0) {
            $playlist['payUrl'] = Pi::url(
                Pi::service('url')->assemble(
                    'video', [
                        'module'     => 'video',
                        'controller' => 'order',
                        'action'     => 'playlist',
                        'id'         => $playlist['id'],
                    ]
                )
            );
        }

        // return
        return $playlist;
    }

    public function getPlaylistsForVideo($video)
    {
        $list   = [];
        $where  = ['id' => $video['playlist']];
        $select = Pi::model('playlist_inventory', $this->getModule())->select()->where($where);
        $rowSet = Pi::model('playlist_inventory', $this->getModule())->selectWith($select);
        foreach ($rowSet as $row) {
            $list[$row->id] = $this->canonizePlaylist($row);
        }

        return $list;
    }

    public function getVideosForPlaylists($playlist)
    {
        $list   = [];
        $where  = ['playlist_id' => $playlist['id']];
        $select = Pi::model('playlist_video', $this->getModule())->select()->where($where);
        $rowSet = Pi::model('playlist_video', $this->getModule())->selectWith($select);
        foreach ($rowSet as $row) {
            $list[$row->id] = $this->canonizePlaylist($row);
        }

        return $list;
    }

    public function setVideo($video, $playlists)
    {
        // Remove
        Pi::model('playlist_video', $this->getModule())->delete(['video_id' => $video]);

        // Add
        $playlists = json_decode($playlists, true);
        $playlists = is_array($playlists) ? $playlists : [$playlists];

        foreach ($playlists as $playlist) {
            // Set array
            $values = [
                'playlist_id' => $playlist,
                'video_id'    => $video,
            ];

            // Save
            $row = Pi::model('playlist_video', $this->getModule())->createRow();
            $row->assign($values);
            $row->save();
        }
    }
}
