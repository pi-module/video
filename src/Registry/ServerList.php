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
namespace Module\Video\Registry;

use Pi;
use Pi\Application\Registry\AbstractRegistry;

/**
 * server list
 */
class ServerList extends AbstractRegistry
{
    /** @var string Module name */
    protected $module = 'video';

    /**
     * {@inheritDoc}
     */
    protected function loadDynamic($options = array())
    {
        $return = array();
        $where = array('status' => 1);
        $select = Pi::model('server', $this->module)->select()->where($where);
        $rowset = Pi::model('server', $this->module)->selectWith($select);
        foreach ($rowset as $row) {
            $return[$row->id] = Pi::api('server', 'video')->canonizeServer($row);
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     * @param array
     */
    public function read()
    {
        $options = array();
        $result = $this->loadData($options);

        return $result;
    }

    /**
     * {@inheritDoc}
     * @param bool $name
     */
    public function create()
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