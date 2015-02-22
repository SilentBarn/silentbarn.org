<?php

namespace Actions\Posts;

use \Db\Sql\Medias as Medias,
    \Lib\Mocks\File as MockFile;

class Audio extends \Actions\Posts\Media
{
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

        if ( ! is_array( $files ) || ! count( $files ) )
        {
            $util->addMessage( "No audio files were uploaded.", INFO );
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
            $audio = new Medias();
            $audio->initialize();
            $audio->user_id = $this->getService( 'auth' )->getUserId();
            $audio->post_id = $postId;
            $audio->type = MEDIA_AUDIO;
            $audio->filename = $fileToken;
            $audio->filename_orig = $file->getName();
            $audio->ext = $ext;
            $audio->date_path = $yearPath;
            $audio->size = $file->getSize();
            $audio->mime_type = $file->getRealType();
            $audio->is_deleted = 0;

            if ( ! $audio->save() )
            {
                $util->addMessage( 'There was a problem saving the audio file.', ERROR );
                return FALSE;
            }
        }

        return TRUE;
    }
}