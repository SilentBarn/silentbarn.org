<?php

namespace Db\Sql;

use Db\Behaviors\Timestamp as Timestampable;

class Users extends \Base\Model
{
    public $id;
    public $email;
    public $password;
    public $name;
    public $access_users;
    public $access_members;
    public $access_press;
    public $access_spaces;
    public $access_media;
    public $access_pages;
    public $access_homepage;
    public $access_publish;
    public $is_deleted;
    public $created_at;

    // lazy loaded
    private $settings;

    const CATEGORY_ACCESS = 'category_access';

    function initialize()
    {
        $this->settings = array();
        $this->setSource( 'users' );
        $this->addBehavior( new Timestampable() );
    }

    /**
     * Load a user by login token, stored as a setting
     */
    static function getByToken( $token )
    {
        $config = self::getStaticService( 'config' );
        $setting = \Db\Sql\Settings::getByKeyValue(
            $config->settings->cookieToken,
            $token->getValue(),
            [ 'first' => TRUE ]);

        if ( ! $setting || ! valid( $setting->object_id ) )
        {
            return FALSE;
        }

        return \Db\Sql\Users::findFirst( $setting->object_id );
    }

    /**
     * Looks up the setting for which article categories the user
     * has access to, and then checks against the category ID
     * coming in.
     *
     * @param integer|array $category_id
     * @return boolean
     */
    public function canAccessCategory( $category_id )
    {
        $category_ids = ( is_numeric( $category_id ) )
            ? [ $category_id ]
            : $category_id;

        if ( is_null( get( $this->settings, self::CATEGORY_ACCESS, NULL ) ) )
        {
            $this->getCategoryAccess();
        }

        foreach ( $category_ids as $id )
        {
            $access = get(
                $this->settings[ self::CATEGORY_ACCESS ],
                $id );

            if ( ! $access )
            {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * Returns the setting for category access
     *
     * @return \Db\Sql\Settings object
     */
    public function getCategoryAccess( $fullObject = FALSE )
    {
        if ( ! is_null( get( $this->settings, self::CATEGORY_ACCESS, NULL ) )
            && ! $fullObject )
        {
            return $this->settings[ self::CATEGORY_ACCESS ];
        }

        $setting = \Db\Sql\Settings::get(
            $this->id,
            USER,
            self::CATEGORY_ACCESS,
            [ 'first' => TRUE ] );
        $this->settings[ self::CATEGORY_ACCESS ] = ( $setting )
            ? unserialize( $setting->value )
            : [];

        if ( ! $setting )
        {
            $setting = new \Db\Sql\Settings();
            $setting->initialize();
            $setting->key = self::CATEGORY_ACCESS;
        }

        return ( $fullObject )
            ? $setting
            : $this->settings[ self::CATEGORY_ACCESS ];
    }

    /**
     * Get the category slugs for the categories that this
     * user can access. This can return slugs or names.
     */
    public function getCategories( $key = "slug" )
    {
        $access = $this->getCategoryAccess();
        $categories = \Db\Sql\Categories::find();
        $return = [];

        foreach ( $categories as $category )
        {
            if ( isset( $access[ $category->id ] )
                && int_eq( $access[ $category->id ], 1 ) )
            {
                $return[] = $category->$key;
            }
        }

        return $return;
    }
}