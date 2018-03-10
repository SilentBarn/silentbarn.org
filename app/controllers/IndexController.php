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
        $hideOverlay = $this->request->getQuery( 'hide' ) == 1;

        $this->view->pick( 'home/index' );
        // In demo mode, when ready for prod remove "!"
        $this->view->hideOverlay = ! $hideOverlay;
        $this->view->boxPosts = \Db\Sql\Posts::getByLocation( 'boxes', 5 );
        $this->view->heroPosts = \Db\Sql\Posts::getByLocation( 'hero', 10 );
    }
}