<?php

namespace Controllers;

class AdminController extends \Base\Controller
{
    public function beforeExecuteRoute()
    {
        $this->checkLoggedIn = TRUE;
        $this->view->setMainView( 'admin' );

        return parent::beforeExecuteRoute();
    }

    /**
     * Shows a list of articles and allows the user to manage
     * them and create new ones.
     */
    public function indexAction()
    {
        // get all of the posts
        //
        $posts = \Db\Sql\Posts::getActive();
        print_r($posts);exit;
    }

    /**
     * Create a new post and redirect to the edit page.
     */
    public function newAction()
    {
        // create the post
        //
        $action = new \Actions\Posts\Post();
        $id = $action->create();

        // redirect
        //
        $this->redirect = "admin/edit/$id";
    }

    /**
     * Edit a post
     */
    public function editAction( $id = "" )
    {
        if ( ! valid( $id, INT ) )
        {
            $this->addMessage( "No post ID specified", INFO );
            $this->redirect = 'admin';
            return FALSE;
        }
    }
}
