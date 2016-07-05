<?php

namespace Controllers\Admin;

class HomepageController extends \Base\Controller
{
    public function beforeExecuteRoute()
    {
        $this->checkLoggedIn = TRUE;
        $this->view->setMainView( 'admin' );

        // Check if they have access
        if ( ! $this->auth->user[ 'access_homepage' ] ) {
            return $this->quit(
                "You don't have access to edit the homepage!",
                INFO,
                'admin/articles' );
        }

        return parent::beforeExecuteRoute();
    }

    /**
     * Shows the homepage management page.
     */
    public function indexAction()
    {
        // Load all the posts and bucket them by category
        $this->view->boxes = [
            "News" => NEWS,
            "Events" => EVENTS,
            "Galleries" => EXHIBITIONS,
            "Community" => COMMUNITY,
            "Studios" => ARTICLES
        ];
        $this->view->days = [
            'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'
        ];
        $this->view->cafeDays = [
            0 => '9 - 5',
            1 => 'Closed',
            2 => 'Closed',
            3 => '9 - 5',
            4 => '9 - 5',
            5 => '9:30 - 2',
            6 => '11 - 5'
        ];
        $this->view->posts =  \Db\Sql\Posts::getAllWithCategory();
        $this->view->pick( 'admin/homepage/edit' );
        $this->view->backPage = 'admin/articles';
        $this->view->buttons = [ 'saveHomepage' ];
    }

    /**
     * Saves the homepage settings.
     */
    public function saveAction()
    {
        exit( 'todo' );
        // Edit the page
        $data = $this->request->getPost();
        $pageName = $this->request->getPost( 'name' );
        $pageAction = new \Actions\Pages\Page();
        $page = $pageAction->edit( $data );

        if ( ! $page ) {
            return ( valid( $pageName, STRING ) )
                ? $this->quit( "", INFO, "admin/pages/edit/{$pageName}" )
                : $this->quit( "", INFO, 'admin/pages' );
        }

        $this->redirect = "admin/pages/edit/{$page->name}";
    }
}