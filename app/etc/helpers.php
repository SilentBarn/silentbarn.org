<?php

/**
 * Chcek if two values are numerically the same. Casts the
 * arguments as integers to type check.
 *
 * @param integer $arg_1
 * @param integer $arg_2
 * @return boolean
 */
function int_eq( $arg_1, $arg_2 )
{
    return (int) $arg_1  === (int) $arg_2;
}

/**
 * Chcek if two values are equal string. Casts the
 * arguments as strings to type check.
 *
 * @param string $arg_1
 * @param string $arg_2
 * @return boolean
 */
function str_eq( $arg_1, $arg_2 )
{
    return "". $arg_1 === "". $arg_2;
}

/**
 * Returns the indexed element for the mixed object. You can specify
 * a default value to return in $default and specify if you want to
 * just find out of the index exists ($check_index_exists). The latter
 * will return a boolean.
 *
 * @param mixed $object
 * @param string $index
 * @param mixed $default
 * @param boolean $check_index_exists
 * @return boolean or mixed
 */
function get( $object, $index, $default = FALSE, $check_index_exists = FALSE )
{
    if ( is_array( $object ) )
    {
        if ( isset( $object[ $index ] ) )
        {
            return ( $check_index_exists )
                ? TRUE
                : $object[ $index ];
        }
    }
    else
    {
        if ( isset( $object->$index ) )
        {
            return ( $check_index_exists )
                ? TRUE
                : $object->$index;
        }
    }

    return $default;
}

/**
 * Checks if the variable is of the specified type and a valid value
 *
 * @param mixed $mixed
 * @param string $expected_type
 * @return boolean
 */
function valid( $mixed, $expected_type = INT )
{
    if ( is_numeric( $mixed ) || $expected_type === INT )
    {
        return is_numeric( $mixed )
            && strlen( $mixed )
            && (int) $mixed > 0;
    }
    elseif ( $expected_type === STRING )
    {
        return is_string( $mixed )
            && strlen( $mixed ) > 0;
    }
    elseif ( $expected_type === DATE )
    {
        // check if date is of form YYYY-MM-DD HH:MM:SS and that it
        // is not 0000-00-00 00:00:00.
        if ( strlen( $mixed ) === 19
            && ! str_eq( $mixed, '0000-00-00 00:00:00' ) )
        {
            return TRUE;
        }

        // check for MM/DD/YYYY type dates
        $parts = explode( "/", $mixed );

        return count( $parts ) === 3
            && checkdate( $parts[ 0 ], $parts[ 1 ], $parts[ 2 ] );
    }
    elseif ( $expected_type === VECTOR )
    {
        return is_array( $mixed )
            && count( array_filter( $mixed ) ) > 0;
    }
    elseif ( $expected_type === OBJECT )
    {
        if ( ! is_object( $mixed ) )
        {
            return FALSE;
        }

        // iterate through object and check if there are any properties
        foreach ( $mixed as $property )
        {
            if ( $property )
            {
                return TRUE;
            }
        }
    }

    return FALSE;
}

/**
 * Converts a size in bytes to something human readable.
 */
function human_bytes( $bytes, $precision = 2 )
{
    $unit = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB' );

    if ( ! $bytes )
    {
        return "0 B";
    }

    return @round(
        $bytes / pow( 1024, ( $i = floor( log( $bytes, 1024 ) ) ) ), $precision ).
        ' '. $unit[ $i ];
}

/**
 * Returns a list of columns from an array of arrays
 */
function map( $objects, $key = 'id', $listify = FALSE )
{
    $mapped = \__::map(
        $objects,
        function ( $object ) use ( $key ) {
            return $object[ $key ];
        });

    return ( $listify )
        ? implode( ', ', $mapped )
        : $mapped;
}

/**
 * Outputs a date formatted string
 */
function date_str( $datetime, $format = DATE_TABLE, $nullOnEmpty = FALSE, $local = FALSE )
{
    if ( ! $datetime && $nullOnEmpty )
    {
        return NULL;
    }

    if ( $local )
    {
        $timestamp = new \DateTime(
            $datetime,
            new DateTimeZone( 'UTC' ) );
        $timestamp->setTimezone( new DateTimeZone( 'America/New_York' ) );
    }
    else
    {
        $timestamp = new \DateTime( $datetime );
    }

    return $timestamp->format( $format );
}

/**
 * Cleans a string of text for safe insertion into
 * an HTML field.
 */
function clean_str( $string )
{
    return htmlspecialchars( $string );
}

/**
 * Returns First L. style name
 */
function short_name( $string )
{
    $pieces = explode( " ", $string );

    if ( ! $pieces )
    {
        return "N/A";
    }

    if ( count( $pieces ) === 1 )
    {
        return trim( $pieces[ 0 ] );
    }

    return $pieces[ 0 ] ." ". substr( $pieces[ 1 ], 0, 1 ) .".";
}

/**
 * Returns a 'read more' excerpt for text
 */
function read_more( $text, $length )
{
    if ( strlen( $text ) <= $length )
    {
        return $text;
    }

    return sprintf(
        '%s<span>&nbsp;[<a href="%s" class="%s">%s&hellip;</a>]</span>'.
        '<span class="%s">%s</span>',
        substr( $text, 0, $length ),
        "javascript:;",
        "read-more",
        "more",
        "more-text",
        substr( $text, $length, strlen( $text ) - $length ) );
}