<?php

namespace Db\Sql;

use Michelf\Markdown
  , Db\Behaviors\Timestamp as Timestampable;

class Pages extends \Base\Model
{
    public $id;
    public $name;
    public $content;
    public $owner_id;
    public $created_at;
    public $modified_at;

    private $owner;
    private $unserializedContent;

    function initialize()
    {
        $this->setSource( 'pages' );
        $this->addBehavior( new Timestampable() );
    }

    static function getEditablePages()
    {
        return \Db\Sql\Pages::query()
            ->inWhere( 'name', [ 'donate' ] )
            ->orderBy( 'name asc' )
            ->execute();
    }

    function getOwner()
    {
        if ( ! is_null( $this->owner ) )
        {
            return $this->owner;
        }

        if ( ! valid ( $this->owner_id ) )
        {
            return new \Db\Sql\Users();
        }

        $this->owner = \Db\Sql\Users::findFirst([
            'id = :id:',
            'bind' => [
                'id' => $this->owner_id ]
            ]);

        if ( ! $this->owner )
        {
            $this->owner = new \Db\Sql\Users();
        }

        return $this->owner;
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
