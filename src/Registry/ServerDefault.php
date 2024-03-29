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

namespace Module\Video\Registry;

use Pi;
use Pi\Application\Registry\AbstractRegistry;

/*
 * Pi::registry('serverDefault', 'video')->clear();
 * Pi::registry('serverDefault', 'video')->read();
 */

/**
 * server default
 */
class ServerDefault extends AbstractRegistry
{
    /** @var string Module name */
    protected string $module = 'video';

    /**
     * {@inheritDoc}
     */
    protected function loadDynamic($options = []): array
    {
        $return = [];
        $where  = ['status' => 1, 'default' => 1];
        $limit  = 1;
        $order  = ['id DESC'];
        $select = Pi::model('server', $this->module)->select()->where($where)->limit($limit)->order($order);
        $row    = Pi::model('server', $this->module)->selectWith($select);
        if ($row) {
            $row    = $row->current();
            $return = Pi::api('server', 'video')->canonizeServer($row);
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     * @param array
     */
    public function read()
    {
        $options = [];
        return $this->loadData($options);
    }

    /**
     * {@inheritDoc}
     * @param bool $name
     */
    public function create(): bool
    {
        $this->clear('');
        $this->read();

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function setNamespace($meta = '')
    {
        return parent::setNamespace('');
    }

    /**
     * {@inheritDoc}
     */
    public function flush()
    {
        return $this->clear('');
    }
}
