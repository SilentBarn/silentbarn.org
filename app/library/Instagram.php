<?php

namespace Lib;

use Httpful\Request as Request,
    Db\Sql\Settings as Settings;

/**
 * Fetches recent media from Instagram account
 */
class Instagram extends \Base\Library
{
    /**
     * Get recent media from the instagram account
     */
    public static function getMedia( $count = 5 )
    {
        $config = self::getStaticService( 'config' );
        $url = sprintf(
            $config->instagram->mediaUrl,
            $config->instagram->userId,
            $config->instagram->clientId,
            $count );

        // get the response
        try
        {
            $response = Request::get( $url )
                ->timeout( 5 )
                ->send();

            if ( ! $response->hasBody()
                || $response->hasErrors()
                || ! get( $response->body, 'data' ) )
            {
                return array();
            }
        }
        catch ( \Exception $e )
        {
            return array();
        }

        // iterate through the data and prepare an array of
        // objects containing the information about the instagram
        // photos.
        $photos = array();

        foreach ( $response->body->data as $item )
        {
            $photos[] = new \Base\Object([
                'link' => $item->link,
                'image' => $item->images->thumbnail->url,
                'title' => get( $item->caption, 'text', '' ) ]);
        }

        return $photos;
    }

    /**
     * Get the recent instagram photos from the SQL store
     */
    public static function get()
    {
        // check if there's an entry in the settings table. if it's
        // more than hour old, fetch a new copy from instagram and
        // update them in the database.
        $setting = Settings::get(
            0,
            'app',
            'instagram',
            [ 'first' => TRUE ]);
        $oneHourAgo = strtotime( '1 hour ago' );

        // if there's no setting create a new one
        if ( ! $setting )
        {
            $setting = new Settings();
            $setting->object_id = 0;
            $setting->object_type = 'app';
            $setting->key = 'instagram';
        }

        // if it's older than an hour, update the photos
        if ( ! $setting->created_at
            || abs( strtotime( $setting->created_at ) - $oneHourAgo ) > 3600 )
        {
            $photos = self::getMedia( 4 );
            $setting->value = serialize( $photos );
            $timestamp = new \DateTime();
            $setting->created_at = $timestamp->format( DATE_DATABASE );
            $setting->save();
        }

        return unserialize( $setting->value );
    }
}