<?php

namespace Controllers;

class IndexController extends \Base\Controller
{
    public function beforeExecuteRoute()
    {
        $this->checkLoggedIn = FALSE;

        return parent::beforeExecuteRoute();
    }

    public function indexAction( $hideOverlay = "" )
    {
        $this->view->pick( 'home/index' );
        // In demo mode, when ready for prod remove "!"
        $this->view->hideOverlay = ! strlen( trim( $hideOverlay ) );
        $this->view->boxPosts = \Db\Sql\Posts::getByLocation( 'boxes', 5 );
        $this->view->heroPosts = \Db\Sql\Posts::getByLocation( 'hero', 10 );
    }
}