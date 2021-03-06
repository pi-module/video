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

namespace Module\Video\Controller\Admin;

use Module\Video\Form\SitemapForm;
use Pi;
use Pi\Mvc\Controller\ActionController;

class ToolsController extends ActionController
{
    public function indexAction()
    {
        // Set template
        $this->view()->setTemplate('tools-index');
    }

    public function sitemapAction()
    {
        $form    = new SitemapForm('sitemap');
        $message = __('Rebuild thie module links on sitemap module tabels');
        if ($this->request->isPost()) {
            // Set form date
            $values = $this->request->getPost()->toArray();
            switch ($values['type']) {
                case '1':
                    Pi::api('video', 'video')->sitemap();
                    Pi::api('category', 'video')->sitemap();
                    break;

                case '2':
                    Pi::api('video', 'video')->sitemap();
                    break;

                case '3':
                    Pi::api('category', 'video')->sitemap();
                    break;
            }
            $message = __('Sitemap rebuild finished');
        }
        // Set view
        $this->view()->setTemplate('tools-sitemap');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Rebuild sitemap links'));
        $this->view()->assign('message', $message);
    }

    public function migrateAction()
    {
        $message = Pi::api('video', 'video')->migrateMedia();

        if (empty($message)) {
            $message = __('Media have migrate successfully');
        }
        $this->jump(['action' => 'index'], $message);
    }
}
