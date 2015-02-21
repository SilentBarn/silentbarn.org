INSERT INTO `silentbarn`. `inserts` ( `name` , `created_at` )
VALUES ( '005_relationships', NOW( ) );

CREATE TABLE IF NOT EXISTS `silentbarn`.`relationships` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `object_id` int(10) unsigned DEFAULT NULL,
  `object_type` varchar(20) DEFAULT NULL,
  `property_id` int(10) unsigned DEFAULT NULL,
  `property_type` varchar(20) DEFAULT NULL,
  `key` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `silentbarn`.`relationships` ADD INDEX (  `object_id` ) ;
ALTER TABLE  `silentbarn`.`relationships` ADD INDEX (  `object_type` ) ;
ALTER TABLE  `silentbarn`.`relationships` ADD INDEX (  `property_id` ) ;
ALTER TABLE  `silentbarn`.`relationships` ADD INDEX (  `property_type` ) ;
