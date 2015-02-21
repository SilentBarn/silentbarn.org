<?php

namespace Actions\Posts;

use \Db\Sql\Medias as Medias,
    \Lib\Mocks\File as MockFile;

class Media extends \Base\Action
{
    /**
     * Check the files array for any errors
     */
    public function checkFilesArrayErrors( $key = 'audio' )
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
    }

    /**
     * Marks a specific media item as deleted.
     *
     * @return boolean
     */
    public function delete( $mediaId )
    {
        return Medias::deleteById( $mediaId );
    }

    /**
     * Saves an array of \Phalcon\Http\Request\File objects
     * to the media directory.
     *
     * @param integer $postId
     * @param \Phalcon\Http\Request\File array $files
     * @return boolean
     */
    public function saveToPost( $postId, $files )
    {
        $util = $this->getService( 'util' );
        $config = $this->getService( 'config' );

        if ( ! is_array( $files )
            || ! count( $files ) )
        {
            $util->addMessage( "No files were uploaded.", INFO );
            return FALSE;
        }

        foreach ( $files as $file )
        {
            // generate the file hash and path
            $ext = pathinfo( $file->getName(), PATHINFO_EXTENSION );
            $yearPath = date( DATE_YEAR_MONTH_SLUG );
            $authAction = new \Actions\Users\Auth();
            $fileToken = $authAction->generateRandomToken();

            // move the file to the new directory
            $fullPath = $config->paths->media .'/'. $yearPath;
            $fileName = $fileToken .'.'. $ext;
            @mkdir( $fullPath, 0755, TRUE );
            $file->moveTo( $fullPath .'/'. $fileName );

            // save the record out to the database
            $image = new Medias();
            $image->initialize();
            $image->user_id = $this->getService( 'auth' )->getUserId();
            $image->post_id = $postId;
            $image->type = MEDIA_AUDIO;
            $image->filename = $fileToken;
            $image->filename_orig = $file->getName();
            $image->ext = $ext;
            $image->date_path = $yearPath;
            $image->size = $file->getSize();
            $image->mime_type = $file->getRealType();
            $image->is_deleted = 0;

            if ( ! $image->save() )
            {
                $util->addMessage( 'There was a problem saving the audio file.', ERROR );
                return FALSE;
            }
        }

        return TRUE;
    }
}