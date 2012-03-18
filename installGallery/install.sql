
DROP TABLE IF EXISTS  ##_dg_gallery;
CREATE TABLE ##_dg_gallery(
  `id` INT AUTO_INCREMENT,
  `parent_id` MEDIUMINT NOT NULL DEFAULT '0',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `meta_data` MEDIUMBLOB,
  `access_data` MEDIUMBLOB,
  `albom_num` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `symbol` VARCHAR(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `albom_num` (`albom_num`),
  KEY `symbol` (`symbol`)
) ENGINE = INNODB CHARACTER SET cp1251 COLLATE cp1251_general_ci;



DROP TABLE IF EXISTS ##_dg_gallery_albom;
CREATE TABLE ##_dg_gallery_albom(
  `id` INT AUTO_INCREMENT,
  `parent_id` MEDIUMINT NOT NULL DEFAULT '0',
  `author` VARCHAR(40) NOT NULL DEFAULT '',
  `author_id` MEDIUMINT(8) NOT NULL DEFAULT '0',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `meta_data` MEDIUMBLOB,
  `access_data` MEDIUMBLOB,
  `data` MEDIUMBLOB,
  `images` MEDIUMINT(4) UNSIGNED NOT NULL DEFAULT '0',
  `votes` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `comm` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `rating` SMALLINT(5) NOT NULL DEFAULT '0',
  `approve` TINYINT(1) NOT NULL DEFAULT '0',
  `symbol` VARCHAR(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `symbol` (`symbol`),
  KEY `rating` (`rating`),
  KEY `author` (`author`)
) ENGINE = INNODB CHARACTER SET cp1251 COLLATE cp1251_general_ci;


DROP TABLE IF EXISTS ##_dg_gallery_file;
CREATE TABLE ##_dg_gallery_file(
  `id` INT AUTO_INCREMENT,
  `parent_id` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0',
  `author` VARCHAR(40) NOT NULL DEFAULT '',
  `author_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `descr` TEXT NOT NULL,
  `rating` SMALLINT(5) NOT NULL DEFAULT '0',
  `vote_num` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `path` VARCHAR(255) NOT NULL DEFAULT '',
  `hash` VARCHAR(32) NOT NULL DEFAULT '',
  `other_dat` MEDIUMBLOB,
  `original` TINYINT(1) NOT NULL DEFAULT '1',
  `comm_access` TINYINT(1) NOT NULL DEFAULT '0',
  `rating_access` TINYINT(1) NOT NULL DEFAULT '0',
  `comm_num` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `download` INT UNSIGNED NOT NULL DEFAULT '0',
  `view` INT UNSIGNED NOT NULL DEFAULT '0',
  `position` SMALLINT(5) NOT NULL DEFAULT '0',
  `status` VARCHAR(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `hash` (`hash`),
  KEY `download` (`download`),
  KEY `view` (`view`),
  FULLTEXT KEY `description` (`descr`)
) ENGINE = MYISAM CHARACTER SET cp1251 COLLATE cp1251_general_ci;


DROP TABLE IF EXISTS ##_dg_gallery_log;
CREATE TABLE ##_dg_gallery_log(
  `id` INT AUTO_INCREMENT,
  `user_id` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0',
  `parent_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `ip` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `action` VARCHAR(25) NOT NULL DEFAULT '',
  `status` VARCHAR(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ip` (`ip`)
) ENGINE = INNODB CHARACTER SET cp1251 COLLATE cp1251_general_ci;




DROP TABLE IF EXISTS ##_dg_gallery_comments;
CREATE TABLE ##_dg_gallery_comments(
  `id` INT AUTO_INCREMENT,
  `parent_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `user_id` MEDIUMINT(8) NOT NULL DEFAULT '0',
  `date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `autor` VARCHAR(40) NOT NULL DEFAULT '',
  `email` VARCHAR(40) NOT NULL DEFAULT '',
  `text` TEXT NOT NULL,
  `ip` VARCHAR(16) NOT NULL DEFAULT '',
  `is_register` TINYINT(1) NOT NULL DEFAULT '0',
  `status` VARCHAR(40) NOT NULL DEFAULT '',
  `ns_level` INT(11) DEFAULT NULL,
  `ns_right` BIGINT(20) NOT NULL DEFAULT 0,
  `ns_left` BIGINT(20) NOT NULL DEFAULT 0,
  `approve` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE = INNODB CHARACTER SET cp1251 COLLATE cp1251_general_ci;


DROP TABLE IF EXISTS ##_dg_gallery_tags;
CREATE TABLE ##_dg_gallery_tags(
  `id` INT AUTO_INCREMENT,
  `parent_id` INT UNSIGNED NOT NULL DEFAULT '0',
  `tag` VARCHAR(100) NOT NULL DEFAULT '',
  `status` VARCHAR(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE = INNODB CHARACTER SET cp1251 COLLATE cp1251_general_ci;


DROP TABLE IF EXISTS ##_dg_gallery_user;
CREATE TABLE ##_dg_gallery_user(
  `id` INT AUTO_INCREMENT,
  `user_id` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0',
  `albom` INT UNSIGNED NOT NULL DEFAULT '0',
  `files` INT UNSIGNED NOT NULL DEFAULT '0',
  `comments` INT UNSIGNED NOT NULL DEFAULT '0',
  `rating` INT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE = INNODB CHARACTER SET cp1251 COLLATE cp1251_general_ci;



INSERT INTO ##_admin_sections (name, title, descr, icon, allow_groups) VALUES ('dg_gallery', '"Gallery"', '', 'gallery.png', '1')
ON DUPLICATE KEY UPDATE name = 'dg_gallery';