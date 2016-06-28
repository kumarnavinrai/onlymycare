<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 05/10/2008
 * @license: GPLv2
 */

if (!class_exists('WPtConnect_Interface')):
class WPtConnect_Interface {
	
	/*
	 * Display and handle updates from the Admin screen options page.
	 *
	 * @options_page
	 */
	function options_page() {
		global $wp_version;

			if ( isset($_POST['info_update']) ) {
				check_admin_referer('wp-fbconnect-info_update');

				$error = '';
				update_option( 't_login_enabled', $_POST['t_login_enabled'] );
				update_option( 'tw_api_key', $_POST['tw_api_key'] );
				update_option( 'tw_api_secret', $_POST['tw_api_secret'] );
				if ($error !== '') {
					echo '<div class="error"><p><strong>'.__('At least one of Twitter Connector options was NOT updated', 'fbconnect').'</strong>'.$error.'</p></div>';
				} else {
					echo '<div class="updated"><p><strong>'.__('Twitter Connector options updated', 'fbconnect').'</strong></p></div>';
				}

			
			}
			
			// Display the options page form
			$siteurl = fb_get_option('home');
			if( substr( $siteurl, -1, 1 ) !== '/' ) $siteurl .= '/';
			?>
			<div class="wrap">
				<h2>
					<img src="<?php echo FBCONNECT_PLUGIN_URL;?>/images/twitter-20.png"/>
					<?php _e('Twitter Configuration', 'fbconnect') ?></h2>

				<form method="post">


					<h3><?php _e('Twitter Application Configuration', 'fbconnect') ?></h3>
     				<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
     					<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Enable Twitter login:', 'fbconnect') ?></th>
							<td>
								<p><input type="checkbox" name="t_login_enabled" id="t_login_enabled" <?php
								if( fb_get_option('t_login_enabled')) echo 'checked="checked"'
								?> />
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Twitter App. Config.', 'fbconnect') ?></th>
							<td>
							<a href="https://dev.twitter.com/apps/new" target="_blank"><?php _e('Create a new Twitter Application', 'fbconnect') ?></a><br/>
							<a href="https://dev.twitter.com/apps" target="_blank"><?php _e('Admin your Twitter Apps', 'fbconnect') ?></a>
							</td>
						</tr>

						<tr valign="top">
							<th style="width: 33%" scope="row"><label for="tw_api_key"><?php _e('Twitter Consumer key:', 'fbconnect') ?></label></th>
							<td>
							<input type="text" name="tw_api_key" id="tw_api_key" size="50" value="<?php echo fb_get_option('tw_api_key');?>"/>
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><label for="tw_api_secret"><?php _e('Twitter Consumer secret:', 'fbconnect') ?></label></th>
							<td>
							<input type="text" name="tw_api_secret" size="50" id="tw_api_secret" value="<?php echo fb_get_option('tw_api_secret');?>"/>
							</td>
						</tr>							

     				</table>

					
					<?php wp_nonce_field('wp-fbconnect-info_update'); ?>
					
     				<p class="submit"><input class="button-primary" type="submit" name="info_update" value="<?php _e('Update Configuration', 'fbconnect') ?> &raquo;" /></p>
     			</form>
				
			</div>
    			<?php
	} // end function options_page


}
endif;

?>
