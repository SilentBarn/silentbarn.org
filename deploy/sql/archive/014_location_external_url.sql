INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '014_location_external_url', NOW( ) );

ALTER TABLE  `silentbarn`.`posts` ADD  `location` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER  `body` ,
ADD  `external_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER  `location` ;