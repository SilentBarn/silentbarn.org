<?php

namespace Actions\Posts;

class Post extends \Base\Action
{
    /**
     * Creates a new blank post
     *
     * @return integer Post ID
     */
    public function create()
    {
        $auth = $this->getService( 'auth' );
        $post = new \Db\Sql\Posts();
        $post->user_id = $auth->getUserId();
        $post->is_deleted = 0;
        $post->status = 'draft';

        if ( ! $this->save( $post ) )
        {
            return FALSE;
        }

        return $post->id;
    }

    /**
     * Saves a post
     *
     * @param array $data
     * @return boolean
     */
    public function edit( $data )
    {
        $util = $this->getService( 'util' );
        $filter = $this->getService( 'filter' );

        // check the post ID and verify that this post exists
        //
        if ( ! isset( $data[ 'id' ] )
            || ! valid( $data[ 'id' ], INT ) )
        {
            $util->addMessage( "You didn't specify a post ID.", INFO );
            return FALSE;
        }

        $post = \Db\Sql\Posts::findFirst( $data[ 'id' ] );

        if ( ! $post )
        {
            $util->addMessage( "That post couldn't be found.", INFO );
            return FALSE;
        }

        // apply the data params to the post and save it
        //
        $post->title = $filter->sanitize( get( $data, 'title' ), 'string' );
        $post->body = $filter->sanitize( get( $data, 'body' ), 'striptags' );
        $post->post_date = date_str(
            get( $data, 'post_date' ),
            DATE_DATABASE,
            TRUE );
        $post->event_date = date_str(
            get( $data, 'event_date' ),
            DATE_DATABASE,
            TRUE );

        // set up status filter
        //
        $filter->add(
            'status',
            function ( $value ) {
                return ( in_array( $value, [ 'draft', 'published' ] ) )
                    ? $value
                    : 'draft';
            });
        $post->status = $filter->sanitize( get( $data, 'status' ), 'status' );

        // set up homepage loc filter
        //
        $filter->add(
            'homepageLocation',
            function ( $value ) {
                return ( in_array( $value, [ 'hero', 'boxes' ] ) )
                    ? $value
                    : NULL;
            });
        $post->homepage_location = $filter->sanitize(
            get( $data, 'homepage_location' ),
            'homepageLocation' );

        if ( ! $this->save( $post ) )
        {
            return FALSE;
        }

        return $post;
    }

    /**
     * Deletes a post, i.e. marks it is_deleted
     */
    public function delete( $id )
    {
        $util = $this->getService( 'util' );
        $post = \Db\Sql\Posts::findFirst( $id );

        if ( ! $post )
        {
            $util->addMessage( "That post couldn't be found.", INFO );
            return FALSE;
        }

        $post->is_deleted = 1;

        return $this->save( $post );
    }

    /**
     * Saves a post, error handles
     *
     * @param \Db\Sql\Post $post
     * @return
     */
    private function save( &$post )
    {
        if ( $post->save() == FALSE )
        {
            $util = $this->getService( 'util' );
            $util->addMessage(
                "There was a problem creating your post.",
                INFO );

            foreach ( $post->getMessages() as $message )
            {
                $util->addMessage( $message, ERROR );
            }

            return FALSE;
        }

        return TRUE;
    }
}