<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 05/10/2008
 * @license: GPLv2
 */


		global $wp_version, $fbconnect,$fb_reg_formfields;

			// if we're posted back an update, let's set the values here
			if ( isset($_POST['info_update']) ) {
				check_admin_referer('wp-fbconnect-info_update');

				$error = '';
				update_option( 'fb_terms_page',$_POST['fb_terms_page'] );
				update_option( 'fb_show_reg_form',$_POST['fb_show_reg_form'] );
				update_option( 'fb_hide_edit_profile',$_POST['fb_hide_edit_profile'] );
				update_option('fb_permsToRequestOnConnect',$_POST['fb_permsToRequestOnConnect']);				
				
				update_option( 'fb_custom_user_profile',$_POST['fb_custom_user_profile'] );
				update_option( 'fb_custom_reg_form',$_POST['fb_custom_reg_form'] );
				update_option( 'fb_custom_lostpasswd_form',$_POST['fb_custom_lostpasswd_form'] );
				update_option( 'fb_loginreload', $_POST['fb_loginreload'] );
				
				$fb_form_fields=";";
				foreach($fb_reg_formfields as $field){
						if (isset($_POST["fb_reg_form_".$field])){
							$fb_form_fields.=$field.";";							
						}
				}
				update_option( 'fb_form_fields',$fb_form_fields );
				if ($error !== '') {
					echo '<div class="error"><p><strong>'.__('At least one of Facebook Connector options was NOT updated', 'fbconnect').'</strong>'.$error.'</p></div>';
				} else {
					echo '<div class="updated"><p><strong>'.__('Facebook Connector options updated', 'fbconnect').'</strong></p></div>';
				}

			
			}
			
			// Display the options page form
			$siteurl = fb_get_option('home');
			if( substr( $siteurl, -1, 1 ) !== '/' ) $siteurl .= '/';
			?>

			<div class="wrap">
				<h2><?php _e('Facebook Configuration', 'fbconnect') ?>: <?php _e('Registration form', 'fbconnect') ?></h2>

				<form method="post">

     				<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
     					<tr valign="top">
							<th style="width: 33%" scope="row"><label for="fb_permsToRequestOnConnect"><?php _e('Perms to request:', 'fbconnect') ?></label></th>
							<td>
							<input type="text" name="fb_permsToRequestOnConnect" id="fb_api_key" size="50" value="<?php echo fb_get_option('fb_permsToRequestOnConnect');?>"/>
							<label for="fb_permsToRequestOnConnect"><?php _e('Perms to request on user first login (comma separated list) (email,user_about_me,user_birthday,user_location).', 'fbconnect') ?></label>
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><label for="fb_show_reg_form"><?php _e('Show Registration:', 'fbconnect') ?></label></th>
							<td>
								<input type="checkbox" name="fb_show_reg_form" id="fb_show_reg_form" <?php 
									echo fb_get_option('fb_show_reg_form') ? 'checked="checked"' : ''; ?> />
									<label for="fb_show_reg_form"><?php _e('Show registration form', 'fbconnect') ?></label>
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><label for="fb_hide_edit_profile"><?php _e('Hide edit profile:', 'fbconnect') ?></label></th>
							<td>
								<input type="checkbox" name="fb_hide_edit_profile" id="fb_hide_edit_profile" <?php 
									echo fb_get_option('fb_hide_edit_profile') ? 'checked="checked"' : ''; ?> />
									<label for="fb_hide_edit_profile"><?php _e('Hide edit user profile link', 'fbconnect') ?></label>
							</td>
						</tr>
						
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Registration form fields:', 'fbconnect') ?></th>
							<td>
								<?php 
									 $fb_form_fields = fb_get_option('fb_form_fields');
									 foreach($fb_reg_formfields as $field){
									 	$pos = strrpos($fb_form_fields, ";".$field.";");
										$checked = "checked";
										if (is_bool($pos) && !$pos) { 
											 $checked ="";
										}
									 	echo "<input name=\"fb_reg_form_$field\" type=\"checkbox\" value=\"$field\" ".$checked." /> $field<br/>";
									}
								?>	
								

							</td>
						</tr>
						<?php 
						?>
						<tr valign="top">
							<th style="width: 33%" scope="row"><label for="fb_custom_user_profile"><?php _e('Custom profile page:', 'fbconnect') ?></label></th>
							<td>
							<input type="text" name="fb_custom_user_profile" id="fb_custom_user_profile" size="50" value="<?php echo fb_get_option('fb_custom_user_profile');?>"/>
							<label for="fb_custom_user_profile"><?php _e('Custom user profile URL, %USERID% will be replaced with the Wordpress user ID.', 'fbconnect') ?></label>
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><label for="fb_custom_reg_form"><?php _e('Custom registration:', 'fbconnect') ?></label></th>
							<td>
							<input type="text" name="fb_custom_reg_form" id="fb_custom_reg_form" size="50" value="<?php echo fb_get_option('fb_custom_reg_form');?>"/>
							<label for="fb_custom_reg_form"><?php _e('Custom registration form URL.', 'fbconnect') ?></label>
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><label for="fb_custom_lostpasswd_form"><?php _e('Custom password lost:', 'fbconnect') ?></label></th>
							<td>
							<input type="text" name="fb_custom_lostpasswd_form" id="fb_custom_lostpasswd_form" size="50" value="<?php echo fb_get_option('fb_custom_lostpasswd_form');?>"/>
							<label for="fb_custom_reg_form"><?php _e('Custom password lost form URL.', 'fbconnect') ?></label>
							</td>
						</tr>
						
						<?php 
						?>						
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Terms of use:', 'fbconnect') ?></th>
							<td>
								<?php 
								wp_dropdown_pages(array('selected' => fb_get_option('fb_terms_page') , 'name' => 'fb_terms_page', 'show_option_none' => __(' '), 'sort_column'=> 'post_title,menu_order')); 
								?>	
								<label for="fb_terms_page"><?php _e('The site Terms of use page.', 'fbconnect') ?></label>

							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><label for="fb_loginreload"><?php _e('Reload page after login:', 'fbconnect') ?></label></th>
							<td>
								<input type="checkbox" name="fb_loginreload" id="fb_loginreload" <?php 
									echo fb_get_option('fb_loginreload') ? 'checked="checked"' : ''; ?> />
									
							</td>
						</tr>
     				</table>

					
					<?php wp_nonce_field('wp-fbconnect-info_update'); ?>
					
     				<p class="submit"><input class="button-primary" type="submit" name="info_update" value="<?php _e('Update Configuration', 'fbconnect') ?> &raquo;" /></p>
     			</form>