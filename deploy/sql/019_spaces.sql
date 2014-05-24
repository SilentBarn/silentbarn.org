INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '019_spaces', NOW( ) );

CREATE TABLE IF NOT EXISTS `silentbarn`.`spaces` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8_unicode_ci,
  `subtitle` text COLLATE utf8_unicode_ci,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_filename` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_residence` tinyint(3) unsigned DEFAULT '0',
  `is_stewdio` tinyint(3) unsigned DEFAULT '0',
  `is_gallery` tinyint(3) unsigned DEFAULT '0',
  `is_active` tinyint(3) unsigned DEFAULT '1',
  `is_deleted` tinyint(3) unsigned DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
