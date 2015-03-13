INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '025_publish_setting', NOW( ) );

ALTER TABLE  `users` ADD  `silentbarn`.`access_publish` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '0' AFTER  `access_homepage` ;