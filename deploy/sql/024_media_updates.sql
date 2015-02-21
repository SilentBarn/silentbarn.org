INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '024_media_updates', NOW( ) );

ALTER TABLE  `silentbarn`.`users` ADD  `access_homepage` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '0' AFTER  `access_spaces` ;
