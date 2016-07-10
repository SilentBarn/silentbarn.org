<?php

namespace Actions\Pages;

use \Db\Sql\Pages as Pages;

class Page extends \Base\Action
{
    /**
     * Saves the page content.
     *
     * @param array $data
     * @return boolean | \Db\Sql\Page
     */
    public function edit( $data )
    {
        $name = get( $data, 'name' );
        $util = $this->getService( 'util' );
        $page = Pages::findFirstByName( $name );

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
     * Saves the home page content in a specific format.
     *
     * @param array $data
     * @return boolean | \Db\Sql\Page
     */
    public function editHomepage( $data )
    {
        $boxes = get( $data, 'boxes', [] );
        $util = $this->getService( 'util' );
        $slider = get( $data, 'slider', [] );
        $cafeDays = get( $data, 'cafe_days', [] );
        $page = Pages::findFirstByName( Pages::HOMEPAGE );

        if ( ! $page ) {
            $util->addMessage( "The homepage could not be found.", INFO );
            return FALSE;
        }

        if ( ! $this->save( $page ) ) {
            return FALSE;
        }

        // Check of the boxes keys were set
        $requiredBoxes = [
            NEWS, EVENTS, EXHIBITIONS, COMMUNITY, ARTICLES
        ];
        $shouldBeEmpty = array_intersect_key(
            array_flip( $requiredBoxes ),
            $boxes );

        if ( ! $shouldBeEmpty ) {
            $util->addMessage( "You need to add a post for every box." );
            return FALSE;
        }

        // Check if a post came in for all boxes
        foreach ( $boxes as $category => $id ) {
            if ( ! $id ) {
                $util->addMessage(
                    "Please select an article for every box!" );
                return FALSE;
            }
        }

        // Clean out slider
        $slider = array_unique( array_filter( $slider ) );

        // Check if there's at least 1 slider post
        if ( ! count( $slider ) ) {
            $util->addMessage(
                "Please select at least one article for the slider!" );
            return FALSE;
        }

        // Check for weirdness in cafe hours
        if ( count( $cafeDays ) !== 7 ) {
            $util->addMessage(
                "You didn't sent the cafe hours correctly." );
            return FALSE;
        }

        // Store this as a serialized array
        $page->content = @serialize([
            'boxes' => $boxes,
            'slider' => $slider,
            'cafe_days' => $cafeDays
        ]);

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