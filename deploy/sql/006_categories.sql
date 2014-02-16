INSERT INTO `silentbarn`. `inserts` ( `name` , `created_at` )
VALUES ( '006_categories', NOW( ) );

CREATE TABLE IF NOT EXISTS `silentbarn`.`categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO  `silentbarn`.`categories` ( `id` , `slug` , `name` , `created_at` )
VALUES ( NULL ,  'events',  'Events', NOW( ) );

INSERT INTO  `silentbarn`.`categories` ( `id` , `slug` , `name` , `created_at` )
VALUES ( NULL ,  'news',  'News', NOW( ) );

INSERT INTO  `silentbarn`.`categories` ( `id` , `slug` , `name` , `created_at` )
VALUES ( NULL ,  'press',  'Press', NOW( ) );
