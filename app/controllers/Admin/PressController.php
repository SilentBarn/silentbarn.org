<?php

namespace Controllers\Admin;

class PressController extends \Base\Controller
{
    public function beforeExecuteRoute()
    {
        $this->checkLoggedIn = TRUE;
        $this->view->setMainView( 'admin' );

        // Check if they have access
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
        $this->view->pick( 'admin/press/index' );
        $this->view->pageContent = \Db\Sql\Pages::findFirstByName( 'press' );
        $this->view->backPage = 'admin/articles';
        $this->view->buttons = [ 'savePress' ];
    }

    /**
     * Save the press content
     */
    public function saveAction()
    {
        // Edit the page content
        $data = $this->request->getPost();
        $pressAction = new \Actions\Pages\Press();
        $page = $pressAction->edit( $data );

        // Redirect
        $this->redirect = "admin/press";
    }
}