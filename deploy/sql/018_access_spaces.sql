INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '018_access_spaces', NOW( ) );

ALTER TABLE  `silentbarn`.`users` ADD  `access_spaces` TINYINT( 3 ) UNSIGNED NULL DEFAULT  '0' AFTER  `access_press` ;