<?php

namespace Controllers\Admin;

use Db\Sql\Pages
  , Db\Sql\Posts;

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
        // Get all posts into category buckets
        $posts = Posts::getAllWithCategory();
        $sliderPosts = array_merge(
            get( $posts, NEWS, [] ),
            get( $posts, EVENTS, [] ),
            get( $posts, COMMUNITY, [] ));

        // Load all the posts and bucket them by category
        $this->view->boxCategories = [
            "News" => NEWS,
            "Events" => EVENTS,
            "Galleries" => EXHIBITIONS,
            "Community" => COMMUNITY,
            "Studios" => ARTICLES
        ];
        $this->view->days = [
            'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'
        ];

        // Sort the slider posts by ID
        usort( $sliderPosts, function ( $a, $b ) {
            return $a[ 'id' ] < $b[ 'id' ];
        });

        $this->view->posts = $posts;
        $this->view->postsSlider = $sliderPosts;
        $this->view->backPage = 'admin/articles';
        $this->view->buttons = [ 'saveHomepage' ];
        $this->view->pick( 'admin/homepage/edit' );
        $page = Pages::findFirstByName( Pages::HOMEPAGE );
        $this->view->boxes = $page->getContentVar( 'boxes', [] );
        $this->view->slider = $page->getContentVar( 'slider', [] );
        $this->view->cafeDays = $page->getContentVar(
            'cafe_days',
            array_pad( [], 7, "" ));
    }

    /**
     * Saves the homepage settings.
     */
    public function saveAction()
    {
        // Edit the page
        $data = $this->request->getPost();
        $pageAction = new \Actions\Pages\Page();
        $page = $pageAction->editHomepage( $data );

        if ( ! $page ) {
            return $this->quit( "", INFO, "admin/homepage" );
        }

        $this->redirect = "admin/homepage";
    }
}