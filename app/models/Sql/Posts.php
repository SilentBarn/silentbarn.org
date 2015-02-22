<?php

namespace Db\Sql;

use Phalcon\Mvc\Model\Query,
    Michelf\Markdown,
    Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class Posts extends \Base\Model
{
    public $id;
    public $user_id;
    public $title;
    public $slug;
    public $excerpt;
    public $body;
    public $location;
    public $price;
    public $external_url;
    public $status;
    public $is_deleted;
    public $post_date;
    public $event_date;
    public $event_date_end;
    public $event_time;
    public $event_time_end;
    public $homepage_location;
    public $created_at;
    public $modified_at;

    private $images;
    private $image;
    private $audio;
    private $categories;
    private $author;

    function initialize()
    {
        $this->setSource( 'posts' );
        $this->addBehavior( 'timestamp' );

        $this->images = NULL;
        $this->image = NULL;
        $this->categories = NULL;
        $this->author = NULL;
    }

    /**
     * Get all posts (not deleted)
     */
    static function getAll( $limit = 25, $offset = 0 )
    {
        return \Db\Sql\Posts::query()
            ->where( 'is_deleted = 0' )
            ->orderBy( 'created_at desc' )
            ->limit( $limit, $offset )
            ->execute();
    }

    /**
     * Get all active posts (not deleted and published)
     */
    static function getActive( $limit = 25, $offset = 0 )
    {
        return \Db\Sql\Posts::query()
            ->where( 'is_deleted = 0' )
            ->where( 'status = "'. PUBLISHED .'"' )
            ->orderBy( 'created_at desc' )
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
            ->andWhere( "status = '". PUBLISHED ."'" )
            ->andWhere( "homepage_location = :location:" )
            ->orderBy( 'event_date desc, post_date desc' )
            ->limit( $limit )
            ->bind([
                'location' => $location ])
            ->execute();
    }

    /**
     * Returns posts between a given date range and for a given
     * set of categories. You can optionally limit these results.
     * The results are sorted by oldest first unless specified.
     *
     * @param array $params
     * @return \Db\Sql\Posts
     */
    static function getByCategoryDateRange( $params = array() )
    {
        $defaults = [
            'categories' => [ EVENTS, EXHIBITIONS ],
            'startDate' => date( DATE_DATABASE, mktime( 0, 0, 0 ) ),
            'endDate' => NULL,
            'startOperand' => '>=',
            'endOperand' => '<=',
            'ongoing' => FALSE,
            'sort' => 'event_date asc',
            'limit' => 9999,
            'offset' => 0 ];
        $options = array_merge( $defaults, $params );
        $dateWhereClauses = [];
        $bindings = [];

        // create our date where clause
        if ( ! is_null( $options[ 'startDate' ] ) )
        {
            $string = ( $options[ 'ongoing' ] )
                ? "( p.event_date %s :startDate: or p.event_date_end %s :startDate: )"
                : "( p.event_date %s :startDate: )";
            $dateWhereClauses[] = sprintf(
                $string,
                $options[ 'startOperand' ],
                $options[ 'startOperand' ] );
            $bindings[ 'startDate' ] = $options[ 'startDate' ];
        }

        if ( ! is_null( $options[ 'endDate' ] ) )
        {
            $string = ( $options[ 'ongoing' ] )
                ? "( p.event_date %s :endDate: or p.event_date_end %s :endDate: )"
                : "( p.event_date_end %s :endDate: or p.event_date_end is null )";
            $dateWhereClauses[] = sprintf(
                $string,
                $options[ 'endOperand' ],
                $options[ 'endOperand' ] );
            $bindings[ 'endDate' ] = $options[ 'endDate' ];
        }

        // create the SQL statement
        $phql = sprintf(
            "select p.* from \Db\Sql\Posts as p ".
            "inner join \Db\Sql\Relationships as r ".
            "  on p.id = r.object_id and r.object_type = '%s' ".
            "inner join \Db\Sql\Categories as c ".
            "  on c.id = r.property_id and r.property_type = '%s' ".
            "where c.slug in ('%s') ".
            "  and ( %s ) ".
            "  and p.is_deleted = 0 and p.status = '%s' ".
            "order by p.%s ".
            "limit %s, %s",
            POST,
            CATEGORY,
            implode( ',', $options[ 'categories' ] ),
            implode( ' and ', $dateWhereClauses ),
            PUBLISHED,
            $options[ 'sort' ],
            $options[ 'offset' ],
            $options[ 'limit' ] );
        $query = new Query( $phql, self::getStaticDI() );

        return $query->execute( $bindings );
    }

    /**
     * Search posts by a variety of params. This executes a raw SQL
     * command because of the complexity of the query.
     *
     * @param array $params
     * @return \Db\Sql\Posts
     */
    static function search( $params )
    {
        // initialize arrays
        $whereClauses = [];
        $bindings = [];

        // set up date clauses if any came in
        if ( valid( get( $params, 'startDate' ), STRING ) )
        {
            //$whereClauses[] = "p.event_date >= ':startDate:'";
            //$bindings[ 'startDate' ] = $params[ 'startDate' ];
            $whereClauses[] = "p.event_date >= ?";
            $bindings[] = $params[ 'startDate' ];
        }

        if ( valid( get( $params, 'endDate' ), STRING ) )
        {
            //$whereClauses[] = "p.event_date <= ':endDate:'";
            //$bindings[ 'endDate' ] = $params[ 'endDate' ];
            $whereClauses[] = "p.event_date <= ?";
            $bindings[] = $params[ 'endDate' ];
        }

        // search on keywords
        if ( valid( get( $params, 'keywords' ), STRING ) )
        {
            //$whereClauses[] = "p.title like '%:keywords:%'";
            //$bindings[ 'keywords' ] = $params[ 'keywords' ];
            $whereClauses[] = "p.title like ?";
            $bindings[] = '%'. $params[ 'keywords' ] .'%';
        }

        // search artists
        if ( valid( get( $params, 'artist' ), STRING ) )
        {
            $whereClauses[] = sprintf(
                "exists (".
                "  select * from artists as a ".
                "  inner join relationships as r2 ".
                "  on a.id = r2.property_id and r2.property_type = '%s' ".
                "  where a.name like ? and p.id = r2.object_id and r2.object_type = '%s' ) ",
                ARTIST,
                POST );
            //$bindings[ 'artist' ] = $params[ 'artist' ];
            $bindings[] = '%'. $params[ 'artist' ] .'%';
        }

        if ( isset( $params[ 'isDeleted' ] ) )
        {
            $whereClauses[] = "p.is_deleted = ". $params[ 'isDeleted' ];
        }

        if ( isset( $params[ 'status' ] ) )
        {
            $whereClauses[] = "p.status = ". $params[ 'status' ];
        }

        // stringify the where clauses
        $whereClausesString = ( count( $whereClauses ) )
            ? " and " . implode( ' and ', $whereClauses )
            : "";

        // optionally can get the count
        if ( get( $params, 'count' ) === TRUE )
        {
            $select = "count(1) as count";
            $limit = "";
        }
        else
        {
            $select = "p.*";
            $limit = sprintf( "limit %s, %s", $params[ 'offset' ], $params[ 'limit' ] );
        }

        // create the SQL statement
        $sql = sprintf(
            "select %s from posts as p ".
            "inner join relationships as r ".
            "  on p.id = r.object_id and r.object_type = '%s' ".
            "inner join categories as c ".
            "  on c.id = r.property_id and r.property_type = '%s' ".
            "where c.slug in ('%s') ". // cat names
            "  %s ". // where clauses
            "order by p.%s ".
            "%s", // limit
            $select,
            POST,
            CATEGORY,
            implode( "','", $params[ 'categories' ] ),
            $whereClausesString,
            'event_date desc',
            $limit );

        // base model
        $post = new Posts();

        // execute the query
        $results = new Resultset(
            NULL,
            $post,
            $post->getReadConnection()->query(
                $sql,
                $bindings
            ));

        if ( get( $params, 'count' ) === TRUE )
        {
            $array = $results->toArray();

            return get( array_shift( $array ), 'count', 0 );
        }

        return $results;
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

        $this->images = \Db\Sql\Medias::query()
            ->where( 'post_id = :postId:' )
            ->andWhere( 'type = :type:' )
            ->andWhere( 'is_deleted = 0' )
            ->bind([
                'postId' => $this->id,
                'type' => MEDIA_IMAGE ])
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

        $this->image = \Db\Sql\Medias::findFirst([
            'post_id = :postId: and is_deleted = 0 and type = :type:',
            'bind' => [
                'postId' => $this->id,
                'type' => MEDIA_IMAGE ]
            ]);

        if ( ! $this->image )
        {
            $this->image = new \Db\Sql\Medias();
        }

        return $this->image;
    }

    /**
     * Returns an audio file for the post
     */
    function getAudio()
    {
        if ( ! is_null( $this->audio ) )
        {
            return $this->audio;
        }

        $this->audio = \Db\Sql\Medias::findFirst([
            'post_id = :postId: and is_deleted = 0 and type = :type:',
            'bind' => [
                'postId' => $this->id,
                'type' => MEDIA_AUDIO ]
            ]);

        if ( ! $this->audio )
        {
            $this->audio = new \Db\Sql\Medias();
        }

        return $this->audio;
    }

    /**
     * Retrieves categories for the post.
     */
    function getCategories()
    {
        if ( ! is_null( $this->categories ) )
        {
            return $this->categories;
        }
        
        $this->categories = \Db\Sql\Relationships::getProperties(
            '\Db\Sql\Categories',
            CATEGORY,
            $this->id,
            POST );

        return $this->categories;
    }

    function getCategoryIds()
    {
        $ids = [];

        foreach ( $this->getCategories() as $category )
        {
            $ids[] = $category->id;
        }

        return $ids;
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
     * Get the Markdown version of the body text
     */
    function getHtmlBody()
    {
        // get html from markdown
        $html = Markdown::defaultTransform( $this->body );

        // process any newlines
        $html = str_replace( '#NL#', '<br>', $html );

        // process any youtube videos
        $youtubeEmbed = sprintf(
            '<iframe width="%s" height="%s" src="%s/%s?rel=0" '.
                'frameborder="0" allowfullscreen></iframe>',
            '670',
            '380',
            '//www.youtube.com/embed',
            '$1' ); // preg variable of video ID
        $html = preg_replace( "/\[\#youtube:(.*?)\]/", $youtubeEmbed, $html );

        // process any vimeo videos
        $vimeoEmbed = sprintf(
            '<iframe src="%s/%s?%s" width="%s" height="%s"' .
                'frameborder="0" allowfullscreen></iframe>',
            '//player.vimeo.com/video',
            '$1', // preg variable of video ID
            'title=0&portrait=0&badge=0',
            '670',
            '380' );
        $html = preg_replace( "/\[\#vimeo:(.*?)\]/", $vimeoEmbed, $html );

        // process any soundcloud links
        $soundcloudEmbed = sprintf(
            '<iframe width="%s" height="%s" scrolling="no" frameborder="no" '.
                'src="%s/%s%s"></iframe>',
            '100%',
            '166',
            'https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks',
            '$1', // preg variable of cloud ID
            '&amp;color=ff5500&amp;auto_play=false&amp;hide_related=false&amp;show_artwork=true' );
        $html = preg_replace( "/\[\#soundcloud:(.*?)\]/", $soundcloudEmbed, $html );

        // process any images
        $img = '<img src="$1" alt="" title="" />';
        $html = preg_replace( "/\[\#image:(.*?)\]/", $img, $html );

        return $html;
        //return Markdown::defaultTransform( nl2br( $this->body ) );
    }

    /**
     * Get the user object that posted this
     */
    function getAuthor()
    {
        if ( ! is_null( $this->author ) )
        {
            return $this->author;
        }

        if ( ! $this->user_id )
        {
            return FALSE;
        }

        $cache = $this->getService( 'cache' );
        $keyName = 'user:'. $this->user_id;

        if ( $cache->getLocal( $keyName ) )
        {
            $this->author = $cache->getLocal( $keyName );
            return $this->author;
        }

        $this->author = \Db\Sql\Users::findFirst([
            'id = :id:',
            'bind' => [
                'id' => $this->user_id ]
            ]);
        $this->author = $this->author->toArray();
        $cache->setLocal( $keyName, $this->author );

        return $this->author;
    }

    /**
     * Generate a slug based on the title
     */
    function generateSlug()
    {
        // replace non letter or digits by -, then trim, transliterate
        // utf8 characters, lowercase it, and remove unwanted characters.
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

    /**
     * Get the event date. if the event is ongoing (i.e. has an
     * end date) and now() is later than the start date, display
     * today's date as the event date. if now() is later than the
     * ending date use the ending date.
     */
    function getEventTime()
    {
        $now = gmmktime( 0, 0, 0 );
        $startTime = strtotime( $this->event_date );

        if ( ! $startTime || ! $this->event_date )
        {
            return NULL;
        }

        if ( valid( $this->event_date_end, STRING ) )
        {
            $endTime = strtotime( $this->event_date_end );

            if ( $now <= $startTime )
            {
                return $startTime;
            }

            if ( $now >= $endTime )
            {
                return $endTime;
            }

            return $now;
        }

        return $startTime;
    }

    /**
     * Returns the number of days the event spans
     */
    function getDaySpan()
    {
        if ( ! $this->event_date )
        {
            return 0;
        }

        if ( ! $this->event_date_end )
        {
            return 1;
        }

        $endTime = strtotime( $this->event_date_end );
        $startTime = strtotime( $this->event_date );
        $daySeconds = 60 * 60 * 24;

        return floor( ( $endTime - $startTime ) / $daySeconds ) + 1;
    }

    /**
     * Generate a short date string for the event's start and end date
     */
    function getShortDate( $html = TRUE, $defaultToPostDate = FALSE )
    {
        if ( ! valid( $this->event_date, STRING ) )
        {
            return ( $defaultToPostDate )
                ? date( DATE_SHORT_DAY_NAME, strtotime( $this->post_date ) )
                : "";
        }

        $startTime = strtotime( $this->event_date );
        $dateFormat = ( date( 'Y', $startTime ) != date( 'Y' ) )
            ? DATE_SHORT_DAY_NAME_YEAR
            : DATE_SHORT_DAY_NAME;
        $string = date( $dateFormat, $startTime );

        if ( valid( $this->event_date_end, STRING ) )
        {
            $string .= ( $html ) ? " &ndash; " : ' - ';
            $string .= date( $dateFormat, strtotime( $this->event_date_end ) );
        }

        return $string;
    }

    /**
     * Return a shortened version of the event's start and stop time
     */
    function getShortTime( $html = TRUE, $withLocation = FALSE, $withPrice = FALSE )
    {
        $string = "";

        if ( valid( $this->event_time, STRING ) )
        {
            $string = ( substr( $this->event_time, 3, 2 ) == '00' )
                ? date( 'ga', strtotime( $this->event_time ) )
                : date( 'g:ia', strtotime( $this->event_time ) );

            if ( $html )
            {
                $string = '<span class="eventtime">'. $string .'</span>';
            }

            if ( valid( $this->event_time_end, STRING ) )
            {
                $string .= ( $html ) ? " &ndash; " : ' - ';
                $endTime = ( substr( $this->event_time_end, 3, 2 ) == '00' )
                    ? date( 'ga', strtotime( $this->event_time_end ) )
                    : date( 'g:ia', strtotime( $this->event_time_end ) );
                $string .= ( $html )
                    ? '<span class="eventtime">'. $endTime .'</span>'
                    : $endTime;
            }
        }

        // add location if specified
        if ( $withLocation && valid( $this->location, STRING ) )
        {
            $string = ( $html )
                ? trim( $string .' @ <span class="location">'. $this->location .'</span>' )
                : trim( $string ." @ ". $this->location );
        }

        // add price id specified
        if ( $withPrice
            && valid( $this->price, STRING )
            && $this->price != 0 )
        {
            $string = ( $html )
                ? trim( '<span class="price">$'. $this->price .'</span>'. $string )
                : trim( $this->price .", ". $string );
        }

        return $string;
    }

    /**
     * Determines if the external link is a facebook url
     */
    function urlIsFacebook()
    {
        return strstr( $this->external_url, "facebook.com" );
    }
}
