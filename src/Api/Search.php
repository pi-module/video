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
use Pi\Search\AbstractSearch;

class Search extends AbstractSearch
{
    /**
     * {@inheritDoc}
     */
    protected $table = 'video';

    /**
     * {@inheritDoc}
     */
    protected $searchIn = array(
        'title',
        'description',
    );

    /**
     * {@inheritDoc}
     */
    protected $meta = array(
        'id'            => 'id',
        'title'         => 'title',
        'text_summary'  => 'content',
        'time_create'   => 'time',
        'uid'           => 'uid',
        'slug'          => 'slug',
    );

    /**
     * {@inheritDoc}
     */
    protected $condition = array(
        'status'    => 1,
    );

    /**
     * {@inheritDoc}
     */
    protected function buildUrl(array $item)
    {
        $link = Pi::service('url')->assemble('video', array(
            'module'        => $this->getModule(),
            'controller'    => 'watch',
            'slug'          => $item['slug'],
        ));

        return $link;
    }
}
