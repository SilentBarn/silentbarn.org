<?php

namespace Db\Sql;

class Artists extends \Base\Model
{
    public $id;
    public $name;
    public $slug;
    public $created_at;

    function initialize()
    {
        $this->setSource( 'artists' );
        $this->addBehavior( 'timestamp' );
    }

    /**
     * Returns all artists with the given slugs
     *
     * @param array $slugs
     * @return array of \Db\Sql\Artists
     */
    static function getBySlugs( $slugs )
    {
        return \Db\Sql\Artists::query()
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
