<?php

namespace Controllers\Admin;

class PressController extends \Base\Controller
{
    public function beforeExecuteRoute()
    {
        $this->checkLoggedIn = TRUE;
        $this->view->setMainView( 'admin' );

        // check if they have access
        //
        if ( ! $this->auth->user[ 'access_press' ] )
        {
            return $this->quit( "You don't have access to press!", INFO, 'admin/articles' );
        }

        return parent::beforeExecuteRoute();
    }

    /**
     * Shows the Press settings/content fields
     */
    public function indexAction()
    {
        // get all of the posts
        //
        $this->view->pick( 'admin/press/index' );
        $this->view->backPage = '';
        $this->view->buttons = [ 'savePress' ];
    }

    /**
     * Save the press content
     */
    public function saveAction()
    {
        // edit the member
        //
        $data = $this->request->getPost();
        $memberAction = new \Actions\Users\Member();
        $member = $memberAction->edit( $data );
        $memberId = $this->request->getPost( 'id' );

        if ( ! $member )
        {
            return ( valid( $memberId ) )
                ? $this->quit( "", INFO, "admin/members/edit/{$memberId}" )
                : $this->quit( "", INFO, 'admin/members' );
        }

        // check for $_FILES errors
        //
        $imageAction = new \Actions\Posts\Image();
        $imageAction->checkFilesArrayErrors();

        if ( $this->request->hasFiles() == TRUE )
        {
            $imageAction->deleteByMember( $member );
            $imageAction->saveToMember( $member, $this->request->getUploadedFiles() );
        }

        // redirect
        //
        $this->redirect = "admin/members/edit/{$member->id}";
    }
}