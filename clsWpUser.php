<?php
class clsWpUser
{
    function __construct()
    {
        add_action('show_user_profile', [$this, 'es_custom_user_profile_fields'], 999);
        add_action('edit_user_profile', [$this, 'es_custom_user_profile_fields'], 999);
        add_action( 'profile_update', [$this, 'es_save_custom_user_profile_fields'] );
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

        if ( isset($_POST['es_user_role']) && !empty($_POST['es_user_role']) )
        {
            $user = get_user_by('id', $user_id);
            
            if ($user) 
            {
                $user->set_role( $_POST['es_user_role'] );
            }
            
        }

    }

    private function startsWithWcwp($string) 
    {
        return strpos($string, 'wcwp_') === 0;
    }
}

new clsWpUser();

if (!function_exists('p_r')) { function p_r($s) { echo "<pre>"; print_r($s); echo "</pre>"; } }