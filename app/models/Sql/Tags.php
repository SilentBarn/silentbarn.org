<?php

namespace Db\Sql;

class Tags extends \Base\Model
{
    public $id;
    public $name;
    public $slug;
    public $created_at;

    function initialize()
    {
        $this->setSource( 'tags' );
    }
}
