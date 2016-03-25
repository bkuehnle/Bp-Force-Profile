<?php
/*
Plugin Name: Bp Force Profile
Description: Bp Force Profile
Author: Cajet Régis
License: GNU GENERAL PUBLIC LICENSE 2.0 or later http://www.gnu.org/licenses/gpl.txt
Version: 1.1.1
Text Domain: bp-force-profile
Network: true
*/

//-------------------------------
// Constants

// Configuration
define( 'BP_FP_VERSION', '1.1.1' );
define( 'BP_FP_PLUGIN_NAME', basename( dirname( __FILE__ ) ) );
define( 'BP_FP_PLUGIN_TEXTDOMAIN', 'bp-force-profile' );

// core Paths
define( 'BP_FP_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . BP_FP_PLUGIN_NAME );
define( 'BP_FP_PLUGIN_URL', WP_PLUGIN_URL . '/' . BP_FP_PLUGIN_NAME );
//-------------------------------

/**
 * Init the plugin
 */
function bp_fp_init() 
{
	// this plugin is useless without profiles
	switch ( false ) 
	{
		case ( bp_is_active( 'xprofile' ) ):
			return;
	}

	load_plugin_textdomain('bp-force-profile', false, BP_FP_PLUGIN_NAME . '/languages' );
}

/**
 * Admin menu
 */
function bp_fp_admin_menu()
{
	global $bp;

  	if ( true == $bp->loggedin_user->is_super_admin ):
   		$user_is_admin = true;
  	elseif (true == $bp->loggedin_user->is_site_admin ):
   		$user_is_admin = true;
  	else:
   		$user_is_admin = false;
  	endif;
    
	if ( !$user_is_admin )
		return false;

    require_once( dirname( __FILE__ ) . '/bp-fp-admin.php' );

	add_submenu_page('bp-general-settings', __('Bp Force Profile', 'bp-force-profile'), __('Bp Force Profile Setup', 'bp-force-profile'), 'manage_options', 'bp-fp-settings', 'bp_fp_admin');
}

/**
 * Plugin launcher
 */
function bp_fp_launch() 
{
	if (is_user_logged_in()) 
	{
		$user_id 	= wp_get_current_user()->ID;
		$current_url  = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$redirect_url = bp_fp_get_redirect_url($user_id);

		if (strpos($current_url, $redirect_url) === false)
		{
			global $wpdb;

			$bp_prefix  	 = bp_core_get_table_prefix();
			$xprofile_fields = $wpdb->get_results("SELECT count(*) AS empty_fields_count FROM {$bp_prefix}bp_xprofile_fields WHERE parent_id = 0 AND is_required = 1 AND id NOT IN (SELECT field_id FROM {$bp_prefix}bp_xprofile_data WHERE user_id = {$user_id} AND `value` IS NOT NULL AND `value` != '')");

			foreach ($xprofile_fields as $field) 
			{
				if ($field->empty_fields_count > 0)	
				{
					wp_redirect($redirect_url);
					exit;
				}
			}
		}		
	}
}

/**
 * Plugin styles
 */
function bp_fp_styles()
{
	echo '<link rel="stylesheet" media="screen" type="text/css" href="' . BP_FP_PLUGIN_URL . '/css/styles.css" />';
}

/**
 * Plugin notice
 */
function bp_fp_notice() 
{
	if (is_user_logged_in()) 
	{
		$user_id 	= wp_get_current_user()->ID;
		$current_url  = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$redirect_url = bp_fp_get_redirect_url($user_id);

		if (strpos($current_url, $redirect_url) !== false)
		{
			global $wpdb;

			$bp_prefix  	 = bp_core_get_table_prefix();
			$xprofile_fields = $wpdb->get_results("SELECT `name` FROM {$bp_prefix}bp_xprofile_fields WHERE parent_id = 0 AND is_required = 1 AND id NOT IN (SELECT field_id FROM {$bp_prefix}bp_xprofile_data WHERE user_id = {$user_id} AND `value` IS NOT NULL AND `value` != '')");
	
			$xprofile_fields_count = count($xprofile_fields);
			if ($xprofile_fields_count > 0)
			{
				$message = '<div id="bp_fp_message">' . __('Please complete your profile to continue', 'bp-force-profile') . ' (' . $xprofile_fields_count . __(' fields are missing', 'bp-force-profile') . ')</div>';
				$message .= '<ul id="bp_fp_fields">';

				foreach ($xprofile_fields as $field) 
				{
					$message .= '<li>' . $field->name . '</li>';
				}

				$message .= '</ul>';

				echo '<div id="bp_fp_notice"><div id="bp_fp_container" class="red">' . $message . '</div></div>';
			}	
		}	
	}
}

function bp_fp_get_redirect_url($user_id)
{
	$bp_fp_default_redirect_group_id = get_option('bp_fp_redirect_group_id');
	return bp_members_edit_profile_url(null, $user_id) . (!empty($bp_fp_default_redirect_group_id) ? 'group/' . $bp_fp_default_redirect_group_id .'/' : '');
}

// Hook into BuddyPress
add_action('bp_include'			, 'bp_fp_init');
add_action('admin_menu'			, 'bp_fp_admin_menu');
add_action('template_redirect'		, 'bp_fp_launch');
add_action('wp_head'			, 'bp_fp_styles');
add_action('wp_footer'			,'bp_fp_notice');
?>
