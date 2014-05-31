INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '021_members_is_active', NOW( ) );

ALTER TABLE  `silentbarn`.`members` ADD  `is_active` TINYINT( 3 ) UNSIGNED NULL DEFAULT  '1' AFTER  `is_stewdio` ,
ADD INDEX (  `is_active` ) ;