<?php

namespace Actions\Posts;

use \Suin\ImageResizer\ImageResizer,
    \Db\Sql\Images as Images,
    \Lib\Mocks\File as MockFile;

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

            if ( $width < 310 || $height < 310 )
            {
                $util->addMessage(
                    "Please upload images at least 310px wide and 310px tall.",
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

    /**
     * Uploads a picture from a URL
     *
     * @param string $url
     */
    function saveUrlToPost( $postId, $url )
    {
        $file = new MockFile();
        $util = $this->getService( 'util' );

        if ( ! $file->download( $url ) )
        {
            $util->addMessage( 'There was a problem downloading that image.', ERROR );
            return FALSE;
        }

        $saved = $this->saveToPost( $postId, [ $file ] );
        $file->deleteTemp();

        return $saved;
    }

    /**
     * Deletes a photo for a particular member.
     *
     * @param \Db\Sql\Members $member
     * @return boolean
     */
    function deleteByMember( &$member )
    {
        if ( ! valid( $member->image_filename, STRING ) )
        {
            return TRUE;
        }

        @unlink( $member->getImagePath( FALSE ) );
        $util = $this->getService( 'util' );
        $member->image_filename = NULL;

        if ( ! $member->save() )
        {
            $util->addMessage( 'There was a problem saving the image.', ERROR );
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Saves an array of \Phalcon\Http\Request\File objects
     * to the media/members directory.
     *
     * @param \Db\Sql\Members $member
     * @param \Phalcon\Http\Request\File array $files
     * @return boolean
     */
    function saveToMember( $member, $files )
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
            // check the width is > 310
            //
            $tempName = $file->getTempName();
            $ext = strtolower( pathinfo( $file->getName(), PATHINFO_EXTENSION ) );
            list( $width, $height, $type, $attr ) = getimagesize( $tempName );

            if ( $width < 310 || $height < 310 )
            {
                $util->addMessage(
                    "Please upload square images at least 310px wide and 310px tall.",
                    INFO );
                return FALSE;
            }

            // generate the file hash and path
            //
            $authAction = new \Actions\Users\Auth();
            $fileToken = $authAction->generateRandomToken();

            // save the temporary image to the media directory
            //
            $fullPath = $config->paths->media .'/members';
            $fileName = $fileToken .'.'. $ext;
            @mkdir( $fullPath, 0755, TRUE );
            $file->moveTo( $fullPath .'/'. $fileName );

            // resize the image to 310
            //
            $resizer310 = new ImageResizer( $fullPath .'/'. $fileName );

            if ( ! $resizer310->maxWidth( 310 )->resize() )
            {
                @unlink( $fullPath .'/'. $fileName );
                $util->addMessage( "There was a problem resizing your photo.", ERROR );
                return FALSE;
            }

            // save the record out to the database
            //
            $member->image_filename = $fileName;

            if ( ! $member->save() )
            {
                $util->addMessage( 'There was a problem saving the image.', ERROR );
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * Deletes a photo for a particular space.
     *
     * @param \Db\Sql\Spaces $space
     * @return boolean
     */
    function deleteBySpace( &$space )
    {
        if ( ! valid( $space->image_filename, STRING ) )
        {
            return TRUE;
        }

        @unlink( $space->getImagePath( FALSE ) );
        $util = $this->getService( 'util' );
        $space->image_filename = NULL;

        if ( ! $space->save() )
        {
            $util->addMessage( 'There was a problem saving the image.', ERROR );
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Saves an array of \Phalcon\Http\Request\File objects
     * to the media/spaces directory.
     *
     * @param \Db\Sql\Spaces $space
     * @param \Phalcon\Http\Request\File array $files
     * @return boolean
     */
    function saveToSpace( $space, $files )
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
            // check the width is > 310
            $tempName = $file->getTempName();
            $ext = strtolower( pathinfo( $file->getName(), PATHINFO_EXTENSION ) );
            list( $width, $height, $type, $attr ) = getimagesize( $tempName );

            if ( $width < 310 || $height < 310 )
            {
                $util->addMessage(
                    "Please upload square images at least 310px wide and 310px tall.",
                    INFO );
                return FALSE;
            }

            // generate the file hash and path
            $authAction = new \Actions\Users\Auth();
            $fileToken = $authAction->generateRandomToken();

            // save the temporary image to the media directory
            //
            $fullPath = $config->paths->media .'/spaces';
            $fileName = $fileToken .'.'. $ext;
            @mkdir( $fullPath, 0755, TRUE );
            $file->moveTo( $fullPath .'/'. $fileName );

            // resize the image to 310
            //
            $resizer310 = new ImageResizer( $fullPath .'/'. $fileName );

            if ( ! $resizer310->maxWidth( 310 )->resize() )
            {
                @unlink( $fullPath .'/'. $fileName );
                $util->addMessage( "There was a problem resizing your photo.", ERROR );
                return FALSE;
            }

            // save the record out to the database
            //
            $space->image_filename = $fileName;

            if ( ! $space->save() )
            {
                $util->addMessage( 'There was a problem saving the image.', ERROR );
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * Crops an image based on the passed in coordinates
     *
     * @param \Db\Sql\Images $image
     * @param array $coords
     */
    function crop( $image, $coords )
    {
        if ( ! $image || ! valid( $image->id ) )
        {
            return FALSE;
        }

        // for messages
        $util = $this->getService( 'util' );

        // check if coords exist and x2/y2 are non-zero
        $x1 = get( $coords, 'crop_x1', 0 );
        $y1 = get( $coords, 'crop_y1', 0 );
        $x2 = get( $coords, 'crop_x2', 0 );
        $y2 = get( $coords, 'crop_y2', 0 );

        if ( ! valid( $x2 ) || ! valid( $y2 ) )
        {
            return FALSE;
        }

        // check if the width or height is < 310
        if ( $x2 - $x1 < 310 || $y2 - $y1 < 310 )
        {
            $util->addMessage( "Images must be a minimum of 310px by 310px!", ERROR );
            return FALSE;
        }

        // instantiate new cropping class
        $newImage = new \Zebra_Image();
        $newImage->source_path = $image->getFilePath( 960 );
        $newImage->target_path = $image->getFilePath( 960 );

        // crop the image        
        if ( ! $newImage->crop( $x1, $y1, $x2, $y2 ) )
        {
            $util->addMessage(
                'There was a problem cropping the image (code: '. $newImage->error .')',
                ERROR );
            return FALSE;
        }

        return TRUE;
    }
}
