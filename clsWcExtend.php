<?php
class clsWcExtend
{

    function __construct()
    {
        add_filter( 'woocommerce_locate_template', [$this, 'es_custom_email_template_path'], 10, 3 );
        add_filter( 'woocommerce_email_classes', [$this, 'add_email_classes'] );
    }

    public function add_email_classes( $email_classes ) 
    {
        error_log("sib: register custom email class");

        require_once plugin_dir_path( __FILE__ ) . 'WC_CreditEmail.php';
        $email_classes['WC_CreditEmail'] = new WC_CreditEmail();
        return $email_classes;
		//$email_classes['WC_CreditEmail'] = new WC_CreditEmail();
        //return $email_classes;
	}

    function es_custom_email_template_path( $template, $template_name, $template_path ) 
    {

        //$plugin_path = plugin_dir_path( __FILE__ ) . 'templates/';
        $plugin_path = WP_PLUGIN_DIR . '/wp-user-rolencredits/templates/';
    
        error_log("sib: " . $plugin_path . $template_name);

        if ( file_exists( $plugin_path . $template_name ) ) {
            $template = $plugin_path . $template_name;
        }

        error_log("sib: " . $template);
    
        return $template;
    }

    
}

new clsWcExtend();