<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 05/10/2008
 * @license: GPLv2
 */

if (!class_exists('WPgConnect_Interface')):
class WPgConnect_Interface {
	function endsWith($haystack, $needle){
   		$length = strlen($needle);
    	$start  = $length * -1; //negative
    	return (substr($haystack, $start) === $needle);
	}
		
	function getRedirectUrl(){
		$siteUrl= fb_get_option('siteurl');
		if (!WPgConnect_Interface::endsWith("/",$siteUrl)){
			$siteUrl = $siteUrl . "/";
		}
		$siteUrl = WPfbConnect_Logic::add_urlParam($siteUrl,"oauth_login=google");	
		return $siteUrl;
	}
	
	/*
	 * Display and handle updates from the Admin screen options page.
	 *
	 * @options_page
	 */
	function options_page() {
		global $wp_version, $fbconnect,$fb_reg_formfields;

			if ( isset($_POST['info_update']) ) {
				check_admin_referer('wp-fbconnect-info_update');

				$error = '';
				update_option( 'g_login_enabled', $_POST['g_login_enabled'] );
				update_option( 'g_api_key', $_POST['g_api_key'] );
				update_option( 'g_appId', $_POST['g_appId'] );
				update_option( 'g_api_secret', $_POST['g_api_secret'] );
				
				if ($error !== '') {
					echo '<div class="error"><p><strong>'.__('At least one of Google Connector options was NOT updated', 'fbconnect').'</strong>'.$error.'</p></div>';
				} else {
					echo '<div class="updated"><p><strong>'.__('Google Connector options updated', 'fbconnect').'</strong></p></div>';
				}

			
			}
			
			// Display the options page form
			$siteurl = fb_get_option('home');
			if( substr( $siteurl, -1, 1 ) !== '/' ) $siteurl .= '/';
			?>
			<div class="wrap">
				<h2>
					<img src="<?php echo FBCONNECT_PLUGIN_URL;?>/images/gplus-20.png"/>
					<?php _e('Google Configuration', 'fbconnect') ?></h2>

				<form method="post">


					<h3><?php _e('Google Application Configuration', 'fbconnect') ?></h3>
     				<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
     					<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Enable Google login:', 'fbconnect') ?></th>
							<td>
								<p><input type="checkbox" name="g_login_enabled" id="g_login_enabled" <?php
								if( fb_get_option('g_login_enabled')) echo 'checked="checked"'
								?> />
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Google App. Config.', 'fbconnect') ?></th>
							<td>
							<a href="https://code.google.com/apis/console" target="_blank"><?php _e('Create a new Google Application and activate Google+ service', 'fbconnect') ?></a><br/>
							<br/><?php _e('Your Google redirect URI (copy and paste in the Google Application configuration):', 'fbconnect') ?>
							<br/><input type="text" name="g_callback" id="g_callback" size="50" value="<?php echo WPgConnect_Interface::getRedirectUrl();?>"/>
							</td>
						</tr>

						<tr valign="top">
							<th style="width: 33%" scope="row"><label for="g_appId"><?php _e('Google client ID:', 'fbconnect') ?></label></th>
							<td>
							<input type="text" name="g_appId" id="g_appId" size="50" value="<?php echo fb_get_option('g_appId');?>"/>
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><label for="g_api_secret"><?php _e('Google client secret:', 'fbconnect') ?></label></th>
							<td>
							<input type="text" name="g_api_secret" size="50" id="g_api_secret" value="<?php echo fb_get_option('g_api_secret');?>"/>
							</td>
						</tr>
						<tr valign="top" >
							<th style="width: 33%" scope="row"><label for="g_api_key"><?php _e('Developer API Key:', 'fbconnect') ?></label></th>
							<td>
							<input type="text" name="g_api_key" id="g_api_key" size="50" value="<?php echo fb_get_option('g_api_key');?>"/>
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
