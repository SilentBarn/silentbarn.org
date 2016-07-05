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
        $page = \Db\Sql\Pages::findFirstByName( 'press' );

        // Read in content from admin page
        $content[ 'about' ] = $filter->sanitize( get( $data, 'about' ), 'striptags' );
        $content[ 'video' ] = $filter->sanitize( get( $data, 'video' ), 'striptags' );

        // Process the video. this comes in as a URL and we need to
        // write out an embed object based on the URL that comes in
        if ( strpos( $content[ 'video' ], 'youtube.com' ) ) {
            // Get the code
            $pieces = explode( 'watch?v=', $content[ 'video' ] );

            if ( count( $pieces ) < 2 ) {
                $util->addMessage(
                    "You didn't enter a valid YouTube URL",
                    ERROR );
                return FALSE;
            }

            $content[ 'embed' ] = sprintf(
                '<iframe width="%s" height="%s" src="%s/%s" frameborder="0"></iframe>',
                560,
                315,
                "//www.youtube.com/embed/",
                get( $pieces, 1 ));
        }

        $page->content = serialize( $content );

        if ( ! $this->save( $page ) ) {
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
        if ( $page->save() == FALSE ) {
            $util = $this->getService( 'util' );
            $util->addMessage(
                "There was a problem saving your press content.",
                INFO );

            foreach ( $page->getMessages() as $message ) {
                $util->addMessage( $message, ERROR );
            }

            return FALSE;
        }

        return TRUE;
    }
}