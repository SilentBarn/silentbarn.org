<?php

namespace Db\Sql;

class Members extends \Base\Model
{
    public $id;
    public $email;
    public $name;
    public $bio;
    public $image_filename;
    public $is_chef;
    public $is_resident;
    public $is_stewdio;
    public $is_deleted;
    public $created_at;

    function initialize()
    {
        $this->setSource( 'members' );
        $this->addBehavior( 'timestamp' );
    }

    /**
     * Returns profile picture
     */
    function getImagePath( $public = TRUE )
    {
        if ( ! valid( $this->image_filename, STRING ) )
        {
            return "";
        }

        $config = $this->getService( 'config' );

        return sprintf(
            "%s/%s/%s",
            ( $public )
                ? $config->paths->mediaPublic
                : $config->paths->media,
            "members",
            $this->image_filename );
    }
}
