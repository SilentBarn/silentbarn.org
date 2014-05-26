INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '020_event_price', NOW( ) );

ALTER TABLE  `silentbarn`.`posts` ADD  `price` DECIMAL NULL DEFAULT  '0' AFTER  `location` ;