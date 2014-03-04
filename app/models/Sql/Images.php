<?php

namespace Db\Sql;

use Phalcon\Mvc\Model\Query;

class Images extends \Base\Model
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
        $this->setSource( 'images' );
        $this->addBehavior( 'timestamp' );
    }

    /**
     * Get the path for an image.
     *
     * @param integer $size 960 or 310
     * @return string
     */
    function getPath( $size = 960 )
    {
        if ( ! valid( $this->filename, STRING ) )
        {
            return '';
        }

        $config = $this->getService( 'config' );
        $url = $this->getService( 'url' );

        return sprintf(
            "%s%s/%s_%s.%s",
            $config->paths->mediaPublic,
            $this->date_path,
            $this->filename,
            $size,
            $this->ext );
    }

    static function deleteByPost( $postId )
    {
        $phql = "update \Db\Sql\Images set is_deleted = 1 where post_id = :postId:";
        $query = new Query( $phql, self::getStaticDI() );

        return $query->execute([ 'postId' => $postId ]);
    }
}
