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
        // fetch the events for the next two weeks, and the events
        // for the current month's calendar
        //
        $action = new \Actions\Posts\Event();
        $limit = 10;
        $offset = $this->request->getPost( 'offset' );
        $offset = ( valid( $offset ) ) ? $offset : 0;
        $this->data->upcomingEvents = $action->getByDateRange([
            'limit' => $limit,
            'offset' => $offset,
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
        //
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

    public function archivesAction()
    {
        // fetch the last month of events
        //
        $action = new \Actions\Posts\Event();
        $limit = 9;
        $offset = $this->request->getPost( 'offset' );
        $offset = ( valid( $offset ) ) ? $offset : 0;
        $this->data->events = $action->getByDateRange([
            'categories' => [ EVENTS ],
            'startDate' => NULL,
            'endDate' => 'today',
            'sort' => 'event_date desc',
            'limit' => $limit,
            'offset' => $offset ]);
        $this->data->limit = $limit;
        $this->data->pageNav = [
            'partial' => 'partials/events/nav',
            'page' => 'archives' ];
        $this->data->pageTitle = "Event Archives";
        $this->view->pick( 'events/archives' );

        // if an offset came in, return the API response
        //
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

    public function exhibitionsAction()
    {
        // fetch any current exhibitions and 5 of the previous
        // exhibitions.
        //
        $action = new \Actions\Posts\Event();
        $this->data->currentExhibitions = $action->getByDateRange([
            'categories' => [ EXHIBITIONS ],
            'ongoing' => TRUE,
            'startDate' => 'today',
            'endDate' => 'today',
            'startOperand' => '<=',
            'endOperand' => '>=' ]);
        $this->data->pastExhibitions = $action->getByDateRange([
            'categories' => [ EXHIBITIONS ],
            'ongoing' => TRUE,
            'startDate' => NULL,
            'endDate' => 'today',
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
        //
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

    public function bookingAction()
    {
        $this->data->pageTitle = "Performance Booking";
        $this->view->pick( 'events/booking' );
    }
}