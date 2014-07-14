<?php

namespace Controllers;

class EventsController extends \Base\Controller
{
    public function beforeExecuteRoute()
    {
        $this->checkLoggedIn = FALSE;

        return parent::beforeExecuteRoute();
    }

    public function indexAction()
    {
        return $this->dispatcher->forward([
            'controller' => 'events',
            'action' => 'upcoming'
        ]);
    }

    public function upcomingAction()
    {
        // get the EST time
        $dateTime = new \DateTime();
        $dateTime->setTimeZone( new \DateTimeZone( 'America/New_York' ) );

        // now, we actually want to show events from yesterday if
        // it's still earlier than 4am
        if ( $dateTime->format( 'G' ) <= 4 )
        {
            $dateTime->sub( new \DateInterval( 'P1D' ) );
        }

        // fetch the events for the next two weeks, and the events
        // for the current month's calendar
        $action = new \Actions\Posts\Event();
        $limit = 7;
        $offset = $this->request->getQuery( 'o' );
        $offset = ( valid( $offset ) ) ? $offset : 0;
        $this->data->upcomingEvents = $action->getByDateRange([
            'limit' => $limit,
            'offset' => $offset,
            'startDate' => $dateTime->format( DATE_YEAR_MONTH_DAY ),
            'categories' => [ EVENTS ] ]);
        $this->data->calendarEvents = $action->getByMonth(
            'today',
            [ EVENTS ]);
        $this->data->pageNav = [
            'partial' => 'partials/events/nav',
            'page' => 'upcoming' ];
        $this->data->limit = $limit;
        $this->data->pageTitle = "Upcoming Events";
        $this->view->pick( 'events/upcoming' );

        // if an offset came in, return the API response
        if ( valid( $offset ) )
        {
            $this->responseMode = 'api';
            $this->data->count = count( $this->data->upcomingEvents );
            $this->data->html = $this->renderPartial(
                'partials/events/list_full', [
                    'events' => $this->data->upcomingEvents,
                    'offset' => $offset
                ]);
        }
    }

    /**
     * This method can take in various GET parameters:
     *    query, type, artist, dates
     * These are used when the form is submitted and will
     * change the view to show results.
     *
     * @param string $flag Will be 'search' to show results
     */
    public function archivesAction( $flag = "" )
    {
        // set up global page data
        $this->data->pageNav = [
            'partial' => 'partials/events/nav',
            'page' => 'archives' ];
        $this->data->pageTitle = "Event Archives";
        $queryParams = $this->request->getQuery();

        // only show the 'clear' link when no params came in
        unset( $queryParams[ '_url' ] );
        $filteredParams = array_filter(
            $queryParams,
            function ( $var ) {
                return valid( $var, STRING );
            });
        $this->data->showClear = count( $filteredParams ) > 0;

        // if we have a query string coming in, perform
        // the search
        if ( str_eq( $flag, 'search' ) )
        {
            // save GET params for the form
            $params = [
                'keywords' => get( $queryParams, 'q', '' ),
                'artist' => get( $queryParams, 'a', '' ),
                'type' => get( $queryParams, 't', 'events' ),
                'date_start' => get( $queryParams, 'ds', '' ),
                'date_end' => get( $queryParams, 'de', '' )];

            // fetch the last month of events
            $action = new \Actions\Posts\Event();
            $limit = 9;
            $offset = get( $queryParams, 'o' );
            $offset = ( valid( $offset ) ) ? $offset : 0;
            $this->data->events = $action->search([
                'category' => $params[ 'type' ],
                'artist' => $params[ 'artist' ],
                'keywords' => $params[ 'keywords' ],
                'startDate' => $params[ 'date_start' ],
                'endDate' => $params[ 'date_end' ],
                'limit' => $limit,
                'offset' => $offset ]);
            $this->data->limit = $limit;
            $this->data->params = $params;
            $this->view->pick( 'events/archives_results' );

            // if an offset came in, return the API response
            if ( valid( $offset ) )
            {
                $this->responseMode = 'api';
                $this->data->count = count( $this->data->events );
                $this->data->html = $this->renderPartial(
                    'partials/events/list_archive', [
                        'events' => $this->data->events,
                        'offset' => $offset
                    ]);
            }
        }
        // show the intro and form
        else
        {
            $this->view->pick( 'events/archives' );
        }
    }

    public function exhibitionsAction()
    {
        // fetch any current exhibitions and 5 of the previous
        // exhibitions.
        $action = new \Actions\Posts\Event();
        $this->data->currentExhibitions = $action->getByDateRange([
            'categories' => [ EXHIBITIONS ],
            'ongoing' => TRUE,
            'startDate' => 'today',
            'startOperand' => '>=' ]);
        $this->data->pastExhibitions = $action->getByDateRange([
            'categories' => [ EXHIBITIONS ],
            'ongoing' => TRUE,
            'startDate' => NULL,
            'endDate' => 'today',
            'sort' => 'event_date desc',
            'limit' => 5 ]);
        $this->data->pageNav = [
            'partial' => 'partials/events/nav',
            'page' => 'exhibitions' ];
        $this->data->pageTitle = "Exhibitions";

        $this->view->pick( 'events/exhibitions' );
    }

    /**
     * List all events by month (last 3 months) in ASCII format
     */
    public function asciiAction()
    {
        $action = new \Actions\Posts\Event();
        $this->data->events = $action->getByDateRange([
            'categories' => [ EVENTS ] ]);
        $this->data->pageTitle = "ASCII Calendar";
        $this->view->setRenderLevel( \Phalcon\Mvc\View::LEVEL_ACTION_VIEW );
    }

    /**
     * json call to fetch month's events for calendar
     */
    public function getbymonthAction()
    {
        $this->responseMode = 'api';
        $date = $this->request->getPost( 'date' );

        // get the events for the month by the date given
        $action = new \Actions\Posts\Event();
        $events = $action->getByMonth(
            $date,
            [ EVENTS ]);
        $jsonEvents = array();

        foreach ( $events as $event )
        {
            $jsonEvents[] = [
                'title' => str_replace( "'", "\'", $event->title ), 
                'date' => date( DATE_DAY_NAME_YEAR, strtotime( $event->event_date ) ),
                'url' => $event->getPath(),
                'image' => $event->getImage()->getPath( 310 ) ];
        }

        $this->data->events = $jsonEvents;
    }

    /**
     * If $flag = 'thankyou' then show a thank you message
     * @param string $flag
     */
    public function bookingAction( $flag = "" )
    {
        if ( str_eq( $flag, "thankyou" ) )
        {
            $this->data->notifications[] = [
                'success' => 
                    "Your inquiry has successfully been sent! We'll ".
                    "contact you shortly." ];
        }

        $this->data->pageTitle = "Performance Booking";
        $this->view->pick( 'events/booking' );
    }

    /**
     * POST request to send an email to the bookings admin
     */
    public function bookinginquiryAction()
    {
        // read in the post data and send the email out
        $data = $this->request->getPost();
        $action = new \Actions\Email();
        $action->event( $data );

        // redirect to thank you page
        $this->redirect = "events/booking/thankyou";
    }
}