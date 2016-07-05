<?php

namespace Controllers\Admin;

class PagesController extends \Base\Controller
{
    public function beforeExecuteRoute()
    {
        $this->checkLoggedIn = TRUE;
        $this->view->setMainView( 'admin' );

        // Check if they have access
        if ( ! $this->auth->user[ 'access_pages' ] )
        {
            return $this->quit( "You don't have access to pages!", INFO, 'admin/articles' );
        }

        return parent::beforeExecuteRoute();
    }

    /**
     * Shows a list of pages to edit
     */
    public function indexAction()
    {
        $this->view->pick( 'admin/pages/index' );
        // @TODO get list of authorized pages for the user
        $this->data->pages = \Db\Sql\Pages::getEditablePages();
        $this->view->backPage = 'admin/articles';
        $this->view->buttons = [];
    }

    /**
     * Edit a page's HTML content
     */
    public function editAction( $name = "" )
    {
        if ( ! valid( $name, STRING ) )
        {
            return $this->quit( "No page specified", INFO, 'admin/pages' );
        }

        $page = \Db\Sql\Pages::findFirstByName( $name );

        if ( ! $page )
        {
            return $this->quit( "That page doesn't exist!", INFO, 'admin/pages' );
        }

        $this->view->pick( 'admin/pages/edit' );
        $this->view->page = $page;
        $this->view->backPage = 'admin/pages';
        $this->view->subPage = 'Edit '. $page->label . 'Page';
        $this->view->buttons = [ 'savePage' ];
    }

    /**
     * Save a page
     */
    public function saveAction()
    {
        // Edit the page
        $data = $this->request->getPost();
        $pageName = $this->request->getPost( 'name' );
        $pageAction = new \Actions\Pages\Page();
        $page = $pageAction->edit( $data );

        if ( ! $page )
        {
            return ( valid( $pageName, STRING ) )
                ? $this->quit( "", INFO, "admin/pages/edit/{$pageName}" )
                : $this->quit( "", INFO, 'admin/pages' );
        }

        $this->redirect = "admin/pages/edit/{$page->name}";
    }
}