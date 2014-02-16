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
        // get all of the posts
        //
        $this->view->posts = \Db\Sql\Posts::getActive();
        $this->view->backPage = '';
        $this->view->buttons = [ 'new' ];
    }

    /**
     * Create a new post and redirect to the edit page.
     */
    public function newAction()
    {
        // create the post
        //
        $action = new \Actions\Posts\Post();
        $postId = $action->create();

        // redirect
        //
        $this->redirect = "admin/edit/$postId";
    }

    /**
     * Edit a post
     */
    public function editAction( $postId = "" )
    {
        if ( ! valid( $postId, INT ) )
        {
            $this->addMessage( "No post ID specified", INFO );
            $this->redirect = 'admin';
            return FALSE;
        }

        $post = \Db\Sql\Posts::findFirst( $postId );

        if ( ! $post )
        {
            $this->addMessage( "That post doesn't exist!", INFO );
            $this->redirect = 'admin';
            return FALSE;
        }

        $this->view->post = $post;
        $this->view->postCategories = map( $post->getCategories()->toArray(), 'slug' );
        $this->view->categories = \Db\Sql\Categories::find();
        $this->view->backPage = 'admin';
        $this->view->subPage = 'Edit Article';
        $this->view->buttons = [ 'save' ];
    }

    /**
     * Save a post
     */
    public function saveAction()
    {
        // edit the post
        //
        $data = $this->request->getPost();
        $postAction = new \Actions\Posts\Post();
        $post = $postAction->edit( $data );

        if ( ! $post )
        {
            $this->redirect = 'admin';
            return FALSE;
        }

        // save any categories
        //
        $categoryAction = new \Actions\Posts\Category();
        $categoryAction->saveToPost(
            $post,
            $this->request->getPost( 'category' ));

        // save any tags
        //
        $tagAction = new \Actions\Posts\Tag();
        $tagAction->saveToPost(
            $post,
            $this->request->getPost( 'tags' ));

        // save any artists
        //
        $artistAction = new \Actions\Posts\Artist();
        $artistAction->saveToPost(
            $post,
            $this->request->getPost( 'artists' ));

        // save any images and do the resizing
        //
        if ( $this->request->hasFiles() == TRUE )
        {
            $imageAction = new \Actions\Posts\Image();
            $imageAction->deleteByPost( $post->id );
            $imageAction->upload( $post->id, $this->request->getUploadedFiles() );
        }

        // redirect
        //
        $this->redirect = "admin/edit/{$post->id}";
    }

    /**
     * Delete a post
     */
    public function deleteAction( $id = "" )
    {
        $postAction = new \Actions\Posts\Post();
        $post = $postAction->delete( $id );

        $this->redirect = 'admin';
    }
}
