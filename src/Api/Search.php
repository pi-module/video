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
    protected $table = array(
        'video',
        'category',
    );

    /**
     * {@inheritDoc}
     */
    protected $searchIn = array(
        'title',
        'text_summary',
        'text_description',
    );

    /**
     * {@inheritDoc}
     */
    protected $meta = array(
        'id'            => 'id',
        'title'         => 'title',
        'text_summary'  => 'content',
        'time_create'   => 'time',
        'slug'          => 'slug',
        'image'         => 'image',
        'path'          => 'path',
    );

    /**
     * {@inheritDoc}
     */
    protected $condition = array(
        'status' => 1,
    );

    /**
     * {@inheritDoc}
     */
    protected $order = array(
        'time_create DESC',
        'id DESC'
    );

    /**
     * {@inheritDoc}
     */
    protected function buildUrl(array $item, $table = '')
    {
        switch ($table) {
            case 'category':
                $link = Pi::url(Pi::service('url')->assemble('video', array(
                    'module' => $this->getModule(),
                    'controller' => 'category',
                    'slug' => $item['slug'],
                )));
                break;

            case 'video':
                $link = Pi::url(Pi::service('url')->assemble('video', array(
                    'module' => $this->getModule(),
                    'controller' => 'watch',
                    'slug' => $item['slug'],
                )));
                break;
        }

        return $link;
    }

    /**
     * {@inheritDoc}
     */
    protected function buildImage(array $item, $table = '')
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());

        $image = '';
        if (isset($item['image']) && !empty($item['image'])) {
            $image = Pi::url(
                sprintf('upload/%s/thumb/%s/%s',
                    $config['image_path'],
                    $item['path'],
                    $item['image']
                ));
        }

        return $image;
    }
}