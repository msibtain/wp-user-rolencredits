<?php
class clsWpUser
{
    function __construct()
    {
        add_action('show_user_profile', [$this, 'es_custom_user_profile_fields'], 999);
        add_action('edit_user_profile', [$this, 'es_custom_user_profile_fields'], 999);
        add_action( 'profile_update', [$this, 'es_save_custom_user_profile_fields'] );

        //add_filter( 'woocommerce_email_enabled_customer_processing_order',  [$this, 'es_disable_wc_email'], 10, 2 );
        //add_filter( 'woocommerce_email_enabled_customer_completed_order',  [$this, 'es_disable_wc_email'], 10, 2 );
        
    }

    function es_custom_user_profile_fields( $user )
    {
        $wpwcRoles = [];
        $editable_roles = get_editable_roles();
        foreach ($editable_roles as $role => $details) 
        {
            if ( $this->startsWithWcwp($role) )
            {
                $wpwcRoles[$role] = $details['name'];
            }
        }
        include('views/admin/user_fields.php');
    }

    function es_save_custom_user_profile_fields( $user_id )
    {
        if (!current_user_can('edit_user', $user_id)) 
        {
            return false;
        }

        # update role;
        if ( isset($_POST['es_user_role']) && !empty($_POST['es_user_role']) )
        {
            $user = get_user_by('id', $user_id);
            
            if ($user) 
            {
                $user->set_role( $_POST['es_user_role'] );
            }
            
        }

        # add credits;
        if ( isset($_POST['es_user_credits']) && !empty($_POST['es_user_credits']) )
        {
            $this->addUserCredits( $user_id,  $_POST['es_user_credits']);
        }

        # set / unset email preference;
        if (isset($_POST['send_invoice_to_mail']) && $_POST['send_invoice_to_mail'] === "1")
        {
            $user = get_user_by('id', $user_id);

            if ( $post_parent = $this->isAdded( $user->user_email ) )
            {
                $history = $this->userCreditHistory( $post_parent );
                
                $temp[] = [
                    'email' => $user->user_email,
                    'history' => $history
                ];
                $this->sendCreditHistoryEmail($user->user_email, $user->user_nicename, $history, $post_parent, $user_id);
            }
            
        }

        $value = isset($_POST['send_invoice_to_mail']) ? 1 : 0;
        update_user_meta($user_id, 'send_invoice_to_mail', $value);
        
    }

    private function userCreditHistory( $post_parent ): array
    {
        $current_year = date('Y');
        $current_month = date('m');
        //$current_month = "11";

        $args = array(
            'date_query' => array(
                array(
                    'year'  => $current_year,
                    'month' => $current_month,
                ),
            ),
            'post_type'      => 'wc_cs_credits_txn',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'post_parent' => $post_parent,
        );

        
        $arrPosts = get_posts( $args );

        $history = [];
        if (count($arrPosts))
        {
            foreach ($arrPosts as $objPost)
            {
                $history[] = [
                    'title' => $objPost->post_title,
                    'description' => $objPost->post_content,
                    'date' => date( "M d, Y h:i a", get_post_meta($objPost->ID, "_date_created", true) ),
                    'credited' => get_post_meta($objPost->ID, "_credited", true),
                    'debited' => get_post_meta($objPost->ID, "_debited", true),
                    'balance' => get_post_meta($objPost->ID, "_balance", true),
                ];
            }
        }

        return $history;
    }

    private function sendCreditHistoryEmail($recipient, $name, $history, $credit_id, $user_id)
    {
        error_log('sib: sending credit history email to ' . $recipient);
        $post_meta = get_post_meta($credit_id);
        
        $user_billing_detail = $this->get_user_billing_details( $user_id );
        
        $email_class = WC()->mailer()->emails['WC_MonthlyCreditEmail'];
        $custom_data = [
            'history' => $history,
            'blogname' => get_bloginfo( 'name' ),
            'user_nicename' => $name,
            'user_email' => $recipient,
            'user_billing_detail' => $user_billing_detail,
            'meta' => [
                '_total_outstanding_amount' => $post_meta['_total_outstanding_amount'][0],
                '_due_day' => $post_meta['_due_day'][0],
                '_available_credits' => $post_meta['_available_credits'][0]
            ]
        ];

        $email_class->trigger( $recipient, $custom_data );
        //$email_class->trigger( "mohsibtain@gmail.com", $custom_data );
    }

    private function addUserCredits( $user_id, $credits )
    {
        $objUser = get_user_by('id', $user_id);
        $post_title = $objUser->user_login . " / " . $objUser->user_email;

        $credit_post_id = '';
        $credit_post_id = $this->isAdded( $objUser->user_email );
        if ( !$credit_post_id )
        {
            $credit_post_id = wp_insert_post([
                'post_type' => 'wc_cs_credits',
                'post_title' => $post_title,
                'post_status' => '_wc_cs_active',
                'post_author' => $user_id
            ]);

            update_post_meta($credit_post_id, "_id", $credit_post_id);
            update_post_meta($credit_post_id, "_date_created", time());
            update_post_meta($credit_post_id, "_billing_day", "1");
            update_post_meta($credit_post_id, "_due_day", "20");
            update_post_meta($credit_post_id, "_due_duration_by", "this-month");
            update_post_meta($credit_post_id, "_last_billed_status", "");
            update_post_meta($credit_post_id, "_last_billed_date", "");
            update_post_meta($credit_post_id, "_last_billed_due_date", "");
            update_post_meta($credit_post_id, "_last_billed_amount", 0);
            update_post_meta($credit_post_id, "_last_payment_date", "");
            update_post_meta($credit_post_id, "_last_payment_order_id", 0);
            update_post_meta($credit_post_id, "_last_billed_outstanding_amount", 0);
            update_post_meta($credit_post_id, "_total_outstanding_amount", 0);
            update_post_meta($credit_post_id, "_statements", []);
            update_post_meta($credit_post_id, "_next_bill_date", "");
            update_post_meta($credit_post_id, "_rule_applied", "");
            update_post_meta($credit_post_id, "_created_via", "admin");
            update_post_meta($credit_post_id, "_type", "auto");
            update_post_meta($credit_post_id, "_charge_late_fee", "yes");
            update_post_meta($credit_post_id, "_attachments", []);
            update_post_meta($credit_post_id, "_form_fields", []);

            update_post_meta($credit_post_id, "_user_first_name", $objUser->first_name);
            update_post_meta($credit_post_id, "_user_last_name", $objUser->last_name);
            update_post_meta($credit_post_id, "_user_company", "");
            update_post_meta($credit_post_id, "_user_address_1", "");
            update_post_meta($credit_post_id, "_user_address_2", "");
            update_post_meta($credit_post_id, "_user_city", "");
            update_post_meta($credit_post_id, "_user_state", "");
            update_post_meta($credit_post_id, "_user_postcode", "");
            update_post_meta($credit_post_id, "_user_country", "");
            update_post_meta($credit_post_id, "_user_phone", "");
            update_post_meta($credit_post_id, "_total_orders_placed_by_user", 0);
            update_post_meta($credit_post_id, "_total_amount_spent_by_user", "0.00");
            update_post_meta($credit_post_id, "_highest_order_value_by_user", "0.00");
            update_post_meta($credit_post_id, "_lowest_order_value_by_user", "0.00");
            update_post_meta($credit_post_id, "_avg_monthly_amount_spent_by_user", "0.00");
            update_post_meta($credit_post_id, "_avg_yearly_amount_spent_by_user", "0.00");
            update_post_meta($credit_post_id, "_user_address_index", $objUser->first_name . " " . $objUser->last_name . " " . $objUser->user_email);
            
            update_post_meta($credit_post_id, "_user_email", $objUser->user_email);
        }
        else
        {
            # update credit status to active;
            $post_data = array(
                'ID'          => $credit_post_id,
                'post_status' => '_wc_cs_active',
            );
            wp_update_post($post_data, true);
        }

        $approved_credits = get_post_meta($credit_post_id, "_approved_credits", true);
        $approved_credits = (float)$approved_credits +  (float)$credits;
        update_post_meta($credit_post_id, "_approved_credits", $approved_credits);

        $available_credits = get_post_meta($credit_post_id, "_available_credits", true);
        $available_credits = (float)$available_credits +  (float)$credits;
        update_post_meta($credit_post_id, "_available_credits", $available_credits);

        $this->addCreditTransaction($credit_post_id, $credits, $available_credits, $user_id);
        $this->sendCreditEmail($objUser->user_email, $credits, $available_credits, $objUser->user_nicename);
        
    }

    private function addCreditTransaction($post_parent, $credits, $balance, $post_author)
    {
        $txn_post_id = wp_insert_post([
            'post_type' => 'wc_cs_credits_txn',
            'post_title' => 'Credits Transaction',
            'post_status' => '_wc_cs_unbilled',
            'post_content' => 'Credits Advance Payment',
            'ping_status' => 'closed',
			'post_parent' => $post_parent,
            'post_author' => $post_author
        ]);

        
        update_post_meta($txn_post_id, "_id", $txn_post_id);
        update_post_meta($txn_post_id, "_date_created", time());
        update_post_meta($txn_post_id, "_order_id", 0);
        update_post_meta($txn_post_id, "_billed_date", "");
        update_post_meta($txn_post_id, "_credited", $credits);
        update_post_meta($txn_post_id, "_debited", 0);
        update_post_meta($txn_post_id, "_balance", $balance);

    }

    private function sendCreditEmail($recipient, $credits, $balance, $name)
    {
        error_log('sib: sending credit add email to ' . $recipient);
        
        $email_class = WC()->mailer()->emails['WC_CreditEmail'];
        $custom_data = [
            'new_credit' => '$' . $credits,
            'date_created' => date("M d, Y h:i a"),
            'total_credit' => '$' . $balance,
            'blogname' => get_bloginfo( 'name' ),
            'user_nicename' => $name
        ];

        $email_class->trigger( $recipient, $custom_data );
    }

    function isAdded($email)
    {
        $args = array(
            'post_type'  => 'wc_cs_credits',
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key'   => "_user_email",
                    'value' => $email
                )
            )
        );

        $arrPosts = get_posts( $args );
        return ($arrPosts) ? $arrPosts[0]->ID : "";

    }

    function es_disable_wc_email( $enabled, $order )
    {
        $user_id = $order->get_user_id();
        $send_invoice_to_mail = get_user_meta($user_id, 'send_invoice_to_mail', true);

        if ($send_invoice_to_mail === 1 || $send_invoice_to_mail === "1")
        {
            return true;
        }
        else
        {
            return false;
        }

        //return $enabled;
    }


    private function startsWithWcwp($string) 
    {
        return strpos($string, 'wcwp_') === 0;
    }

    private function get_user_billing_details($user_id) {
        // Check if the user exists
        if (!get_user_by('id', $user_id)) {
            return 'User not found.';
        }
    
        // WooCommerce user meta keys for billing details
        $billing_details = [
            'billing_first_name' => get_user_meta($user_id, 'billing_first_name', true),
            'billing_last_name'  => get_user_meta($user_id, 'billing_last_name', true),
            'billing_company'    => get_user_meta($user_id, 'billing_company', true),
            'billing_address_1'  => get_user_meta($user_id, 'billing_address_1', true),
            'billing_address_2'  => get_user_meta($user_id, 'billing_address_2', true),
            'billing_city'       => get_user_meta($user_id, 'billing_city', true),
            'billing_postcode'   => get_user_meta($user_id, 'billing_postcode', true),
            'billing_country'    => get_user_meta($user_id, 'billing_country', true),
            'billing_state'      => get_user_meta($user_id, 'billing_state', true),
            'billing_phone'      => get_user_meta($user_id, 'billing_phone', true),
            'billing_email'      => get_user_meta($user_id, 'billing_email', true),
        ];
    
        return $billing_details;
    }
}

global $clsWpUser;
$clsWpUser = new clsWpUser();

if (!function_exists('p_r')) { function p_r($s) { echo "<pre>"; print_r($s); echo "</pre>"; } }