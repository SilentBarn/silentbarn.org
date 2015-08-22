INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '026_pages_setting', NOW( ) );

ALTER TABLE  `silentbarn`.`users` ADD  `access_pages` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `access_publish` ;

ALTER TABLE  `silentbarn`.`users` CHANGE  `access_users`  `access_users` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '0',
CHANGE  `access_members`  `access_members` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '0',
CHANGE  `access_press`  `access_press` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '0',
CHANGE  `access_spaces`  `access_spaces` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '0',
CHANGE  `is_deleted`  `is_deleted` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '0';

ALTER TABLE  `silentbarn`.`pages` ADD  `owner_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL AFTER  `name` ;

INSERT INTO  `silentbarn`.`pages` (
    `id` ,
    `name` ,
    `owner_id` ,
    `content` ,
    `created_at` ,
    `modified_at`
)
VALUES (
    NULL ,  'donate', NULL , NULL , NOW( ) , NOW( )
);

ALTER TABLE  `silentbarn`.`pages` ADD  `label` VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL AFTER  `name` ;

UPDATE  `silentbarn`.`pages` SET  `label` =  'Press' WHERE  `silentbarn`.`pages`.`id` =1;
UPDATE  `silentbarn`.`pages` SET  `label` =  'Donate' WHERE  `silentbarn`.`pages`.`id` =2;