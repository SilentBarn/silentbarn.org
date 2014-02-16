<?php

namespace Actions\Posts;

use \Suin\ImageResizer\ImageResizer;

class Image extends \Base\Action
{
    /**
     * Marks all of a given post's photos as deleted. This
     * is usually called before the upload.
     *
     * @return boolean
     */
    public function deleteByPost( $postId )
    {
        return \Db\Sql\Images::deleteByPost( $postId );
    }

    /**
     * Saves an array of \Phalcon\Http\Request\File objects
     * to the media directory. This also saves a few different
     * sizes of the image.
     *
     * @param \Phalcon\Http\Request\File array $files
     * @return boolean
     */
    public function upload( $postId, $files )
    {
        $util = $this->getService( 'util' );
        $config = $this->getService( 'config' );

        if ( ! is_array( $files )
            || ! count( $files ) )
        {
            $util->addMessage( "No photos were uploaded.", INFO );
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
            $yearPath = date( 'Y/m' );
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

            $resizer = new ImageResizer( $fullPath .'/'. $fileName960 );
            $resizer = new ImageResizer( $fullPath .'/'. $fileName310 );

            if ( ! $resizer->maxWidth( 960 )->resize()
                || ! $resizer->maxWidth( 310 )->resize() )
            {
                @unlink( $fullPath .'/'. $fileName );
                @unlink( $fullPath .'/'. $fileName960 );
                @unlink( $fullPath .'/'. $fileName310 );
                $util->addMessage( "There was a problem resizing your photo.", ERROR );
            }

            // save the record out to the database
            //
            $image = new \Db\Sql\Images();
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
