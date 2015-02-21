INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '023_archived_members_spaces', NOW( ) );

ALTER TABLE  `silentbarn`.`spaces` ADD  `is_archived` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '0' AFTER  `is_active` ;
ALTER TABLE  `silentbarn`.`members` ADD  `is_archived` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '0' AFTER  `is_active` ;