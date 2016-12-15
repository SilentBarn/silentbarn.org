<?php

namespace Db\Sql;

use Db\Behaviors\Timestamp as Timestampable;

class Tags extends \Base\Model
{
    public $id;
    public $name;
    public $slug;
    public $created_at;

    function initialize()
    {
        $this->setSource( 'tags' );
        $this->addBehavior( new Timestampable() );
    }

    /**
     * Returns all tags with the given slugs
     *
     * @param array $slugs
     * @return array of \Db\Sql\Tags
     */
    static function getBySlugs( $slugs )
    {
        return \Db\Sql\Tags::query()
            ->inWhere( 'slug', $slugs )
            ->execute();
    }

    /**
     * Creates a slug based on the name
     */
    function generateSlug()
    {
        return self::slugify( $this->name );
    }

    /**
     * Creates a slug based on the name
     */
    static function slugify( $name )
    {
        return trim(
            strtolower(
                str_replace( ' ', '-', $name )
            ));
    }
}
