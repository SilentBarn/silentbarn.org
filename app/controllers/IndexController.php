<?php

namespace Controllers;

class IndexController extends \Base\Controller
{
    public function beforeExecuteRoute()
    {
        $this->checkLoggedIn = FALSE;

        return parent::beforeExecuteRoute();
    }

    public function indexAction()
    {
        $this->view->pick( 'home/index' );
        $this->view->boxPosts = \Db\Sql\Posts::getByLocation( 'boxes', 5 );
        $this->view->heroPosts = \Db\Sql\Posts::getByLocation( 'hero', 10 );
    }
}