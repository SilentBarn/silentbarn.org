INSERT INTO `silentbarn`. `inserts` ( `name` , `created_at` )
VALUES ( '029_homepage', NOW( ) );

INSERT INTO  `silentbarn`.`pages` (
`id` ,
`name` ,
`content` ,
`created_at` ,
`modified_at`
)
VALUES (
NULL ,  'homepage', NULL , NOW( ) , NOW( )
);
