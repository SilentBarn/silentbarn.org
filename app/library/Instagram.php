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
        $response = Request::get( $url )
            ->timeout( 5 )
            ->send();

        if ( ! $response->hasBody()
            || $response->hasErrors()
            || ! get( $response->body, 'data' ) )
        {
            return FALSE;
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
                'title' => $item->caption->text ]);
        }

        return $photos;
    }

    /**
     * Save the cached media into the SQL store
     */
    public function save()
    {

    }

    /**
     * Get the recent instagram photos from the SQL store
     */
    public function get()
    {

    }
}