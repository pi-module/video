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

namespace Module\Video\Installer\Action;

use Pi;
use Pi\Application\Installer\Action\Update as BasicUpdate;
use Pi\Application\Installer\SqlSchema;
use Laminas\EventManager\Event;

class Update extends BasicUpdate
{
    /**
     * {@inheritDoc}
     */
    protected function attachDefaultListeners()
    {
        $events = $this->events;
        $events->attach('update.pre', [$this, 'updateSchema']);
        parent::attachDefaultListeners();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function updateSchema(Event $e): bool
    {
        $moduleVersion = $e->getParam('version');

        // Set video model
        $videoModel   = Pi::model('video', $this->module);
        $videoTable   = $videoModel->getTable();
        $videoAdapter = $videoModel->getAdapter();

        // Set video model
        $linkModel   = Pi::model('link', $this->module);
        $linkTable   = $linkModel->getTable();
        $linkAdapter = $linkModel->getAdapter();

        // Set playlist_inventory model
        $playlistInventoryModel   = Pi::model('playlist_inventory', $this->module);
        $playlistInventoryTable   = $playlistInventoryModel->getTable();
        $playlistInventoryAdapter = $playlistInventoryModel->getAdapter();

        // Set playlist_video model
        $playlistVideoModel   = Pi::model('playlist_video', $this->module);
        $playlistVideoTable   = $playlistVideoModel->getTable();
        $playlistVideoAdapter = $playlistVideoModel->getAdapter();

        // Update to version 1.5.0
        if (version_compare($moduleVersion, '1.5.0', '<')) {
            // Alter table : ADD main_image
            $sql = sprintf("ALTER TABLE %s ADD `main_image` INT(10) UNSIGNED NOT NULL DEFAULT '0'", $videoTable);
            try {
                $videoAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }

            // Alter table : ADD additional_images
            $sql = sprintf("ALTER TABLE %s ADD `additional_images` TEXT", $videoTable);
            try {
                $videoAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }
        }

        // Update to version 1.5.2
        if (version_compare($moduleVersion, '1.5.2', '<')) {
            // Add table : playlist_inventory
            $sql
                = <<<'EOD'
CREATE TABLE `{playlist_inventory}`
(
    `id`               INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
    `title`            VARCHAR(255)        NOT NULL DEFAULT '',
    `text_description` TEXT,
    `seo_title`        VARCHAR(255)        NOT NULL DEFAULT '',
    `seo_keywords`     VARCHAR(255)        NOT NULL DEFAULT '',
    `seo_description`  VARCHAR(255)        NOT NULL DEFAULT '',
    `status`           TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    `can_edit`         TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    `time_create`      INT(10) UNSIGNED    NOT NULL DEFAULT '0',
    `time_update`      INT(10) UNSIGNED    NOT NULL DEFAULT '0',
    `uid`              INT(10) UNSIGNED    NOT NULL DEFAULT '0',
    `hits`             INT(10) UNSIGNED    NOT NULL DEFAULT '0',
    `main_image`       INT(10) UNSIGNED    NOT NULL DEFAULT '0',
    `sale_price`       DECIMAL(16, 2)      NOT NULL DEFAULT '0.00',
    PRIMARY KEY (`id`)
);
EOD;
            SqlSchema::setType($this->module);
            $sqlHandler = new SqlSchema;
            try {
                $sqlHandler->queryContent($sql);
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                        'status'  => false,
                        'message' => 'SQL schema query for author table failed: '
                            . $exception->getMessage(),
                    ]
                );

                return false;
            }

            // Add table : playlist_video
            $sql
                = <<<'EOD'
CREATE TABLE `{playlist_video}`
(
    `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `playlist_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `video_id`    INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `video_order` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `time_create` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
);
EOD;
            SqlSchema::setType($this->module);
            $sqlHandler = new SqlSchema;
            try {
                $sqlHandler->queryContent($sql);
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                        'status'  => false,
                        'message' => 'SQL schema query for author table failed: '
                            . $exception->getMessage(),
                    ]
                );

                return false;
            }
        }

        // Update to version 1.5.3
        if (version_compare($moduleVersion, '1.5.3', '<')) {
            // Alter table : ADD playlist
            $sql = sprintf("ALTER TABLE %s ADD `playlist` VARCHAR(255) NOT NULL DEFAULT ''", $videoTable);
            try {
                $videoAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }
        }

        // Update to version 1.5.4
        if (version_compare($moduleVersion, '1.5.4', '<')) {
            // Alter table : ADD back_url
            $sql = sprintf("ALTER TABLE %s ADD `back_url` VARCHAR(255) NOT NULL DEFAULT ''", $playlistInventoryTable);
            try {
                $playlistInventoryAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }
        }

        // Update to version 1.5.4
        if (version_compare($moduleVersion, '1.5.8', '<')) {

            // Alter table : ADD company_id
            $sql = sprintf("ALTER TABLE %s ADD `company_id` INT(10) UNSIGNED NOT NULL DEFAULT '0'", $videoTable);
            try {
                $videoAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }

            // Alter table : ADD company_id
            $sql = sprintf("ALTER TABLE %s ADD `company_id` INT(10) UNSIGNED NOT NULL DEFAULT '0'", $linkTable);
            try {
                $linkAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }

            // Alter table : ADD company_id
            $sql = sprintf("ALTER TABLE %s ADD `company_id` INT(10) UNSIGNED NOT NULL DEFAULT '0'", $playlistInventoryTable);
            try {
                $playlistInventoryAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }

            // Alter table : ADD company_id
            $sql = sprintf("ALTER TABLE %s ADD `company_id` INT(10) UNSIGNED NOT NULL DEFAULT '0'", $playlistVideoTable);
            try {
                $playlistVideoAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }

            // Alter table : ADD sale_count
            $sql = sprintf("ALTER TABLE %s ADD `sale_count` INT(10) UNSIGNED NOT NULL DEFAULT '0'", $videoTable);
            try {
                $videoAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }
        }

        // Update to version 1.6.0
        if (version_compare($moduleVersion, '1.6.0', '<')) {
            // Alter table : ADD video_status
            $sql = sprintf("ALTER TABLE %s ADD `video_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1'", $videoTable);
            try {
                $videoAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }
        }

        return true;
    }
}
