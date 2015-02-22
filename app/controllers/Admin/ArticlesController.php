<?php

namespace Controllers\Admin;

use \Kilte\Pagination\Pagination as Pagination;

class ArticlesController extends \Base\Controller
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
        // get the curent page
        $currentPage = $this->request->getQuery( 'page' );
        $currentPage = ( valid( $currentPage ) )
            ? abs( $currentPage )
            : 1;
        $limit = 25;
        $offset = abs( ( $currentPage - 1 ) * $limit );

        // search on the categories the user can access
        $categories = $this->auth->getUserObj()->getCategories();

        // get all of the posts
        $this->view->pick( 'admin/articles/index' );
        $this->view->posts = \Db\Sql\Posts::search([
            'isDeleted' => 0,
            'categories' => $categories,
            'limit' => $limit,
            'offset' => $offset,
            'orderBy' => 'id desc',
            'userIds' => [ $this->auth->userId ]
        ]);
        $this->view->backPage = '';
        $this->view->buttons = [ 'newArticle' ];

        // set up the pagination
        $totalPosts = \Db\Sql\Posts::search([
            'isDeleted' => 0,
            'categories' => $categories,
            'userIds' => [ $this->auth->userId ],
            'count' => TRUE
        ]);

        $pagination = new Pagination(
            $totalPosts,
            $currentPage,
            $limit,
            $neighbors = 4 );
        $this->view->pages = $pagination->build();
    }

    /**
     * Create a new post and redirect to the edit page.
     */
    public function newAction()
    {
        // create the post
        $action = new \Actions\Posts\Post();
        $postId = $action->create();

        // redirect
        $this->redirect = "admin/articles/edit/$postId";
    }

    /**
     * Edit a post
     */
    public function editAction( $postId = "" )
    {
        if ( ! valid( $postId, INT ) )
        {
            return $this->quit( "No post ID specified", INFO, 'admin/articles' );
        }

        $post = \Db\Sql\Posts::findFirst( $postId );

        if ( ! $post )
        {
            return $this->quit( "That post doesn't exist!", INFO, 'admin/articles' );
        }

        // check if the user can access this post's category
        if ( ! $this->auth->getUserObj()->canAccessCategory( $post->getCategoryIds() ) )
        {
            return $this->quit( 
                "You're not allowed to edit that article.",
                INFO,
                "admin/articles" );
        }

        $this->view->pick( 'admin/articles/edit' );
        $this->view->post = $post;
        $this->view->postCategories = map( $post->getCategories()->toArray(), 'slug' );
        $this->view->categories = \Db\Sql\Categories::find();
        $this->view->userCats = $this->auth->getUserObj()->getCategoryAccess();
        $this->view->tags = \Db\Sql\Tags::find();
        $this->view->artists = \Db\Sql\Artists::find();
        $this->view->backPage = 'admin/articles';
        $this->view->subPage = 'Edit Article';
        $this->view->buttons = [ 'saveArticle' ];
    }

    /**
     * Save a post
     */
    public function saveAction()
    {
        // edit the post
        $data = $this->request->getPost();
        $postAction = new \Actions\Posts\Post();
        $post = $postAction->edit( $data );

        if ( ! $post )
        {
            return $this->quit( "", INFO, 'admin/articles' );
        }

        // save any categories
        $categoryAction = new \Actions\Posts\Category();
        $categoryAction->saveToPost(
            $post,
            $this->request->getPost( 'categories' ));

        // save any tags
        $tagAction = new \Actions\Posts\Tag();
        $tagAction->saveToPost(
            $post,
            $this->request->getPost( 'tags' ));

        // save any artists
        $artistAction = new \Actions\Posts\Artist();
        $artistAction->saveToPost(
            $post,
            $this->request->getPost( 'artists' ));

        // check for image file errors
        $imageAction = new \Actions\Posts\Image();
        $imageSuccess = TRUE;

        if ( $imageAction->hasFiles( 'image' ) )
        {
            // save any images and do the resizing
            if ( $imageAction->checkFilesArrayErrors( 'image' )
                && $this->request->hasFiles() )
            {
                $imageAction->deleteByPost( $post->id );
                $imageSuccess = $imageAction->saveToPost(
                    $post->id,
                    $this->request->getUploadedFiles() );
            }
        }
        // check if a URL came in
        elseif ( valid( $this->request->getPost( 'image_url' ), STRING ) )
        {
            $imageAction->deleteByPost( $post->id );
            $imageSuccess = $imageAction->saveUrlToPost(
                $post->id,
                $this->request->getPost( 'image_url' ) );
        }
        // if resize coordinates came in, resize the existing image
        elseif ( valid( $this->request->getPost( 'crop_x1' ), INT ) )
        {
            $imageAction->crop( $post->getImage(), $data );
        }

        if ( ! $imageSuccess )
        {
            $imageAction->undeleteByPost( $post->id );
        }

        // check for audio file errors
        $audioAction = new \Actions\Posts\Audio();

        if ( $audioAction->hasFiles( 'audio' )
            && $audioAction->checkFilesArrayErrors( 'audio' )
            && $this->request->hasFiles() )
        {
            $audioAction->saveToPost(
                $post->id,
                $this->request->getUploadedFiles() );
        }

        // redirect
        $this->redirect = "admin/articles/edit/{$post->id}";
    }

    /**
     * Delete a post
     */
    public function deleteAction( $id = "" )
    {
        $postAction = new \Actions\Posts\Post();
        $post = $postAction->delete( $id );

        $this->redirect = 'admin/articles';
    }

    /**
     * Delete a media file
     */
    public function deleteMediaAction( $postId = "", $id = "" )
    {
        $mediaAction = new \Actions\Posts\Media();
        $mediaAction->delete( $id );

        $this->redirect = "admin/articles/edit/{$postId}";
    }
}