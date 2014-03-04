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
        // for the current month's calendar.
        //
        $action = new \Actions\Posts\Event();
        $this->view->upcomingEvents = $action->getByDateRange([
            'categories' => [ EVENTS ] ]);
        $this->view->calendarEvents = $action->getByMonth(
            'today',
            [ EVENTS ]);
        $this->view->pageNav = [
            'partial' => 'partials/events/nav',
            'page' => 'upcoming' ];

        $this->view->pick( 'events/upcoming' );
    }

    public function archivesAction()
    {
        // fetch the last month of events
        //
        $action = new \Actions\Posts\Event();
        $this->view->events = $action->getByDateRange([
            'categories' => [ EVENTS ],
            'startDate' => NULL,
            'endDate' => 'today',
            'sort' => 'event_date desc',
            'limit' => 10 ]);
        $this->view->pageNav = [
            'partial' => 'partials/events/nav',
            'page' => 'archives' ];

        $this->view->pick( 'events/archives' );
    }

    public function exhibitionsAction()
    {
        // fetch any current exhibitions and 5 of the previous
        // exhibitions.
        //
        $action = new \Actions\Posts\Event();
        $this->view->currentExhibitions = $action->getByDateRange([
            'categories' => [ EXHIBITIONS ],
            'ongoing' => TRUE,
            'startDate' => 'today',
            'endDate' => 'today',
            'startOperand' => '<=',
            'endOperand' => '>=' ]);
        $this->view->pastExhibitions = $action->getByDateRange([
            'categories' => [ EXHIBITIONS ],
            'ongoing' => TRUE,
            'startDate' => NULL,
            'endDate' => 'today',
            'limit' => 5 ]);
        $this->view->pageNav = [
            'partial' => 'partials/events/nav',
            'page' => 'exhibitions' ];

        $this->view->pick( 'events/exhibitions' );
    }

    /**
     * json call to fetch month's events for calendar
     */
    public function getbymonthAction()
    {

    }

    public function bookingAction()
    {
        $this->view->pick( 'events/booking' );
    }
}