INSERT INTO  `silentbarn`.`inserts` ( `name` , `created_at` )
VALUES ( '022_article_category', NOW( ) );

INSERT INTO  `silentbarn`.`categories` ( `id` , `slug` , `name` , `created_at` )
VALUES ( NULL ,  'articles',  'Articles', NOW( ) );
