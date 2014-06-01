<?php

namespace Controllers\Admin;

class SpacesController extends \Base\Controller
{
    public function beforeExecuteRoute()
    {
        $this->checkLoggedIn = TRUE;
        $this->view->setMainView( 'admin' );

        // check if they have access
        //
        if ( ! $this->auth->user[ 'access_spaces' ] )
        {
            return $this->quit( "You don't have access to spaces!", INFO, 'admin/articles' );
        }

        return parent::beforeExecuteRoute();
    }

    /**
     * Shows a list of spaces.
     */
    public function indexAction()
    {
        // get all of the posts
        //
        $this->view->pick( 'admin/spaces/index' );
        $this->data->spaces = \Db\Sql\Spaces::find([
            'is_deleted = 0',
            "order" => "name"
        ]);
        $this->view->backPage = 'admin/articles';
        $this->view->buttons = [ 'newSpace' ];
    }

    /**
     * Create a new space and redirect to the edit page.
     */
    public function newAction()
    {
        // create the space
        //
        $action = new \Actions\Spaces\Space();
        $spaceId = $action->create();

        // redirect
        //
        $this->redirect = "admin/spaces/edit/$spaceId";
    }

    /**
     * Edit a space
     */
    public function editAction( $spaceId = "" )
    {
        if ( ! valid( $spaceId, INT ) )
        {
            return $this->quit( "No space ID specified", INFO, 'admin/spaces' );
        }

        $space = \Db\Sql\Spaces::findFirst( $spaceId );

        if ( ! $space )
        {
            return $this->quit( "That space doesn't exist!", INFO, 'admin/spaces' );
        }

        $this->view->pick( 'admin/spaces/edit' );
        $this->view->space = $space;
        $this->view->backPage = 'admin/spaces';
        $this->view->subPage = 'Edit Space';
        $this->view->buttons = [ 'saveSpace' ];
    }

    /**
     * Save a space
     */
    public function saveAction()
    {
        // edit the space
        $data = $this->request->getPost();
        $spaceAction = new \Actions\Spaces\Space();
        $space = $spaceAction->edit( $data );
        $spaceId = $this->request->getPost( 'id' );

        if ( ! $space )
        {
            return ( valid( $spaceId ) )
                ? $this->quit( "", INFO, "admin/spaces/edit/{$spaceId}" )
                : $this->quit( "", INFO, 'admin/spaces' );
        }

        // check for $_FILES errors
        $imageAction = new \Actions\Posts\Image();
        $imageAction->checkFilesArrayErrors();

        if ( $this->request->hasFiles() == TRUE )
        {
            $imageAction->deleteBySpace( $space );
            $imageAction->saveToSpace( $space, $this->request->getUploadedFiles() );
        }

        // redirect
        $this->redirect = "admin/spaces/edit/{$space->id}";
    }

    /**
     * Delete a space
     */
    public function deleteAction( $id = "" )
    {
        $spaceAction = new \Actions\Spaces\Space();
        $space = $spaceAction->delete( $id );

        $this->redirect = 'admin/spaces';
    }
}