<?php

namespace Db\Sql;

use Michelf\Markdown;

class Pages extends \Base\Model
{
    public $id;
    public $name;
    public $content;
    public $owner_id;
    public $created_at;
    public $modified_at;

    private $unserializedContent;

    function initialize()
    {
        $this->setSource( 'pages' );
        $this->addBehavior( 'timestamp' );
    }

    function getContentVar( $key )
    {
        if ( is_null( $this->unserializedContent ) )
        {
            $this->unserializedContent = unserialize( $this->content );
        }

        return get( $this->unserializedContent, $key, '' );
    }

    function getHtmlVar( $key )
    {
        // Get the content in html
        $content = Markdown::defaultTransform(
            $this->getContentVar( $key ) );

        // Process any icons. These take the form [#icon:name] and
        // should be replaced with font icon tags.
        return preg_replace(
            "/\[\#icon:(.*?)\]/",
            "<i class=\"fa fa-$1\"></i>",
            $content );
    }
}
