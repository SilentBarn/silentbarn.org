INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '010_event_date_end', NOW( ) );

ALTER TABLE  `silentbarn`.`posts` ADD  `event_date_end` DATETIME NULL DEFAULT NULL AFTER  `event_date` ;