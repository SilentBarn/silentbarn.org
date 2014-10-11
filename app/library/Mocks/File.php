<?php

namespace Lib\Mocks;

/**
 * Mock the Phalcon\Http\Request\File object for testing
 * and application use.
 */
class File implements \Phalcon\Http\Request\FileInterface
{
    private $name;
    private $tempName;
    private $size;
    private $mimeType;

    public function __construct( $path = "" ) {}

    /**
     * Takes a file and downloads it to /tmp/tempname
     *
     * @param string $url URL to file
     */
    public function download( $url )
    {
        // make sure to remove any GET params or other nonsense
        // from the end of the string.
        $pathUrl = $url;

        if ( strpos( $pathUrl, '?' ) !== FALSE )
        {
            $pieces = explode( '?', $pathUrl, 2 );
            $pathUrl = $pieces[ 0 ];
        }

        if ( strpos( $pathUrl, '#' ) !== FALSE )
        {
            $pieces = explode( '#', $pathUrl, 2 );
            $pathUrl = $pieces[ 0 ];
        }

        // get the path info; set the name. 
        $pathinfo = pathinfo( $pathUrl );
        $this->name = $pathinfo[ 'basename' ];
        $this->tempName = "/tmp/{$this->name}";

        // verify that this is an image; set the mime
        $valid = [ 'png', 'bmp', 'jpg', 'jpeg', 'gif' ];
        $ext = strtolower( $pathinfo[ 'extension' ] );

        if ( ! in_array( $ext, $valid ) )
        {
            exit('a');
            return FALSE;
        }

        $mimes = [
            'bmp' => 'image/bmp',
            'gif' => 'image/gif',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'png' => 'image/png' ];
        $this->mimeType = $mimes[ $ext ];

        // download the file, set the size
        $file = file_get_contents( $url );
        $this->size = mb_strlen( $file );

        if ( ! $this->size )
        {
            return FALSE;
        }

        // move to a new temporary location; set tempName
        return file_put_contents( $this->tempName, $file );
    }

    public function deleteTemp()
    {
        // delete the uploaded file if it's still there
        return unlink( $this->tempName );
    }

    /**
     * Moves file from tempName location to the specified path
     *
     * @param string $path Path to new location
     */
    public function moveTo( $destination )
    {
        // move the file from tempname to destination
        return rename( $this->tempName, $destination );
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getTempName()
    {
        return $this->tempName;
    }

    public function getType()
    {
        return $this->mimeType;
    }

    public function getRealType()
    {
        return $this->mimeType;
    }
}
