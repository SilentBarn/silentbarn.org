INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '011_exhibitions_category', NOW( ) );

INSERT INTO  `silentbarn`.`categories` ( `id` , `slug` , `name` , `created_at` )
VALUES ( NULL ,  'exhibitions',  'Exhibitions', NOW( ) );