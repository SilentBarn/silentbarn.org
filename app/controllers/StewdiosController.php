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
            'action' => 'living'
        ]);
    }

    public function livingAction()
    {
        // get the residence spaces
        $this->data->spaces = \Db\Sql\Spaces::find([
            'is_deleted = 0 and is_active = 1 and is_residence = 1',
            'order' => 'name'
        ]);
        $this->data->pageNav = [
            'partial' => 'partials/spaces/nav',
            'page' => 'living' ];
        $this->data->pageTitle = "Living Stewdios";
        $this->view->pick( 'stewdios/living' );
    }

    public function workingAction()
    {
        $this->data->spaces = \Db\Sql\Spaces::find([
            'is_deleted = 0 and is_active = 1 and is_stewdio = 1',
            'order' => 'name'
        ]);
        $this->data->pageNav = [
            'partial' => 'partials/spaces/nav',
            'page' => 'working' ];
        $this->data->pageTitle = "Working Stewdios";
        $this->view->pick( 'stewdios/working' );
    }

    public function applyAction()
    {
        $this->view->pick( 'stewdios/apply' );
    }

    public function airswapAction()
    {
        $this->view->pick( 'stewdios/airswap' );
    }
}