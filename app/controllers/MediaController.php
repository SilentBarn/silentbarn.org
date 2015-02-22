<?php

namespace Controllers;

/**
 * This controller is a fallback for /media assets or paths
 * that don't exist. Currently, 'members' and 'spaces' are
 * reserved from use. There's also a server rewrite for /media
 * to go back to the home page.
 *
 * A proper solution would be to not use the word 'media' for
 * the base of this controller but instead pick a different
 * stem.
 */
class MediaController extends \Base\Controller
{
    public function beforeExecuteRoute()
    {
        $this->checkLoggedIn = FALSE;

        return parent::beforeExecuteRoute();
    }

    /**
     * Display all media posts with an audio file
     */
    public function radioAction()
    {
        // get the audio posts
        $limit = 10;
        $offset = $this->request->getQuery( 'o' );
        $offset = ( valid( $offset ) ) ? $offset : 0;
        $this->view->posts = \Db\Sql\Posts::search([
            'isDeleted' => 0,
            'categories' => [ MEDIA ],
            'media' => [ MEDIA_AUDIO ],
            'limit' => $limit,
            'offset' => 0,
            'orderBy' => 'post_date desc, id desc'
        ]);

        // load events for the calendar
        $action = new \Actions\Posts\Media();
        $this->data->calendarEvents = $action->getByMonth(
            'today',
            [ MEDIA_AUDIO ]);

        $this->data->limit = $limit;
        $this->data->pageTitle = "Radio";
        $this->view->pick( 'media/radio' );

        // if an offset came in, return the API response
        if ( valid( $offset ) )
        {
            $this->responseMode = 'api';
            $this->data->count = count( $this->data->posts );
            $html = $this->renderPartial(
                'partials/media/audio_list_full', [
                    'events' => $this->data->posts,
                    'offset' => $offset
                ]);
            $this->data->html = utf8_encode( $html ); 
        }
    }

        /**
     * json call to fetch month's events for calendar
     */
    public function getradiobymonthAction()
    {
        $this->responseMode = 'api';
        $date = $this->request->getPost( 'date' );

        // get the events for the month by the date given
        $action = new \Actions\Posts\Media();
        $events = $action->getByMonth(
            $date,
            [ MEDIA_AUDIO ]);
        $jsonEvents = array();

        foreach ( $events as $event )
        {
            $jsonEvents[] = [
                'title' => str_replace( "'", "\'", $event->title ), 
                'date' => date( DATE_DAY_NAME_YEAR, strtotime( $event->post_date ) ),
                'url' => $event->getPath(),
                'image' => $event->getImage()->getImagePath( 310 ) ];
        }

        $this->data->events = $jsonEvents;
    }
}