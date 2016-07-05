<?php

namespace Actions\Pages;

use \Db\Sql\Pages as Pages;

class Page extends \Base\Action
{
    /**
     * Saves the page content
     *
     * @param array $data
     * @return boolean | \Db\Sql\Page
     */
    public function edit( $data )
    {
        $name = get( $data, 'name' );
        $util = $this->getService( 'util' );
        $page = \Db\Sql\Pages::findFirstByName( $name );

        if ( ! $page ) {
            $util->addMessage(
                "The ". $name . " page could not be found.",
                INFO );
            return FALSE;
        }

        $page->content = get( $data, 'content', '' );

        if ( ! $this->save( $page ) ) {
            return FALSE;
        }

        return $page;
    }

    /**
     * Saves the page content, error handles.
     *
     * @param \Db\Sql\Pages $page
     * @return boolean
     */
    private function save( &$page )
    {
        if ( $page->save() == FALSE ) {
            $util = $this->getService( 'util' );
            $util->addMessage(
                "There was a problem saving your page content.",
                INFO );

            foreach ( $page->getMessages() as $message ) {
                $util->addMessage( $message, ERROR );
            }

            return FALSE;
        }

        return TRUE;
    }
}