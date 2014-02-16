<?php

namespace Db\Sql;

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
}
