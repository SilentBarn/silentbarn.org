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
        $this->data->pageTitle = "Community";
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

    public function chefsAction()
    {
        $this->view->pick( 'about/chefs' );
        $this->data->pageTitle = "Chefs";
        $this->data->members = \Db\Sql\Members::find([
            'is_deleted = 0 and is_chef = 1 and is_active = 1',
            "order" => "name"
        ]);
    }

    public function pressAction()
    {
        $this->data->pageTitle = "Press";
        $this->data->pageContent = \Db\Sql\Pages::findFirstByName( 'press' );
        $this->data->recentPosts = \Db\Sql\Posts::getByCategoryDateRange([
            'categories' => [ PRESS ],
            'startDate' => date( DATE_DATABASE, 0 ),
            'limit' => 3 ]);
        $this->view->pick( 'about/press' );
    }

    public function galleryAction()
    {
        $this->data->spaces = \Db\Sql\Spaces::find([
            'is_deleted = 0 and is_active = 1 and is_gallery = 1',
            'order' => 'name'
        ]);
        $this->data->pageTitle = "Barn Art";
        $this->view->pick( 'about/gallery' );
    }
}