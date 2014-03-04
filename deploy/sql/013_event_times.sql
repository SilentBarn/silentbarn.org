INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '013_event_times', NOW( ) );

ALTER TABLE  `posts` ADD  `event_time` TIME NULL DEFAULT NULL AFTER  `event_date_end` ,
ADD  `event_time_end` TIME NULL DEFAULT NULL AFTER  `event_time` ;
