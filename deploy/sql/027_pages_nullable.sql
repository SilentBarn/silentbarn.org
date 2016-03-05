INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '027_pages_nullable', NOW( ) );

ALTER TABLE  `silentbarn`.`users` MODIFY  `access_pages` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '0' AFTER  `access_publish` ;