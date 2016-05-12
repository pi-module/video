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
namespace Module\Video\Api;

use Pi;
use Pi\Application\Api\AbstractApi;
use Zend\Json\Json;

/*
 * Pi::api('channel', 'video')->user($uid);
 */

class Channel extends AbstractApi
{
    public function user($uid)
    {
        // Get user
        $parameters = array('id', 'identity', 'name', 'email', 'gender', 'birthdate');
        $user = Pi::api('user', 'user')->get(
            $uid,
            $parameters,
            true,
            true
        );
        return $user;
    }
}