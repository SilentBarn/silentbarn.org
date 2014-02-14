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
    }

    /**
     * Kill the session and login cookie
     */
    public function editAction( $id = "" )
    {
    }
}
