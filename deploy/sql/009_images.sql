INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '009_images', NOW( ) );

CREATE TABLE IF NOT EXISTS `silentbarn`.`images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `filename` varchar(50) DEFAULT NULL,
  `filename_orig` text,
  `date_path` varchar(8) DEFAULT NULL,
  `size` int(10) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `silentbarn`.`images` ADD  `ext` VARCHAR( 10 ) NULL DEFAULT NULL AFTER  `filename_orig` ;
ALTER TABLE  `silentbarn`.`images` ADD INDEX (  `post_id` ) ;
