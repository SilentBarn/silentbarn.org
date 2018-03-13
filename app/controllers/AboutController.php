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
        $this->data->pageTitle = "About Silent Barn";
        $this->view->pick( 'about/contact' );
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

    /* Removed
    public function volunteerAction()
    {
        $this->data->pageTitle = "Volunteering";
        $this->view->pick( 'about/volunteer' );
    }
    */

    /* Removed
    public function jobsAction()
    {
        $this->data->pageTitle = "Jobs";
        $this->view->pick( 'about/jobs' );
    }
    */

    /**
     * @param string $flag If 'thankyou' then show a thank you message
     */
    public function rentalsAction( $flag = "" )
    {
        if ( str_eq( $flag, "thankyou" ) ) {
            $this->data->notifications[] = [
                'success' =>
                    "Your inquiry has successfully been sent! We'll ".
                    "contact you shortly." ];
        }

        $this->data->pageTitle = "Rentals";
        $this->data->recaptchaKey = $this->config->recaptcha->siteKey;
        $this->view->pick( 'about/rentals' );
    }

    /**
     * POST request to send an email to the rentals admin
     */
    public function rentalinquiryAction()
    {
        // Read in the post data and send the email out
        $data = $this->request->getPost();

        // This will check the CAPTCHA
        $action = new \Actions\Email();
        $success = $action->rental( $data );

        // Redirect to thank you page
        $this->redirect = ( $success )
            ? "about/rentals/thankyou"
            : "about/rentals";
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
        $this->data->pageNav = [
            'partial' => 'partials/spaces/nav',
            'page' => 'gallery' ];
        $this->data->pageTitle = "Barn Art";
        $this->view->pick( 'about/gallery' );
    }

    public function donateAction()
    {
        $this->data->pageTitle = "Donate!";

        /*
        // Try to get content from database
        $page = \Db\Sql\Pages::findFirstByName( 'donate' );

        if ( ! $page || ! valid( $page, STRING ) )
        {
            $this->data->page = $page;
            $this->view->pick( 'partials/page' );
        }
        else
        {
            $this->view->pick( 'about/donate' );
        }
        */

        $this->view->pick( 'about/donate' );
    }

    public function membershipsAction()
    {
        $this->data->pageTitle = "Memberships";
        $this->view->pick( 'about/memberships' );
    }

    public function thankyouAction()
    {
        $this->data->pageTitle = "Thank You!";
        $this->view->pick( 'about/thankyou' );
    }

    public function saferspacesAction()
    {
        $this->data->pageTitle = "Safer Spaces Policy";
        $this->view->pick( 'about/saferspaces' );
    }
}