<?php

namespace Actions\Pages;

use \Db\Sql\Pages as Pages;

class Press extends \Base\Action
{
    /**
     * Saves the press page
     *
     * @param array $data
     * @return boolean
     */
    public function edit( $data )
    {
        $util = $this->getService( 'util' );
        $filter = $this->getService( 'filter' );

        // set up the 3 fields -- static content, links, video embed code
        //

        // serialize them and store in pages table
        //
        $content[ 'name' ] = $filter->sanitize( get( $data, 'name' ), 'striptags' );
        $content[ 'email' ] = $filter->sanitize( get( $data, 'email' ), 'striptags' );
        $content[ 'bio' ] = $filter->sanitize( get( $data, 'bio' ), 'striptags' );

        if ( ! $this->save( $page ) )
        {
            return FALSE;
        }

        return $page;
    }

    /**
     * Saves the press page content, error handles
     *
     * @param \Db\Sql\Pages $page
     * @return
     */
    private function save( &$page )
    {
        if ( $page->save() == FALSE )
        {
            $util = $this->getService( 'util' );
            $util->addMessage(
                "There was a problem saving your press content.",
                INFO );

            foreach ( $page->getMessages() as $message )
            {
                $util->addMessage( $message, ERROR );
            }

            return FALSE;
        }

        return TRUE;
    }
}