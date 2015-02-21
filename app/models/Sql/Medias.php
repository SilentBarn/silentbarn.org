<?php

namespace Db\Sql;

use Phalcon\Mvc\Model\Query;

class Medias extends \Base\Model
{
    public $id;
    public $post_id;
    public $user_id;
    public $filename;
    public $filename_orig;
    public $ext;
    public $date_path;
    public $size;
    public $mime_type;
    public $is_deleted;
    public $created_at;

    function initialize()
    {
        $this->setSource( 'medias' );
        $this->addBehavior( 'timestamp' );
    }

    /**
     * Get the path for an image.
     *
     * @param integer $size 960 or 310
     * @return string
     */
    function getImagePath( $size = 960 )
    {
        if ( ! valid( $this->filename, STRING ) )
        {
            return '';
        }

        $config = $this->getService( 'config' );

        return sprintf(
            "%s%s/%s_%s.%s",
            $config->paths->mediaPublic,
            $this->date_path,
            $this->filename,
            $size,
            $this->ext );
    }

    /**
     * Get the file path for the media.
     *
     * @param integer $size (optional)
     * @return string
     */
    function getFilePath( $size = NULL )
    {
        if ( ! valid( $this->filename, STRING ) )
        {
            return '';
        }

        $config = $this->getService( 'config' );
        $sizeString = ( $size )
            ? "_$size"
            : "";

        return sprintf(
            "%s/%s/%s%s.%s",
            $config->paths->media,
            $this->date_path,
            $this->filename,
            $sizeString,
            $this->ext );
    }

    /**
     * Delete a media item by post ID and type
     *
     * @param integer $postId
     * @return boolean
     */
    static function deleteByPost( $postId, $type )
    {
        $phql =
            "update \Db\Sql\Medias set is_deleted = 1 ".
            "where post_id = :postId: and type = :type:";
        $query = new Query( $phql, self::getStaticDI() );

        return $query->execute([ 'postId' => $postId ]);
    }

    static function deleteById( $mediaId )
    {
        $phql = "update \Db\Sql\Medias set is_deleted = 1 where id = :mediaId:";
        $query = new Query( $phql, self::getStaticDI() );

        return $query->execute([ 'mediaId' => $mediaId ]);
    }
}