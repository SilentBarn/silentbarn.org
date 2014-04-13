<?php

namespace Controllers;

class AboutController extends \Base\Controller
{
    public function beforeExecuteRoute()
    {
        $this->checkLoggedIn = FALSE;

        return parent::beforeExecuteRoute();
    }

    public function indexAction()
    {
        $this->view->pick( 'about/index' );
    }

    public function contactAction()
    {
        $this->data->pageTitle = "Contact Us";
        $this->view->pick( 'about/contact' );
    }

    public function missionAction()
    {
        $this->data->pageTitle = "Mission";
        $this->view->pick( 'about/mission' );
    }

    public function volunteerAction()
    {
        $this->view->pick( 'about/volunteer' );
    }

    public function communityAction()
    {
        $this->view->pick( 'about/community' );
        $this->data->pageTitle = "Community";
        $this->data->members = \Db\Sql\Members::find([
            'is_deleted = 0',
            "order" => "name"
        ]);
    }

    public function pressAction()
    {
        $this->data->pageTitle = "Press";
        $this->view->pick( 'about/press' );
    }

    public function galleryAction()
    {
        $this->data->pageTitle = "Gallery";
        $this->view->pick( 'about/gallery' );
    }
}