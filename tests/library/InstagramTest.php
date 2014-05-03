<?php

namespace Library;

use Httpful\Request as Request,
    Lib\Instagram as Instagram;

class InstagramTest extends \UnitTestCase
{
    /**
     * @group library
     * @group instagram
     */
    public function testMedia()
    {
        $media = Instagram::getMedia();
        $this->assertCount( 5, $media );
    }
}