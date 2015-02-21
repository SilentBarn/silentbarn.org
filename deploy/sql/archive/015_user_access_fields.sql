INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '015_user_access_fields', NOW( ) );

ALTER TABLE  `silentbarn`.`users` ADD  `access_users` TINYINT UNSIGNED NULL DEFAULT  '0' AFTER  `name` ,
ADD  `access_members` TINYINT UNSIGNED NULL DEFAULT  '0' AFTER  `access_users` ,
ADD  `access_press` TINYINT UNSIGNED NULL DEFAULT  '0' AFTER  `access_members` ;

ALTER TABLE  `silentbarn`.`users` ADD  `is_deleted` TINYINT UNSIGNED NULL DEFAULT  '0' AFTER  `access_press` ;