<?php

namespace Db\Sql;

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

        $this->hasMany( 'id', 'Categories', 'post_id' );
        $this->hasMany( 'id', 'Tags', 'post_id' );
        $this->hasMany( 'id', 'Artists', 'post_id' );
        $this->hasMany( 'id', 'Images', 'post_id' );
    }

    /**
     * Get all active posts (not deleted)
     */
    function getActive( $limit = 25, $offset = 0 )
    {
        return \Db\Sql\Posts::find([
            'is_deleted' => 0,
            'order' => 'created_at desc',
            'limit' => [
                'number' => $limit,
                'offset' => $offset ]
            ]);
    }
}
