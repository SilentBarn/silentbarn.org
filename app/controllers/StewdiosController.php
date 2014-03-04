<?php

namespace Controllers;

class StewdiosController extends \Base\Controller
{
    public function beforeExecuteRoute()
    {
        $this->checkLoggedIn = FALSE;

        return parent::beforeExecuteRoute();
    }

    public function indexAction()
    {
        return $this->dispatcher->forward([
            'controller' => 'stewdios',
            'action' => 'about'
        ]);
    }

    public function livingAction()
    {
        $this->view->pick( 'stewdios/living' );
    }

    public function workingAction()
    {
        $this->view->pick( 'stewdios/working' );
    }

    public function applyAction()
    {
        $this->view->pick( 'stewdios/apply' );
    }

    public function aboutAction()
    {
        $this->view->pick( 'stewdios/about' );
    }
}