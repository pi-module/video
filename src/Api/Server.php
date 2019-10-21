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
 * Pi::api('server', 'video')->getServer($id);
 * Pi::api('server', 'video')->canonizeServer($server);
 */

class Server extends AbstractApi
{
    public function getServer($id)
    {
        $server = Pi::model('server', $this->getModule())->find($id);
        $server = $this->canonizeServer($server);
        return $server;
    }

    public function canonizeServer($server)
    {
        // Check
        if (empty($server)) {
            return '';
        }

        // object to array
        $server = $server->toArray();

        // Get setting
        $setting = json_decode($server['setting'], true);
        $server  = array_merge($server, $setting);

        // Set type view
        switch ($server['type']) {
            case 'file':
                $server['type_view'] = __('File server');
                break;

            case 'wowza':
                $server['type_view'] = __('Wowza');
                break;

            case 'nginx':
                $server['type_view'] = __('Nginx');
                break;

            case 'mistserver':
                $server['type_view'] = __('MistServer');
                break;
        }

        // Update methods
        $server['updateMethod'] = Pi::api('serverService', 'video')->updateMethod($server['type']);

        // return
        return $server;
    }
}