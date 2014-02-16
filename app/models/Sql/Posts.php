<?php

namespace Db\Sql;

use Phalcon\Mvc\Model\Query;

class Posts extends \Base\Model
{
    public $id;
    public $user_id;
    public $title;
    public $excerpt;
    public $body;
    public $status;
    public $is_deleted;
    public $post_date;
    public $event_date;
    public $homepage_location;
    public $created_at;
    public $modified_at;

    function initialize()
    {
        $this->setSource( 'posts' );
        $this->addBehavior( 'timestamp' );
    }

    /**
     * Get all active posts (not deleted)
     */
    static function getActive( $limit = 25, $offset = 0 )
    {
        return \Db\Sql\Posts::query()
            ->where( 'is_deleted = 0' )
            ->order( 'created_at desc' )
            ->limit( $limit, $offset )
            ->execute();
    }

    /**
     * Returns the images for the post
     */
    function getImages()
    {
        return \Db\Sql\Images::query()
            ->where( 'post_id = :postId:' )
            ->andWhere( 'is_deleted = 0' )
            ->bind([ 'postId' => $this->id ])
            ->execute();
    }

    /**
     * Returns one (primary) image for the post
     */
    function getImage()
    {
        $image = \Db\Sql\Images::findFirst([
            'post_id = :postId: and is_deleted = 0',
            'bind' => [
                'postId' => $this->id ]
            ]);

        return ( $image )
            ? $image
            : new \Db\Sql\Images();
    }

    /**
     * Retrieves categories for the post
     */
    function getCategories()
    {
        $phql = sprintf(
            "select c.* from \Db\Sql\Categories as c ".
            "inner join \Db\Sql\Relationships as r ".
            "  on c.id = r.property_id and r.property_type = '%s' ".
            "where r.object_id = :objectId: ".
            "  and r.object_type = :objectType: ".
            "order by c.name desc",
            CATEGORY );
        $query = new Query( $phql, $this->getDI() );

        return $query->execute([
            'objectId' => $this->id,
            'objectType' => 'post' ]);
    }

    /**
     * Retrieves tags for the post
     */
    function getTags()
    {
        $phql = sprintf(
            "select t.* from \Db\Sql\Tags as t ".
            "inner join \Db\Sql\Relationships as r ".
            "  on t.id = r.property_id and r.property_type = '%s' ".
            "where r.object_id = :objectId: ".
            "  and r.object_type = :objectType: ".
            "order by r.name desc",
            TAG );
        $query = new Query( $phql, $this->getDI() );

        return $query->execute([
            'objectId' => $this->id,
            'objectType' => 'post' ]);
    }

    /**
     * Retrieves artists for the post
     */
    function getArtists()
    {
        $phql = sprintf(
            "select t.* from \Db\Sql\Artists as a ".
            "inner join \Db\Sql\Relationships as r ".
            "  on a.id = r.property_id and r.property_type = '%s' ".
            "where r.object_id = :objectId: ".
            "  and r.object_type = :objectType: ".
            "order by r.name desc",
            ARTIST );
        $query = new Query( $phql, $this->getDI() );

        return $query->execute([
            'objectId' => $this->id,
            'objectType' => 'post' ]);
    }
}
