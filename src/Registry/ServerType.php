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
 * Pi::registry('serverType', 'video')->clear();
 * Pi::registry('serverType', 'video')->read();
 */

/**
 * server type
 */
class ServerType extends AbstractRegistry
{
    /** @var string Module name */
    protected string $module = 'video';

    /**
     * {@inheritDoc}
     */
    protected function loadDynamic($options = []): array
    {
        $return = [];
        $where  = ['type' => 'server'];
        $select = Pi::model('type', $this->module)->select()->where($where);
        $rowSet = Pi::model('type', $this->module)->selectWith($select);
        foreach ($rowSet as $row) {
            $return[$row->name] = $row->toArray();
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
