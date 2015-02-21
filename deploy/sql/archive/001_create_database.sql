CREATE DATABASE IF NOT EXISTS `silentbarn`;

CREATE TABLE IF NOT EXISTS `silentbarn`.`inserts` (
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '001_create_database', NOW( ) );