INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '028_more_categories', NOW( ) );

INSERT INTO `silentbarn`.`categories` (`id`, `slug`, `name`, `created_at`) VALUES (NULL, 'community', 'Community', NOW());