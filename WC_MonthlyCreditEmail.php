<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_MonthlyCreditEmail extends WC_Email {

    private $custom_data = [];

    public function __construct() {

        $this->id          = 'es_monthly_credit_email';
        $this->title       = 'User Monthly Credits Email';
        $this->description = 'User Monthly Credits Email sent to user every month if subscribed.';

        $this->subject = __( '[{site_title}] - Monthly Credit History' ) ;
		$this->heading = __( 'Monthly Credit History' ) ;
        
        $this->template_html  = 'emails/credit-history-email.php' ;
        $this->template_plain = 'emails/plain/credit-history-email.php' ;
        $this->placeholders   = array(
            '{order_date}'   => '',
            '{order_number}' => '',
                ) ;

        parent::__construct();
    }

    public function trigger( $recipient, $custom_data = [] ) {
        if ( ! $recipient ) {
            return;
        }

        $this->recipient = $recipient;
        $this->custom_data = $custom_data;

        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
    }

    public function get_content_html() {
        ob_start();
        wc_get_template(
            $this->template_html,
            [
                'email_heading' => $this->get_heading(),
                'custom_data'   => $this->custom_data,
                'email'         => $this,
            ]
        );
        return ob_get_clean();
    }

    public function get_content_plain() {
        ob_start();
        wc_get_template(
            $this->template_plain,
            [
                'email_heading' => $this->get_heading(),
                'custom_data'   => $this->custom_data,
                'email'         => $this,
            ]
        );
        return ob_get_clean();
    }
}

new WC_MonthlyCreditEmail();