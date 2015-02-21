INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '024_media_updates', NOW( ) );

ALTER TABLE  `silentbarn`.`users` ADD  `access_homepage` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '0' AFTER  `access_spaces` ;
RENAME TABLE  `silentbarn`.`images` TO  `silentbarn`.`medias` ;
ALTER TABLE  `silentbarn`.`medias` ADD  `type` VARCHAR( 10 ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER  `user_id` ;
UPDATE `silentbarn`.`medias` SET `type` = 'image';