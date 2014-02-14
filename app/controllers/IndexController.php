<?php

namespace Controllers;

class IndexController extends \Base\Controller
{
    public function beforeExecuteRoute()
    {
        $this->checkLoggedIn = FALSE;

        parent::beforeExecuteRoute();
    }

    public function indexAction()
    {
        $this->view->pick( 'home/index' );
    }
}