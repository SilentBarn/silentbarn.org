INSERT INTO `silentbarn`. `inserts` ( `name` , `created_at` )
VALUES ( '004_posts', NOW( ) );

CREATE TABLE IF NOT EXISTS `silentbarn`.`posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `excerpt` text,
  `body` text,
  `status` varchar(10) DEFAULT 'draft',
  `is_deleted` tinyint(1) unsigned DEFAULT '0',
  `post_date` datetime DEFAULT NULL,
  `event_date` datetime DEFAULT NULL,
  `homepage_location` varchar(10) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `silentbarn`.`posts` ADD  `slug` VARCHAR( 255 ) NULL DEFAULT NULL AFTER  `title` ,
ADD UNIQUE ( `slug` );