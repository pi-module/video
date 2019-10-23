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

use Exception;
use Pi\Application\Api\AbstractApi;
use Module\Video\Server\AbstractAdapter;

/*
 * Pi::api('serverService', 'video')->getUrl($type, $params);
 */

class ServerService extends AbstractApi
{
    /**
     * Service handler adapter
     *
     * @var AbstractAdapter
     */
    protected $adapter;

    /**
     * Set service adapter
     *
     * @param AbstractAdapter $adapter
     *
     * @return self
     */
    public function setAdapter(AbstractAdapter $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * Get service adapter
     *
     * @param string $name
     *
     * @return AbstractAdapter
     * @throws Exception
     */
    public function getAdapter($name)
    {
        $loadAdapter = function ($name) {
            $options = [];
            $class   = 'Module\Video\Server\\' . ucfirst($name);
            if (!class_exists($class)) {
                throw new Exception(sprintf('Class %s not found.', $class));
            }
            $adapter = new $class($options);

            return $adapter;
        };

        if (!$name) {
            if (!$this->adapter instanceof AbstractAdapter) {
                $this->adapter = $loadAdapter('file');
            }
            $result = $this->adapter;
        } else {
            $result = $loadAdapter($name);
        }

        return $result;
    }

    /**
     * Get server variables
     *
     * @param string $var
     *
     * @return mixed
     * @throws
     *
     */
    public function __get($var)
    {
        $result = $this->getAdapter()->{$var};

        return $result;
    }

    /**
     * Call APIs defined in server adapter
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     * @throws
     *
     */
    public function __call($method, $args)
    {
        $adapter = array_shift($args);
        return call_user_func_array([$this->getAdapter($adapter), $method], $args);
    }
}