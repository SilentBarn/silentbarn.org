<?php

namespace Actions;

class Email extends \Base\Action
{
    /**
     * Sends the rentals email
     *
     * @param array $data
     * @return boolean
     */
    public function rental( $data )
    {
        // load our services
        $filter = $this->getService( 'filter' );
        $view = $this->getService( 'view' );
        $config = $this->getService( 'config' );

        // read in email fields
        $emailInfo = [
            'name' => $filter->sanitize( get( $data, 'name' ), 'striptags' ),
            'email' => $filter->sanitize( get( $data, 'email' ), 'striptags' ),
            'visited' => $filter->sanitize( get( $data, 'visited' ), 'striptags' ),
            'dates' => $filter->sanitize( get( $data, 'dates' ), 'striptags' ),
            'description' => nl2br( $filter->sanitize( get( $data, 'description' ), 'striptags' ) )];

        // load the data into the view and render the html
        $html = $view->getRender(
            'email', 'inquiry', [
                'params' => $emailInfo,
            ],
            function ( $view ) {
                $view->setRenderLevel( \Phalcon\Mvc\View::LEVEL_LAYOUT );
            });

        return $this->sendEmail(
            $config->mailgun->to->rentals,
            "New Rentals Inquiry!",
            $html );
    }

    /**
     * Sends the event booking email
     *
     * @param array $data
     * @return boolean
     */
    public function event( $data )
    {
        // load our services
        $filter = $this->getService( 'filter' );
        $view = $this->getService( 'view' );
        $config = $this->getService( 'config' );

        // read in email fields
        $emailInfo = [
            'name' => $filter->sanitize( get( $data, 'name' ), 'striptags' ),
            'email' => $filter->sanitize( get( $data, 'email' ), 'striptags' ),
            'message' => nl2br( $filter->sanitize( get( $data, 'message' ), 'striptags' ) )];

        // load the data into the view and render the html
        $html = $view->getRender(
            'email', 'inquiry', [
                'params' => $emailInfo,
            ],
            function ( $view ) {
                $view->setRenderLevel( \Phalcon\Mvc\View::LEVEL_LAYOUT );
            });

        return $this->sendEmail(
            $config->mailgun->to->events,
            "New Event Booking Inquiry!",
            $html );
    }

    /**
     * Sends an email through the Mailgun API
     *
     * @param string $to
     * @param string $subject
     * @param string $html
     * @return boolean
     */
    public function sendEmail( $to, $subject, $html )
    {
        // get the required services
        $config = $this->getService( 'config' );

        // instantiate new mailer
        $mailer = new \PHPMailer();

        // set up SMTP
        $mailer->isSMTP();
        //$mailer->SMTPDebug = 1;
        $mailer->Port = 587;
        $mailer->Host = $config->mailgun->smtp->hostname;
        $mailer->SMTPAuth = TRUE;
        $mailer->AuthType = 'PLAIN';
        $mailer->Username = $config->mailgun->smtp->username;
        $mailer->Password = $config->mailgun->smtp->password;
        $mailer->SMTPSecure = 'tls';

        // add from and to fields
        $mailer->From = $config->mailgun->from;
        $mailer->FromName = $config->mailgun->fromname;
        $mailer->addAddress( $to );

        // set up html message and subject
        $mailer->WordWrap = 50;
        $mailer->isHTML( TRUE );
        $mailer->Subject = $subject;
        $mailer->Body = $html;

        return $mailer->send();
    }
}