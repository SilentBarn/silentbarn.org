<?php

namespace Actions\Users;

use \Db\Sql\Members as Members;

class Member extends \Base\Action
{
    /**
     * Creates a new blank member
     *
     * @return integer Member ID
     */
    public function create()
    {
        $auth = $this->getService( 'auth' );
        $config = $this->getService( 'config' );
        $authAction = new \Actions\Users\Auth();
        $member = new Members();
        $member->initialize();
        $member->is_deleted = 0;
        $member->is_active = 1;
        // save a temporary email
        $member->email = $authAction->generateRandomToken();

        if ( ! $this->save( $member ) )
        {
            return FALSE;
        }

        // set a human readable email
        $member->email = "member_". $member->id ."@". $config->paths->hostname;

        if ( ! $this->save( $member ) )
        {
            return FALSE;
        }

        return $member->id;
    }

    /**
     * Saves a member
     *
     * @param array $data
     * @return boolean
     */
    public function edit( $data )
    {
        $util = $this->getService( 'util' );
        $filter = $this->getService( 'filter' );

        // check the member ID and verify that this member exists
        //
        if ( ! isset( $data[ 'id' ] )
            || ! valid( $data[ 'id' ], INT ) )
        {
            $util->addMessage( "You didn't specify a member ID.", INFO );
            return FALSE;
        }

        $member = Members::findFirst( $data[ 'id' ] );

        if ( ! $member )
        {
            $util->addMessage( "That member couldn't be found.", INFO );
            return FALSE;
        }

        // check for an email
        //
        if ( ! isset( $data[ 'email' ] )
            || ! valid( $data[ 'email' ], STRING ) )
        {
            $util->addMessage( "You didn't enter an email address.", INFO );
            return FALSE;
        }

        // check if new email already exists and isn't the same
        //
        $emailMember = Members::findFirstByEmail( $data[ 'email' ] );

        if ( $emailMember
            && ! str_eq( $emailMember->email, $member->email ) )
        {
            $util->addMessage( "That email address is already taken.", INFO );
            return FALSE;
        }

        $member->name = $filter->sanitize( get( $data, 'name' ), 'striptags' );
        $member->email = $filter->sanitize( get( $data, 'email' ), 'striptags' );
        $member->bio = $filter->sanitize( get( $data, 'bio' ), 'striptags' );
        $member->is_chef = ( get( $data, 'is_chef' ) ) ? 1 : 0;
        $member->is_resident = ( get( $data, 'is_resident' ) ) ? 1 : 0;
        $member->is_stewdio = ( get( $data, 'is_stewdio' ) ) ? 1 : 0;
        $member->is_active = ( get( $data, 'is_active' ) ) ? 1 : 0;

        if ( ! $this->save( $member ) )
        {
            return FALSE;
        }

        return $member;
    }

    /**
     * Deletes a member, i.e. marks it is_deleted
     */
    public function delete( $id )
    {
        $util = $this->getService( 'util' );
        $member = Members::findFirst( $id );

        if ( ! $member )
        {
            $util->addMessage( "That member couldn't be found.", INFO );
            return FALSE;
        }

        $member->is_deleted = 1;

        return $this->save( $member );
    }

    /**
     * Saves a member, error handles
     *
     * @param \Db\Sql\Members $member
     * @return
     */
    private function save( &$member )
    {
        if ( $member->save() == FALSE )
        {
            $util = $this->getService( 'util' );
            $util->addMessage(
                "There was a problem saving your member.",
                INFO );

            foreach ( $member->getMessages() as $message )
            {
                $util->addMessage( $message, ERROR );
            }

            return FALSE;
        }

        return TRUE;
    }
}