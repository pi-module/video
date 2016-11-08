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
namespace Module\Video\Installer\Action;

use Pi;
use Pi\Application\Installer\Action\Update as BasicUpdate;
use Pi\Application\Installer\SqlSchema;
use Zend\EventManager\Event;

class Update extends BasicUpdate
{
    /**
     * {@inheritDoc}
     */
    protected function attachDefaultListeners()
    {
        $events = $this->events;
        $events->attach('update.pre', array($this, 'updateSchema'));
        parent::attachDefaultListeners();
        
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function updateSchema(Event $e)
    {
        $moduleVersion = $e->getParam('version');

        // Set category model
        $categoryModel = Pi::model('category', $this->module);
        $categoryTable = $categoryModel->getTable();
        $categoryAdapter = $categoryModel->getAdapter();

        // Set video model
        $videoModel = Pi::model('video', $this->module);
        $videoTable = $videoModel->getTable();
        $videoAdapter = $videoModel->getAdapter();

        if (version_compare($moduleVersion, '0.4.2', '<')) {
            // Alter table field `type`
            $sql = sprintf("ALTER TABLE %s ADD `text_summary` TEXT", $categoryTable);
            try {
                $categoryAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }
        }

        if (version_compare($moduleVersion, '0.4.3', '<')) {
            // Alter table field `type`
            $sql = sprintf("ALTER TABLE %s ADD `display_type` ENUM ('video', 'subcategory') NOT NULL DEFAULT 'video'", $categoryTable);
            try {
                $categoryAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }
        }

        if (version_compare($moduleVersion, '0.4.4', '<')) {
            // Add table : service
            $sql = <<<'EOD'
CREATE TABLE `{service}` (
  `id`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `video`        INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `module_name`  VARCHAR(64)      NOT NULL DEFAULT '',
  `module_table` VARCHAR(64)      NOT NULL DEFAULT '',
  `module_item`  INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `video` (`video`),
  KEY `select` (`module_name`, `module_table`, `module_item`)
);
EOD;
            SqlSchema::setType($this->module);
            $sqlHandler = new SqlSchema;
            try {
                $sqlHandler->queryContent($sql);
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'SQL schema query for author table failed: '
                        . $exception->getMessage(),
                ));

                return false;
            }
        }

        if (version_compare($moduleVersion, '0.6.0', '<')) {
            // Add table : service
            $sql = <<<'EOD'
CREATE TABLE `{server}` (
  `id`      INT(10) UNSIGNED                NOT NULL AUTO_INCREMENT,
  `title`   VARCHAR(255)                    NOT NULL DEFAULT '',
  `status`  TINYINT(1) UNSIGNED             NOT NULL DEFAULT '1',
  `default` TINYINT(1) UNSIGNED             NOT NULL DEFAULT '0',
  `type`    ENUM ('file', 'wowza', 'qmery') NOT NULL DEFAULT 'file',
  `url`     VARCHAR(255)                    NOT NULL DEFAULT '',
  `setting` TEXT,
  PRIMARY KEY (`id`)
);
INSERT INTO `{server}` (`id`, `title`, `status`, `default`, `type`, `url`, `setting`) VALUES ('1', '%s', '1', '1', 'file', '%s', NULL);
EOD;
            $sql = sprintf($sql, __('Test server'), Pi::url());
            SqlSchema::setType($this->module);
            $sqlHandler = new SqlSchema;
            try {
                $sqlHandler->queryContent($sql);
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'SQL schema query for author table failed: '
                        . $exception->getMessage(),
                ));

                return false;
            }

            // Alter table field `video_server`
            $sql = sprintf("ALTER TABLE %s ADD `video_server` INT(10) UNSIGNED NOT NULL DEFAULT '1'", $videoTable);
            try {
                $videoAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }

            // Alter table field `video_qmery_hash`
            $sql = sprintf("ALTER TABLE %s ADD `video_qmery_hash` VARCHAR(64) NOT NULL DEFAULT ''", $videoTable);
            try {
                $videoAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }

            // Alter table field `video_qmery_id`
            $sql = sprintf("ALTER TABLE %s ADD `video_qmery_id` INT(10) UNSIGNED NOT NULL DEFAULT '0'", $videoTable);
            try {
                $videoAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }
        }

        if (version_compare($moduleVersion, '0.6.4', '<')) {
            // Alter table field `video_qmery_hls`
            $sql = sprintf("ALTER TABLE %s ADD `video_qmery_hls` VARCHAR(255) NOT NULL DEFAULT ''", $videoTable);
            try {
                $videoAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }
        }
        
        return true;
    }
}