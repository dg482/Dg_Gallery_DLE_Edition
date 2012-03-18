
DROP TABLE IF EXISTS  dle_dg_gallery;

CREATE TABLE dle_dg_gallery(
  `id` INT AUTO_INCREMENT,
  `parent_id` MEDIUMINT NOT NULL DEFAULT '0',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `meta_data` MEDIUMBLOB COMMENT 'метаданные, описание,обложка',
  `access_data` MEDIUMBLOB COMMENT 'данные доступа (access,access_load)',
  `albom_num` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `symbol` VARCHAR(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `albom_num` (`albom_num`),
  KEY `symbol` (`symbol`)
) ENGINE = INNODB CHARACTER SET cp1251 COLLATE cp1251_general_ci;



DROP TABLE IF EXISTS dle_dg_gallery_albom;
CREATE TABLE dle_dg_gallery_albom(
  `id` INT AUTO_INCREMENT,
  `parent_id` MEDIUMINT NOT NULL DEFAULT '0',
  `author` VARCHAR(40) NOT NULL DEFAULT '',
  `author_id` MEDIUMINT(8) NOT NULL DEFAULT '0',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `meta_data` MEDIUMBLOB COMMENT 'метаданные, описание,обложка',
  `access_data` MEDIUMBLOB COMMENT 'данные доступа (access_group,access_rating,access_rating_file,access_comm,access_comm_file,access_comm_group)',
  `data` MEDIUMBLOB COMMENT 'данные для внутреннего использования (date,status)',
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


DROP TABLE IF EXISTS dle_dg_gallery_file;
CREATE TABLE dle_dg_gallery_file (
`id` int auto_increment,
`parent_id`  mediumint unsigned NOT NULL default '0',
`author` varchar(40) NOT NULL default '',
`author_id` mediumint(8)  unsigned  NOT NULL default '0',
`title` varchar(255) NOT NULL default '',
`descr` text NOT NULL,
`rating` smallint(5) NOT NULL default '0',
`vote_num` smallint(5) unsigned NOT NULL default '0',
`path` varchar(255) NOT NULL default '',
`hash` varchar(32) NOT NULL default '',
`other_dat` MEDIUMBLOB,
`original` tinyint(1) NOT NULL default '1' COMMENT '0 файл дублирует другой',
`comm_access` tinyint(1) NOT NULL default '0',
`rating_access` tinyint(1) NOT NULL default '0',
`comm_num` mediumint(8) unsigned NOT NULL default '0',
`date` datetime NOT NULL default '0000-00-00 00:00:00',
`download` int unsigned NOT NULL default '0',
`view` int unsigned NOT NULL default '0',
`position` smallint(5) NOT NULL default '0',
`status` varchar(50) NOT NULL default '',
PRIMARY KEY   (`id`),
KEY `parent_id` (`parent_id`),
KEY `hash` (`hash`),
KEY `download`(`download`),
KEY `view` (`view`),
FULLTEXT KEY `description` (`descr`)
)ENGINE=MyISAM CHARACTER SET  cp1251 COLLATE cp1251_general_ci;


DROP TABLE IF EXISTS dle_dg_gallery_log;
CREATE TABLE dle_dg_gallery_log(
  `id` INT AUTO_INCREMENT,
  `user_id` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0',
  `parent_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `ip` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ip2long',
  `action` VARCHAR(25) NOT NULL DEFAULT '',
  `status` VARCHAR(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ip` (`ip`)
) ENGINE = INNODB CHARACTER SET cp1251 COLLATE cp1251_general_ci;




DROP TABLE IF EXISTS dle_dg_gallery_comments;
CREATE TABLE dle_dg_gallery_comments(
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
 
  `ns_level` INT(11) DEFAULT NULL COMMENT "since 1.5.6",
  `ns_right` BIGINT(20) NOT NULL DEFAULT 0 COMMENT "since 1.5.6",
  `ns_left` BIGINT(20) NOT NULL DEFAULT 0 COMMENT "since 1.5.6",

  `approve` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ns_id` (`id`),
  KEY `user_id` (`user_id`)
) ENGINE = INNODB CHARACTER SET cp1251 COLLATE cp1251_general_ci;





DROP TABLE IF EXISTS dle_dg_gallery_tags;
CREATE TABLE dle_dg_gallery_tags(
  `id` INT AUTO_INCREMENT,
  `parent_id` INT UNSIGNED NOT NULL DEFAULT '0',
  `tag` VARCHAR(100) NOT NULL DEFAULT '',
  `status` VARCHAR(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE = INNODB CHARACTER SET cp1251 COLLATE cp1251_general_ci;


DROP TABLE IF EXISTS dle_dg_gallery_user;
CREATE TABLE dle_dg_gallery_user(
  `id` INT AUTO_INCREMENT,
  `user_id` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0',
  `albom` INT UNSIGNED NOT NULL DEFAULT '0',
  `files` INT UNSIGNED NOT NULL DEFAULT '0',
  `comments` INT UNSIGNED NOT NULL DEFAULT '0',
  `rating` INT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE = INNODB CHARACTER SET cp1251 COLLATE cp1251_general_ci;



INSERT INTO dle_admin_sections (name, title, descr, icon, allow_groups) VALUES ('dg_gallery', '"Gallery"', '', 'gallery.png', '1')
ON DUPLICATE KEY UPDATE
  name = 'dg_gallery';



--
  ALTER TABLE `dle_comments`
        ADD `ns_level` INT(11) DEFAULT NULL;
ALTER TABLE `dle_comments`
        ADD `ns_right` BIGINT(20) NOT NULL DEFAULT 0;
ALTER TABLE `dle_comments`
        ADD `ns_left` BIGINT(20) NOT NULL DEFAULT 0;