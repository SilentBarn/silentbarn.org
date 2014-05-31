<?php

namespace Db\Sql;

class Spaces extends \Base\Model
{
    public $id;
    public $name;
    public $bio;
    public $subtitle;
    public $email;
    public $website;
    public $image_filename;
    public $is_residence;
    public $is_stewdio;
    public $is_gallery;
    public $is_active;
    public $is_deleted;
    public $created_at;

    function initialize()
    {
        $this->setSource( 'spaces' );
        $this->addBehavior( 'timestamp' );
    }

    /**
     * Returns profile picture
     */
    function getImagePath( $public = TRUE )
    {
        if ( ! $this->hasImage() )
        {
            return "";
        }

        $config = $this->getService( 'config' );

        return sprintf(
            "%s%s/%s",
            ( $public )
                ? $config->paths->mediaPublic
                : $config->paths->media,
            "spaces",
            $this->image_filename );
    }

    function hasImage()
    {
        return valid( $this->image_filename, STRING );
    }

    /**
     * Get the Markdown version of the body text
     */
    function getHtmlBio()
    {
        return Markdown::defaultTransform( nl2br( $this->bio ) );
    }
}
