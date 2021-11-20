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
        $setting = Pi::service('encryption')->process($server['setting'], 'decrypt');
        $setting = json_decode($setting, true);
        $server  = array_merge($server, $setting);

        // Set type view
        $serverType          = Pi::registry('serverType', 'video')->read();
        $server['type_view'] = $serverType[$server['type']]['title'];

        // Update methods
        $server['updateMethod'] = Pi::api('serverService', 'video')->updateMethod($server['type']);

        // return
        return $server;
    }
}
