INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '025_publish_setting', NOW( ) );

ALTER TABLE  `silentbarn`.`users` ADD  `users`.`access_publish` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '0' AFTER  `access_homepage` ;