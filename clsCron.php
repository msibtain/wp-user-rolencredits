<?php
class clsCron
{
    function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route('users', '/sendcredits/', array(
                'methods' => 'GET',
                'callback' => [$this, 'es_send_credits_to_users'],
                'permission_callback' => '__return_true'
            ));
        });

    }

    function es_send_credits_to_users($data)
    {
        global $clsWpUser;

        # For single user;
        if ( isset($data['email']) && !empty($data['email']) )
        {
            $user = get_user_by("email", $data['email']);

            if ( $post_parent = $clsWpUser->isAdded( $user->user_email ) )
            {
                $history = $this->userCreditHistory( $post_parent );
                
                $temp[] = [
                    'email' => $user->user_email,
                    'history' => $history
                ];
                $this->sendCreditHistoryEmail("mohsibtain@gmail.com", $user->user_nicename, $history);

                return new WP_REST_Response(
                    array(
                      'data' => $temp
                    )
                );
            }
            else
            {
                return new WP_REST_Response(
                    array(
                      'message' => 'No credit history found for this user.',
                      'email' => $data['email']
                    )
                );
            }
        }

        # for all subscribed users;
        $users = $this->getSubscribedUsers();

        $temp = [];

        if (count ($users))
        {
            foreach ($users as $user)
            {
                if ( $post_parent = $clsWpUser->isAdded( $user->user_email ) )
                {
                    $history = $this->userCreditHistory( $post_parent );
                    
                    $temp[] = [
                        'email' => $user->user_email,
                        'history' => $history
                    ];
                    $this->sendCreditHistoryEmail("mohsibtain@gmail.com", $user->user_nicename, $history);
                }
            }
        }
        return new WP_REST_Response(
            array(
              'data' => $temp,
              'users' => $users
            )
        );
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

    private function sendCreditHistoryEmail($recipient, $name, $history)
    {
        error_log('sib: sending credit history email to ' . $recipient);
        
        $email_class = WC()->mailer()->emails['WC_MonthlyCreditEmail'];
        $custom_data = [
            'history' => $history,
            'blogname' => get_bloginfo( 'name' ),
            'user_nicename' => $name
        ];

        $email_class->trigger( $recipient, $custom_data );
    }

    private function getSubscribedUsers()
    {
        $args = array(
            'meta_key'   => "send_invoice_to_mail",
            'meta_value' => "1",
            'number'     => -1,
            'fields'     => ['ID', 'user_email', 'user_nicename'],
        );

        $user_query = new WP_User_Query($args);

        if (!empty($user_query->get_results())) 
        {
            return $user_query->get_results();
        } 
        else 
        {
            return [];
        }
    }
}

new clsCron();