<?php

namespace Actions\Posts;

use \Db\Sql\Medias as Medias;

class Media extends \Base\Action
{
    /**
     * Check if audio files exist in $_FILES
     */
    public function hasFiles( $key )
    {
        return isset( $_FILES[ $key ] )
            && $_FILES[ $key ][ 'size' ] > 0;
    }

    /**
     * Check the files array for any errors
     */
    public function checkFilesArrayErrors( $key )
    {
        $util = $this->getService( 'util' );

        switch ( $_FILES[ $key ][ 'error' ] )
        {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                break;
            case UPLOAD_ERR_INI_SIZE:
                $iniSize = ini_get( 'upload_max_filesize' );
                $util->addMessage(
                    "Sorry, the filesize was larger than $iniSize.",
                    ERROR );
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $util->addMessage(
                    "Sorry, the filesize was larger than what your browser allows.",
                    ERROR );
                break;
            default:
                $util->addMessage(
                    "Something went wrong uploading that file :(",
                    ERROR );
        }

        return TRUE;
    }

    /**
     * Deletes a media item, i.e. marks it is_deleted
     */
    public function delete( $id )
    {
        $util = $this->getService( 'util' );
        $media = Medias::findFirst( $id );

        if ( ! $media )
        {
            $util->addMessage( "That media file couldn't be found.", INFO );
            return FALSE;
        }

        $media->is_deleted = 1;

        return $this->save( $media );
    }

    /**
     * Saves a media file, error handles
     *
     * @param \Db\Sql\Medias $media
     * @return boolean
     */
    private function save( &$media )
    {
        if ( $media->save() == FALSE )
        {
            $util = $this->getService( 'util' );
            $util->addMessage(
                "There was a problem saving your media file.",
                INFO );

            foreach ( $media->getMessages() as $message )
            {
                $util->addMessage( $message, ERROR );
            }

            return FALSE;
        }

        return TRUE;
    }
}