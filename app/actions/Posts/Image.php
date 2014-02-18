<?php

namespace Actions\Posts;

use \Suin\ImageResizer\ImageResizer,
    \Db\Sql\Images as Images;

class Image extends \Base\Action
{
    /**
     * Check the files array for any errors
     */
    public function checkFilesArrayErrors( $key = 'image' )
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
                    "Something went wrong uploading that image :(",
                    ERROR );
        }
    }

    /**
     * Marks all of a given post's photos as deleted. This
     * is usually called before the upload.
     *
     * @return boolean
     */
    public function deleteByPost( $postId )
    {
        return Images::deleteByPost( $postId );
    }

    /**
     * Saves an array of \Phalcon\Http\Request\File objects
     * to the media directory. This also saves a few different
     * sizes of the image.
     *
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
            $util->addMessage( "No photos were uploaded.", INFO );
            return FALSE;
        }

        foreach ( $files as $file )
        {
            // check the width is > 960
            //
            $tempName = $file->getTempName();
            $ext = pathinfo( $file->getName(), PATHINFO_EXTENSION );
            list( $width, $height, $type, $attr ) = getimagesize( $tempName );

            if ( $width < 960 || $height < 420 )
            {
                $util->addMessage(
                    "Please upload images at least 960px wide and 420px tall.",
                    INFO );
                return FALSE;
            }

            // generate the file hash and path
            //
            $yearPath = date( DATE_YEAR_MONTH_SLUG );
            $authAction = new \Actions\Users\Auth();
            $fileToken = $authAction->generateRandomToken();

            // save the temporary image to the media directory
            //
            $fullPath = $config->paths->media .'/'. $yearPath;
            $fileName = $fileToken .'.'. $ext;
            $fileName960 = $fileToken .'_960.'. $ext;
            $fileName310 = $fileToken .'_310.'. $ext;
            @mkdir( $fullPath, 0755, TRUE );
            $file->moveTo( $fullPath .'/'. $fileName );

            // copy a 960px and a 310px wide version
            //
            @copy( $fullPath .'/'. $fileName, $fullPath .'/'. $fileName960 );
            @copy( $fullPath .'/'. $fileName, $fullPath .'/'. $fileName310 );

            $resizer960 = new ImageResizer( $fullPath .'/'. $fileName960 );
            $resizer310 = new ImageResizer( $fullPath .'/'. $fileName310 );

            if ( ! $resizer960->maxWidth( 960 )->resize()
                || ! $resizer310->maxWidth( 310 )->resize() )
            {
                @unlink( $fullPath .'/'. $fileName );
                @unlink( $fullPath .'/'. $fileName960 );
                @unlink( $fullPath .'/'. $fileName310 );
                $util->addMessage( "There was a problem resizing your photo.", ERROR );
                return FALSE;
            }

            // save the record out to the database
            //
            $image = new Images();
            $image->initialize();
            $image->user_id = $this->getService( 'auth' )->getUserId();
            $image->post_id = $postId;
            $image->filename = $fileToken;
            $image->filename_orig = $file->getName();
            $image->ext = $ext;
            $image->date_path = $yearPath;
            $image->size = $file->getSize();
            $image->mime_type = $file->getRealType();
            $image->is_deleted = 0;

            if ( ! $image->save() )
            {
                $util->addMessage( 'There was a problem saving the image.', ERROR );
                return FALSE;
            }
        }

        return TRUE;
    }
}
