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
        // check the post ID and verify that this post exists
        //

        // apply the data params to the post and save it
        //
        
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