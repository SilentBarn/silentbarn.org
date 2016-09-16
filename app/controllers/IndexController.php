<?php

namespace Controllers;

use Db\Sql\Pages
  , Db\Sql\Posts;
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

        // Load the homepage content
        $page = Pages::findFirstByName( Pages::HOMEPAGE );
        $this->view->boxes = $page->getContentVar( 'boxes', [] );
        $this->view->slider = $page->getContentVar( 'slider', [] );
        $this->view->cafeDays = $page->getContentVar( 'cafe_days', [] );
        $this->view->days = [
            'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'
        ];

        if ( ! array_filter( $this->view->cafeDays, 'strlen' ) ) {
            $this->view->cafeDays = NULL;
        }

        // Get all of the posts that are referenced in the homepage
        $posts = [];
        $ids = array_unique(
            array_merge(
                array_values( $this->view->boxes ),
                array_values( $this->view->slider )
            ));
        $allPosts = Posts::getByIds( $ids );

        foreach ( $allPosts as $post ) {
            $posts[ $post->id ] = $post;
        }

        $this->view->posts = $posts;
    }
}