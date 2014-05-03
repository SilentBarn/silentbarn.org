INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '017_pages_and_press', NOW( ) );

CREATE TABLE IF NOT EXISTS `silentbarn`.`pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

INSERT INTO  `silentbarn`.`pages` (
`id` ,
`name` ,
`content` ,
`created_at` ,
`modified_at`
)
VALUES (
NULL ,  'press', NULL , NOW( ) , NOW( )
);
