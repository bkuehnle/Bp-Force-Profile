<?php
add_action( 'admin_init', 'bp_fp_admin_init' );

function bp_fp_admin_init()
{
	register_setting ('bp_fp_settings', 'bp_fp_redirect_group_id');
}

function bp_fp_admin()
{
	if (isset( $_POST['submit'] ) && check_admin_referer('update_bp_fp_settings', 'bp_fp_form_settings'))
	{
		update_option('bp_fp_redirect_group_id', $_POST['bp_fp_default_group_id']);
		$updated = true;
	}

	$bp_fp_default_redirect_group_id = get_option('bp_fp_redirect_group_id');

?>
<div class="wrap nosubsub">

	<h2><?php _e('Bp Force Profile', 'bp-force-profile') ?></h2>

	<h3><?php _e('Thank you for installing Bp Force Profile!', 'bp-force-profile') ?></h3>

	<form action="" name="bp_fp_settings_form" id="bp_fp_settings_form" method="post">
	<table border="0" class="widefat">
		<thead>
			<tr>
				<th colspan="2">
					<?php _e('Informations', 'bp-force-profile') ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th><?php _e('Version', 'bp-force-profile') ?>:</th>
				<th><?php print BP_FP_VERSION ?></th>
			</tr>
		</tbody>
		<tbody>
			<tr>
				<th><?php _e('Default group id', 'bp-force-profile') ?>:</th>
				<th><input name="bp_fp_default_group_id" type="text" id="bp_fp_default_group_id"  value="<?php echo esc_attr($bp_fp_default_redirect_group_id); ?>" size="20" /></th>
			</tr>
		</tbody>
		<tbody>
			<tr>
				<td colspan="2" align="center"><input type="submit" name="submit" value="<?php _e( 'Save Settings', 'bp-force-profile' ) ?>"/></td>
			</tr>
		</tbody>
	</table>
	<?php
		/* This is very important, don't leave it out. */
		wp_nonce_field( 'update_bp_fp_settings', 'bp_fp_form_settings' );
	?>
	</form>

	<h3><?php _e('Quick Start', 'bp-force-profile') ?></h3>
	<p>
		<?php _e('There are no additional installation steps required after plugin activation. Each users with a non complete profile are redirecting to their profile page and a notice is displayed with the required fields.', 'bp-force-profile') ?>
	</p>

	<h3><?php _e('Support', 'bp-force-profile') ?></h3>
	<ul>
		<li><?php _e('https://github.com/bkuehhnle/Bp-Force-Profile.git" target="_blank">Download</a> from GitHub</li>', 'bp-force-profile') ?>
	</ul>

	<p>
		<?php _e('A new plugin from <a href="http://regiscajet.com">Regis Cajet</a>. Have questions or suggestions? Go to <a href="http://regiscajet.com/contact/">Contact</a>', 'bp-force-profile') ?>
	</p>


</div>
<?php
}
?>
