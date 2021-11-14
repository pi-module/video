<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\Video\Installer\Action;

use Pi;
use Pi\Application\Installer\Action\Install as BasicInstall;
use Laminas\EventManager\Event;

class Install extends BasicInstall
{
    protected function attachDefaultListeners(): static
    {
        $events = $this->events;
        $events->attach('install.pre', [$this, 'preInstall'], 1000);
        $events->attach('install.post', [$this, 'postInstall'], 1);
        parent::attachDefaultListeners();
        return $this;
    }

    public function preInstall(Event $e)
    {
        $result = [
            'status'  => true,
            'message' => sprintf('Called from %s', __METHOD__),
        ];
        $e->setParam('result', $result);
    }

    public function postInstall(Event $e)
    {
        // Set status list
        $typeList = [
            [
                'title' => __('File'),
                'type'  => 'server',
                'name'  => 'file',
            ],
            [
                'title' => __('Wowza'),
                'type'  => 'server',
                'name'  => 'wowza',
            ],
            [
                'title' => __('Nginx'),
                'type'  => 'server',
                'name'  => 'nginx',
            ],
            [
                'title' => __('MistServer'),
                'type'  => 'server',
                'name'  => 'mistserver',
            ],
            [
                'title' => __('Youtube'),
                'type'  => 'server',
                'name'  => 'youtube',
            ],
            [
                'title' => __('Vimeo'),
                'type'  => 'server',
                'name'  => 'vimeo',
            ],
            [
                'title' => __('Aparat'),
                'type'  => 'server',
                'name'  => 'aparat',
            ],
        ];

        // Add status list on table
        $typeModel = Pi::model('type', $e->getParam('module'));
        foreach ($typeList as $type) {
            $typeModel->insert($type);
        }

        // Result
        $result = [
            'status'  => true,
            'message' => __('Default types added.'),
        ];
        $this->setResult('post-install', $result);
    }
}
