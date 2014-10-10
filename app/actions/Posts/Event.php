<?php

namespace Actions\Posts;

class Event extends \Base\Action
{
    /**
     * Get events by type (event, exhibition) from now
     * until the specified date.
     *
     * @param array $params
     * @return array \Db\Sql\Posts
     */
    function getByDateRange( $params )
    {
        if ( isset( $params[ 'startDate' ] )
            && ! valid( $params[ 'startDate' ], DATE ) )
        {
            $params[ 'startDate' ] = date(
                DATE_DATABASE, 
                strtotime( $params[ 'startDate' ] ) );
        }

        if ( isset( $params[ 'endDate' ] )
            && ! valid( $params[ 'endDate' ], DATE ) )
        {
            $params[ 'endDate' ] = date(
                DATE_DATABASE, 
                strtotime( $params[ 'endDate' ] ) );
        }

        return \Db\Sql\Posts::getByCategoryDateRange( $params );
    }

    /**
     * Search all posts by a variety of parameters.
     *
     * @param array $params
     * @return array \Db\Sql\Posts
     */
    function search( $params )
    {
        $allowedCats = [ EVENTS, EXHIBITIONS, PRESS, NEWS ];

        // check for category, default to events
        if ( ! in_array( $params[ 'category' ], $allowedCats ) )
        {
            $params[ 'category' ] = EVENTS;
        }

        // prepare our dates
        if ( isset( $params[ 'startDate' ] )
            && valid( $params[ 'startDate' ], STRING ) )
        {
            $params[ 'startDate' ] = date(
                DATE_DATABASE, 
                strtotime( $params[ 'startDate' ] ) );
        }

        if ( isset( $params[ 'endDate' ] )
            && valid( $params[ 'endDate' ], STRING ) )
        {
            $params[ 'endDate' ] = date(
                DATE_DATABASE, 
                strtotime( $params[ 'endDate' ] ) );
        }

        return \Db\Sql\Posts::search( $params );
    }

    /**
     * Get events for a particular month.
     *
     * @param string $dateString
     * @param array $categories
     * @param boolean $breakoutOngoing When true, this will create a separate
     *        event for events that span multiple days. Useful for the calendar.
     * @return array \Db\Sql\Posts
     */
    function getByMonth( $dateString, $categories, $breakoutOngoing = TRUE )
    {
        // get the month from the date string
        $time = strtotime( $dateString );
        $month = date( 'n', $time );
        $year = date( 'Y', $time );
        $startDate = date( DATE_DATABASE, mktime( 0, 0, 0, $month, 1, $year ) );
        $endDate = date( DATE_DATABASE_END_OF_MONTH, $time );

        // get the events
        $events = \Db\Sql\Posts::getByCategoryDateRange([
            'startDate' => $startDate,
            'endDate' => $endDate,
            'categories' => $categories ]);

        // if breakoutOngoing is true, create a duplicate event for events
        // that span multiple days.
        if ( $breakoutOngoing )
        {
            $breakoutEvents = [];

            foreach ( $events as $event )
            {
                for ( $i = 0; $i < $event->getDaySpan(); $i++ )
                {
                    $clone = clone $event;
                    $clone->calendar_time = strtotime( $clone->event_date )
                        + ( ( 60 * 60 * 24 ) * $i );
                    $breakoutEvents[] = $clone;
                }
            }
        }

        // return the results
        return $breakoutEvents;
    }
}