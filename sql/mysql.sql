CREATE TABLE `{video}` (
  `id`               INT(10) UNSIGNED        NOT NULL AUTO_INCREMENT,
  `title`            VARCHAR(255)            NOT NULL DEFAULT '',
  `slug`             VARCHAR(255)            NOT NULL DEFAULT '',
  `category`         VARCHAR(255)            NOT NULL DEFAULT '',
  `category_main`    INT(10) UNSIGNED        NOT NULL DEFAULT '0',
  `text_summary`     TEXT,
  `text_description` TEXT,
  `seo_title`        VARCHAR(255)            NOT NULL DEFAULT '',
  `seo_keywords`     VARCHAR(255)            NOT NULL DEFAULT '',
  `seo_description`  VARCHAR(255)            NOT NULL DEFAULT '',
  `status`           TINYINT(1) UNSIGNED     NOT NULL DEFAULT '0',
  `time_create`      INT(10) UNSIGNED        NOT NULL DEFAULT '0',
  `time_update`      INT(10) UNSIGNED        NOT NULL DEFAULT '0',
  `uid`              INT(10) UNSIGNED        NOT NULL DEFAULT '0',
  `hits`             INT(10) UNSIGNED        NOT NULL DEFAULT '0',
  `download`         INT(10) UNSIGNED        NOT NULL DEFAULT '0',
  `image`            VARCHAR(255)            NOT NULL DEFAULT '',
  `path`             VARCHAR(16)             NOT NULL DEFAULT '',
  `point`            INT(10)                 NOT NULL DEFAULT '0',
  `count`            INT(10) UNSIGNED        NOT NULL DEFAULT '0',
  `attribute`        TINYINT(3) UNSIGNED     NOT NULL DEFAULT '0',
  `related`          TINYINT(3) UNSIGNED     NOT NULL DEFAULT '0',
  `recommended`      TINYINT(1) UNSIGNED     NOT NULL DEFAULT '0',
  `favourite`        INT(10) UNSIGNED        NOT NULL DEFAULT '0',
  `video_type`       ENUM ('video', 'audio') NOT NULL DEFAULT 'video',
  `video_extension`  ENUM ('mp4', 'mp3')     NOT NULL DEFAULT 'mp4',
  `video_link`       VARCHAR(255)            NOT NULL DEFAULT '',
  `video_file`       VARCHAR(64)             NOT NULL DEFAULT '',
  `video_path`       VARCHAR(64)             NOT NULL DEFAULT '',
  `video_url`        VARCHAR(64)             NOT NULL DEFAULT '',
  `video_size`       VARCHAR(16)             NOT NULL DEFAULT '',
  `video_duration`   VARCHAR(16)             NOT NULL DEFAULT '',
  `setting`          TEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `title` (`title`),
  KEY `time_create` (`time_create`),
  KEY `status` (`status`),
  KEY `uid` (`uid`),
  KEY `video_list` (`status`, `id`),
  KEY `video_order` (`time_create`, `id`)
);

CREATE TABLE `{category}` (
  `id`               INT(10) UNSIGNED              NOT NULL AUTO_INCREMENT,
  `parent`           INT(5) UNSIGNED               NOT NULL,
  `title`            VARCHAR(255)                  NOT NULL DEFAULT '',
  `slug`             VARCHAR(255)                  NOT NULL DEFAULT '',
  `text_summary`     TEXT,
  `text_description` TEXT,
  `image`            VARCHAR(255)                  NOT NULL DEFAULT '',
  `path`             VARCHAR(16)                   NOT NULL DEFAULT '',
  `seo_title`        VARCHAR(255)                  NOT NULL DEFAULT '',
  `seo_keywords`     VARCHAR(255)                  NOT NULL DEFAULT '',
  `seo_description`  VARCHAR(255)                  NOT NULL DEFAULT '',
  `time_create`      INT(10) UNSIGNED              NOT NULL DEFAULT '0',
  `time_update`      INT(10) UNSIGNED              NOT NULL DEFAULT '0',
  `setting`          TEXT,
  `status`           TINYINT(1) UNSIGNED           NOT NULL,
  `display_order`    INT(10) UNSIGNED              NOT NULL DEFAULT '0',
  `display_type`     ENUM ('video', 'subcategory') NOT NULL DEFAULT 'video',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `parent` (`parent`),
  KEY `title` (`title`),
  KEY `time_create` (`time_create`),
  KEY `status` (`status`),
  KEY `display_order` (`display_order`),
  KEY `category_list` (`status`, `parent`, `display_order`, `id`)
);

CREATE TABLE `{link}` (
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `video`       INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `category`    INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `time_create` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `time_update` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `status`      TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `uid`         INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `hits`        INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `video` (`video`),
  KEY `category` (`category`),
  KEY `time_create` (`time_create`),
  KEY `status` (`status`),
  KEY `uid` (`uid`),
  KEY `hits` (`hits`),
  KEY `category_list` (`status`, `category`, `time_create`),
  KEY `video_list` (`status`, `video`, `time_create`, `category`),
  KEY `link_order` (`time_create`, `id`)
);

CREATE TABLE `{field}` (
  `id`       INT(10) UNSIGNED                                                                                    NOT NULL AUTO_INCREMENT,
  `title`    VARCHAR(255)                                                                                        NOT NULL DEFAULT '',
  `icon`     VARCHAR(32)                                                                                         NOT NULL DEFAULT '',
  `type`     ENUM ('text', 'link', 'currency', 'date', 'number', 'select', 'video', 'audio', 'file', 'checkbox') NOT NULL DEFAULT 'text',
  `order`    INT(10) UNSIGNED                                                                                    NOT NULL DEFAULT '0',
  `status`   TINYINT(1) UNSIGNED                                                                                 NOT NULL DEFAULT '0' DEFAULT '1',
  `search`   TINYINT(1) UNSIGNED                                                                                 NOT NULL DEFAULT '0' DEFAULT '1',
  `position` INT(10) UNSIGNED                                                                                    NOT NULL DEFAULT '0',
  `value`    TEXT,
  `name`     VARCHAR(64)                                                                                                  DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `title` (`title`),
  KEY `order` (`order`),
  KEY `status` (`status`),
  KEY `search` (`search`),
  KEY `position` (`position`),
  KEY `order_status` (`order`, `status`)
);

CREATE TABLE `{field_category}` (
  `id`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `field`    INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `category` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `field` (`field`),
  KEY `category` (`category`),
  KEY `field_category` (`field`, `category`)
);

CREATE TABLE `{field_position}` (
  `id`     INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `title`  VARCHAR(255)        NOT NULL DEFAULT '',
  `order`  INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `order` (`order`),
  KEY `status` (`status`),
  KEY `order_status` (`order`, `status`)
);

CREATE TABLE `{field_data}` (
  `id`    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `field` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `video` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `data`  VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `field` (`field`),
  KEY `video` (`video`),
  KEY `data` (`data`),
  KEY `field_video` (`field`, `video`)
);

CREATE TABLE `{log}` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`         INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `ip`          CHAR(15)         NOT NULL DEFAULT '',
  `time_create` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `section`     VARCHAR(32)      NOT NULL DEFAULT '',
  `item`        INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `operation`   VARCHAR(32)      NOT NULL DEFAULT '',
  `description` TEXT,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `time_create` (`time_create`)
);

