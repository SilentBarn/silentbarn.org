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
        // Load our services
        $util = $this->getService( 'util' );
        $view = $this->getService( 'view' );
        $filter = $this->getService( 'filter' );
        $config = $this->getService( 'config' );

        // Check the ReCAPTCHA
        $captcha = get( $data, 'g-recaptcha-response' );

        if ( ! $this->verifyCaptcha( $captcha ) ) {
            $util->addMessage( "We couldn't validate the CAPTCHA!", INFO );
            return FALSE;
        }

        // Read in email fields
        $emailInfo = [
            'name' => $filter->sanitize( get( $data, 'name' ), 'striptags' ),
            'email' => $filter->sanitize( get( $data, 'email' ), 'striptags' ),
            'visited' => $filter->sanitize( get( $data, 'visited' ), 'striptags' ),
            'dates' => $filter->sanitize( get( $data, 'dates' ), 'striptags' ),
            'description' => nl2br( $filter->sanitize( get( $data, 'description' ), 'striptags' ) )];

        // Load the data into the view and render the html
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
        $config = $this->getService( 'config' );

        // Set up a new SMTP mailer
        $mailer = new \PHPMailer();
        $mailer->isSMTP();
        $mailer->SMTPDebug = ( $config->mailgun->debug ) ? 1 : 0;
        $mailer->Port = 587;
        $mailer->Host = $config->mailgun->smtp->hostname;
        $mailer->SMTPAuth = TRUE;
        $mailer->AuthType = 'PLAIN';
        $mailer->Username = $config->mailgun->smtp->username;
        $mailer->Password = $config->mailgun->smtp->password;
        $mailer->SMTPSecure = 'tls';

        // Add from and to fields
        $mailer->From = $config->mailgun->from;
        $mailer->FromName = $config->mailgun->fromname;
        $mailer->addAddress( $to );

        // Set up html message and subject
        $mailer->WordWrap = 50;
        $mailer->isHTML( TRUE );
        $mailer->Subject = $subject;
        $mailer->Body = $html;

        return $mailer->send();
    }

    private function verifyCaptcha( $userResponse )
    {
        $config = $this->getService( 'config' );
        $data = [
            'response' => $userResponse,
            'secret' => $config->recaptcha->secretKey
        ];

        $ch = curl_init( $config->recaptcha->verifyUrl );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
        $response = curl_exec( $ch );
        curl_close( $ch );

        $response = @json_decode( $response );

        if ( ! $response
            || ! isset( $response->success )
            || ! $response->success )
        {
            return FALSE;
        }

        return TRUE;
    }
}