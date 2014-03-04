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
            && ! valid( $params, 'startDate', DATE ) )
        {
            $params[ 'startDate' ] = date(
                DATE_DATABASE, 
                strtotime( $params[ 'startDate' ] ) );
        }

        if ( isset( $params[ 'endDate' ] )
            && ! valid( $params, 'endDate', DATE ) )
        {
            $params[ 'endDate' ] = date(
                DATE_DATABASE, 
                strtotime( $params[ 'endDate' ] ) );
        }

        return \Db\Sql\Posts::getByCategoryDateRange( $params );
    }

    /**
     * Get events for a particular month.
     *
     * @param string $dateString
     * @param array $categories
     * @return array \Db\Sql\Posts
     */
    function getByMonth( $dateString, $categories )
    {
        // get the month from the date string
        //
        $time = strtotime( $dateString );
        $month = date( 'n', $time );
        $year = date( 'Y', $time );
        $startDate = date( DATE_DATABASE, mktime( 0, 0, 0, $month, 1, $year ) );
        $endDate = date( DATE_DATABASE_END_OF_MONTH, $time );

        // return the results
        //
        return \Db\Sql\Posts::getByCategoryDateRange([
            'startDate' => $startDate,
            'endDate' => $endDate,
            'categories' => $categories ]);
    }
}