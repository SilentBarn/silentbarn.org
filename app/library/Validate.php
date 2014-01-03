<?php

namespace Lib;

use Phalcon\Validation,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Email,
    Phalcon\Validation\Validator\StringLength;

/**
 * Validation library
 */
class Validate extends \Base\Library
{
    private static $validation;

    /**
     * Add the selected key against the tests. Tests take the form:
     *    type => params
     * where params are the values passed in to the validator.
     */
    public function add( $key, $tests )
    {
        if ( self::$validation === NULL )
        {
            self::$validation = new Validation();
        }

        foreach ( $tests as $test => $params )
        {
            $testObj = NULL;

            switch ( $test )
            {
                case 'exists':
                    $message = sprintf( "%s is required", ucfirst( $key ) );
                    $testObj = new PresenceOf(
                        array(
                            'message' => map( $params, 'message', $message )
                        ));
                    break;

                case 'email':
                    $message = "Please specify a valid email address";
                    $testObj = new Email(
                        array(
                            'message' => map( $params, 'message', $message )
                        ));
                    break;

                case 'length':
                    $message = "Please specify a valid email address";
                    $testObj = new StringLength(
                        array(
                            'message' => map( $params, 'message', $message ),
                            'min' => map( $params, 'min', 0 )
                        ));
                    break;
            }

            if ( ! $testObj )
            {
                throw new \Base\Exception( 'Invalid validation test: '. $test );
            }

            self::$validation
                ->add( $key, $testObj )
                ->setFilters( $key, 'trim' );
        }

        return TRUE;
    }

    public function run( $params )
    {
        if ( is_null( self::$validation ) )
        {
            return FALSE;
        }

        $messages = self::$validation->validate( $params );
        self::$validation = NULL;

        if ( ! count( $messages ) )
        {
            return TRUE;
        }

        // we have errors -- log them and return false
        //
        foreach ( $messages as $message )
        {
            \Lib\Util::addMessage( $message->getMessage(), ERROR );
        }

        return FALSE;
    }
}
