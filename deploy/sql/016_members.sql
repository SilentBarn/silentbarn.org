INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '016_members', NOW( ) );

CREATE TABLE IF NOT EXISTS `silentbarn`.`members` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8_unicode_ci,
  `is_chef` tinyint(3) unsigned DEFAULT '0',
  `is_resident` tinyint(3) unsigned DEFAULT '0',
  `is_stewdio` tinyint(3) unsigned DEFAULT '0',
  `is_deleted` tinyint(3) unsigned DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE  `silentbarn`.`members` ADD  `image_filename` VARCHAR( 50 ) NULL DEFAULT NULL AFTER  `bio` ;