<?php

namespace Actions\Spaces;

use \Db\Sql\Spaces as Spaces;

class Space extends \Base\Action
{
    /**
     * Creates a new blank space
     *
     * @return integer Space ID
     */
    public function create()
    {
        $space = new Spaces();
        $space->initialize();
        $space->is_deleted = 0;
        $space->is_active = 1;

        if ( ! $this->save( $space ) )
        {
            return FALSE;
        }

        return $space->id;
    }

    /**
     * Saves a space
     *
     * @param array $data
     * @return boolean
     */
    public function edit( $data )
    {
        $util = $this->getService( 'util' );
        $filter = $this->getService( 'filter' );

        // check the space ID and verify that this space exists
        if ( ! isset( $data[ 'id' ] )
            || ! valid( $data[ 'id' ], INT ) )
        {
            $util->addMessage( "You didn't specify a space ID.", INFO );
            return FALSE;
        }

        $space = Spaces::findFirst( $data[ 'id' ] );

        if ( ! $space )
        {
            $util->addMessage( "That space couldn't be found.", INFO );
            return FALSE;
        }

        $space->name = $filter->sanitize( get( $data, 'name' ), 'striptags' );
        $space->subtitle = $filter->sanitize( get( $data, 'subtitle' ), 'striptags' );
        $space->email = $filter->sanitize( get( $data, 'email' ), 'striptags' );
        $space->website = $filter->sanitize( get( $data, 'website' ), 'striptags' );
        $space->bio = $filter->sanitize( get( $data, 'bio' ), 'striptags' );
        $space->is_residence = ( get( $data, 'is_residence' ) ) ? 1 : 0;
        $space->is_stewdio = ( get( $data, 'is_stewdio' ) ) ? 1 : 0;
        $space->is_gallery = ( get( $data, 'is_gallery' ) ) ? 1 : 0;
        $space->is_active = ( get( $data, 'is_active' ) ) ? 1 : 0;

        if ( ! $this->save( $space ) )
        {
            return FALSE;
        }

        return $space;
    }

    /**
     * Deletes a space, i.e. marks it is_deleted
     */
    public function delete( $id )
    {
        $util = $this->getService( 'util' );
        $space = Spaces::findFirst( $id );

        if ( ! $space )
        {
            $util->addMessage( "That space couldn't be found.", INFO );
            return FALSE;
        }

        $space->is_deleted = 1;

        return $this->save( $space );
    }

    /**
     * Saves a space, error handles
     *
     * @param \Db\Sql\Spaces $space
     * @return
     */
    private function save( &$space )
    {
        if ( $space->save() == FALSE )
        {
            $util = $this->getService( 'util' );
            $util->addMessage(
                "There was a problem saving your space.",
                INFO );

            foreach ( $space->getMessages() as $message )
            {
                $util->addMessage( $message, ERROR );
            }

            return FALSE;
        }

        return TRUE;
    }
}