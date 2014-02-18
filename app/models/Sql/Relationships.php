<?php

namespace Db\Sql;

use Phalcon\Mvc\Model\Query;

class Relationships extends \Base\Model
{
    public $object_id;
    public $object_type;
    public $property_id;
    public $property_type;
    public $key;
    public $created_at;

    function initialize()
    {
        $this->setSource( 'relationships' );
    }

    /**
     * Returns properties related to an object.
     *
     * @param Model $propModel Full namespace of objects to retrieve
     * @param string $propType Type of property obejcts to retrieve
     * @param integer $objectId Object these properties are tied to
     * @param string $objectType Type of that object
     * @return array
     */
    static function getProperties( $propModel, $propType, $objectId, $objectType, $sort = 'name desc' )
    {
        $phql = sprintf(
            "select p.* from %s as p ".
            "inner join \Db\Sql\Relationships as r ".
            "  on p.id = r.property_id and r.property_type = '%s' ".
            "where r.object_id = :objectId: ".
            "  and r.object_type = :objectType: ".
            "order by p.%s",
            $propModel,
            $propType,
            $sort );
        $query = new Query( $phql, self::getStaticDI() );

        return $query->execute([
            'objectId' => $objectId,
            'objectType' => $objectType ]);
    }

    /**
     * Get relationships for an object and property type
     *
     * @param integer $objectId
     * @param string $objectType
     * @param string $propertyType
     * @return boolean
     */
    static function getByObjectAndPropertyType( $objectId, $objectType, $propertyType )
    {
        return \Db\Sql\Relationships::query()
            ->where( 'object_id = :objectId:' )
            ->andWhere( 'object_type = :objectType:' )
            ->andWhere( 'property_type = :propertyType:' )
            ->bind([
                'objectId' => $objectId,
                'objectType' => $objectType,
                'propertyType' => $propertyType ])
            ->execute();
    }
}
