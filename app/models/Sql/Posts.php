<?php

namespace Db\Sql;

class Posts extends \Base\Model
{
    public $id;
    public $user_id;
    public $title;
    public $slug;
    public $excerpt;
    public $body;
    public $status;
    public $is_deleted;
    public $post_date;
    public $event_date;
    public $homepage_location;
    public $created_at;
    public $modified_at;

    private $images;
    private $image;

    function initialize()
    {
        $this->setSource( 'posts' );
        $this->addBehavior( 'timestamp' );

        $this->images = NULL;
        $this->image = NULL;
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
     * Pull the most recent X posts that are set to display
     * in the home page boxes or hero filmstrip.
     *
     * @param string $location 'boxes' or 'hero'
     * @param integer $limit
     */
    static function getByLocation( $location = 'boxes', $limit = 5 )
    {
        return \Db\Sql\Posts::query()
            ->where( 'is_deleted = 0' )
            ->andWhere( "status = 'published'" )
            ->andWhere( "homepage_location = :location:" )
            ->andWhere( "post_date <= :dateLimit:" )
            ->order( 'post_date desc' )
            ->limit( $limit )
            ->bind([
                'location' => $location,
                'dateLimit' => date_str( NULL, DATE_DATABASE ) ])
            ->execute();
    }

    /**
     * Return a post by slug
     *
     * @param string $slug
     * @return \Db\Sql\Post
     */
    static function getBySlug( $slug )
    {
        return \Db\Sql\Posts::findFirst([
            'slug = :slug:',
            'bind' => [
                'slug' => $slug ]
            ]);
    }

    /**
     * Returns the images for the post
     */
    function getImages()
    {
        if ( ! is_null( $this->images ) )
        {
            return $this->images;
        }

        $this->images = \Db\Sql\Images::query()
            ->where( 'post_id = :postId:' )
            ->andWhere( 'is_deleted = 0' )
            ->bind([ 'postId' => $this->id ])
            ->execute();

        return $this->images;
    }

    /**
     * Returns one (primary) image for the post
     */
    function getImage()
    {
        if ( ! is_null( $this->image ) )
        {
            return $this->image;
        }

        $this->image = \Db\Sql\Images::findFirst([
            'post_id = :postId: and is_deleted = 0',
            'bind' => [
                'postId' => $this->id ]
            ]);

        if ( ! $this->image )
        {
            $this->image = new \Db\Sql\Images();
        }

        return $this->image;
    }

    /**
     * Retrieves categories for the post.
     */
    function getCategories()
    {
        return \Db\Sql\Relationships::getProperties(
            '\Db\Sql\Categories',
            CATEGORY,
            $this->id,
            POST );
    }

    /**
     * Retrieves tags for the post
     */
    function getTags()
    {
        return \Db\Sql\Relationships::getProperties(
            '\Db\Sql\Tags',
            TAG,
            $this->id,
            POST );
    }

    /**
     * Retrieves artists for the post
     */
    function getArtists()
    {
        return \Db\Sql\Relationships::getProperties(
            '\Db\Sql\Artists',
            ARTIST,
            $this->id,
            POST );
    }

    /**
     * Get the URL for the post.
     */
    function getPath()
    {
        $config = $this->getService( 'config' );

        return sprintf(
            "%s%s/%s",
            $config->paths->baseUri,
            date_str( $this->post_date, DATE_YEAR_MONTH_SLUG ),
            $this->slug );
    }

    /**
     * Generate a slug based on the title
     */
    function generateSlug()
    {
        // replace non letter or digits by -, then trim, transliterate
        // utf8 characters, lowercase it, and remove unwanted characters.
        //
        $slug = preg_replace( '~[^\\pL\d]+~u', '-', $this->title );
        $slug = trim( $slug, '-' );
        $slug = iconv( 'utf-8', 'us-ascii//TRANSLIT', $slug );
        $slug = strtolower( $slug );
        $slug = preg_replace( '~[^-\w]+~', '', $slug );

        if ( empty( $slug ) )
        {
            return NULL;
        }

        // check if this slug exists. if so, we need to keep incrementing
        // a counter on the end.
        //
        $checkSlug = $slug;
        $counter = 1;
        $slugOkay = FALSE;

        do {
            $post = \Db\Sql\Posts::getBySlug( $checkSlug );
            if ( $post ):
                $checkSlug = $slug .'-'. $counter;
                $counter++;
            else:
                $slugOkay = TRUE;
                $slug = $checkSlug;
            endif;
        }
        while ( ! $slugOkay );

        return $slug;
    }
}