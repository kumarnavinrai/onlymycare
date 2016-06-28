<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 05/10/2008
 * @license: GPLv2
 */

include_once FBCONNECT_PLUGIN_PATH.'/fbConfig.php';

	function register_feed_forms($fb_online_stories,$fb_short_stories_title,$fb_short_stories_body,$fb_full_stories_title,$fb_full_stories_body) {
	  $one_line_stories = $short_stories = $full_stories = array();
	
	  $one_line_stories[] = $fb_online_stories;
	  $short_stories[] = array('template_title' => $fb_short_stories_title,
	                         'template_body' => $fb_short_stories_body);
	  $full_stories = array('template_title' => $fb_full_stories_title,
	                         'template_body' => $fb_full_stories_body);
	  $form_id = fb_feed_registerTemplateBundle($one_line_stories,$short_stories,$full_stories);
		
	  return $form_id;
	}

	global $wp_version, $fbconnect,$fb_reg_formfields;

			if (isset($_POST['template_update'])){
				check_admin_referer('wp-fbconnect-info_update');

				$error = '';
				update_option( 'fb_enable_commentform', isset($_POST['enable_commentform']) ? true : false );
				update_option( 'fb_enable_approval', isset($_POST['enable_approval']) ? true : false );
				update_option('fb_connect_comments_login',$_POST['fb_connect_comments_login']);
				
				update_option('fb_comments_logo',$_POST['fb_comments_logo']);
				update_option( 'fb_short_stories_title', $_POST['fb_short_stories_title'] );
				update_option( 'fb_short_stories_body', $_POST['fb_short_stories_body'] );
				update_option( 'fb_add_main_image', $_POST['fb_add_main_image'] );				
				update_option( 'fb_add_wpmain_image', $_POST['fb_add_wpmain_image'] );	
				update_option( 'fb_show_fbcomments', $_POST['fb_show_fbcomments'] );	
				update_option( 'fb_fbcomments_width', $_POST['fb_fbcomments_width'] );
				update_option( 'fb_fbcomments_canvas_width', $_POST['fb_fbcomments_canvas_width'] );
				update_option( 'fb_fbcomments_numposts', $_POST['fb_fbcomments_numposts'] );
				update_option( 'fb_fbcomments_home', $_POST['fb_fbcomments_home'] );
				update_option( 'fb_hide_wpcomments', $_POST['fb_hide_wpcomments'] );	
				update_option( 'fb_fbcomments_home_numposts', $_POST['fb_fbcomments_home_numposts'] );
				update_option( 'fb_fbcomments_color', $_POST['fb_fbcomments_color'] );
				update_option( 'fb_fbcomments_usexid', $_POST['fb_fbcomments_usexid'] );
				
				//update_option( 'fb_og_type', $_POST['fbconnect_og_type'] );	
				//update_option( 'fb_admins', $_POST['fb_admins'] );	
			}
			
			// Display the options page form
			$siteurl = fb_get_option('home');
			if( substr( $siteurl, -1, 1 ) !== '/' ) $siteurl .= '/';
			?>
			<div class="wrap">
				<h2><?php _e('Facebook Open Graph Options', 'fbconnect') ?></h2>

				<form method="post">

					<h3><?php _e('Feed comment template', 'fbconnect') ?></h3>
	<?php
							$fb_short_stories_title = fb_get_option('fb_short_stories_title');
							if (!$fb_short_stories_title){
								$fb_short_stories_title = '{*actor*} commented on {*blogname*}';
							}
							$fb_short_stories_body = fb_get_option('fb_short_stories_body');
							if (!$fb_short_stories_body){
								$fb_short_stories_body = '{*body_short*}';
							}
						$fb_user = fb_get_loggedin_user();

							?>

	     				<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
	     					<tr valign="top">
							<th style="width: 33%" scope="row"><label for="enable_approval"><?php _e('Automatic Approval:', 'fbconnect') ?></label></th>
							<td>
								<p><input type="checkbox" name="enable_approval" id="enable_approval" <?php 
									echo fb_get_option('fb_enable_approval') ? 'checked="checked"' : ''; ?> />
									<label for="enable_approval"><?php _e('Enable comment auto-approval', 'fbconnect') ?></label>
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Comment Form:', 'fbconnect') ?></th>
							<td>
								<p><input type="checkbox" name="enable_commentform" id="enable_commentform" <?php
								if( fb_get_option('fb_enable_commentform') ) echo 'checked="checked"'
								?> />
									<label for="enable_commentform"><?php _e('Allow send user comments to Facebook.', 'fbconnect') ?></label></p>

							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Show comments login:', 'fbconnect') ?></th>
							<td>
								<p><input type="checkbox" name="fb_connect_comments_login" id="fb_connect_comments_login" <?php
								if( fb_get_option('fb_connect_comments_login') ) echo 'checked="checked"'
								?> />
									<label for="fb_connect_comments_login"><?php _e('Show Facebook login button at comments.', 'fbconnect') ?></label></p>

							</td>
						</tr>
							<tr valign="top">
								<th width="30%" scope="row"><label for="fb_comments_logo"><?php _e('Default image:', 'fbconnect') ?></label></th>
								<td width="70%">
								<input type="text" size="62" name="fb_comments_logo" id="fb_comments_logo" value="<?php echo fb_get_option('fb_comments_logo'); ?>"/> 
								</td>
							</tr>
							<tr valign="top">
								<th width="30%" scope="row"><label for="fb_short_stories_title"><?php _e('Caption:', 'fbconnect') ?></label></th>
								<td width="70%">
								<textarea rows="3" cols="50" name="fb_short_stories_title" id="fb_short_stories_title"><?php echo htmlspecialchars(stripslashes($fb_short_stories_title));?></textarea> <br/>
								</td>
							</tr>
							<tr valign="top">
								<th width="30%"  scope="row"><label for="fb_short_stories_body"><?php _e('Description:', 'fbconnect') ?></label></th>
								<td width="70%">
								<textarea rows="3" cols="50" name="fb_short_stories_body" id="fb_short_stories_body"><?php echo htmlspecialchars(stripslashes($fb_short_stories_body));?></textarea> 
								</td>
							</tr>
							<tr valign="top">
							<th scope="row"><label for="fb_add_main_image"><?php _e('Add main image selector:', 'fbconnect') ?></label></th>
							<td>
								<p><input type="checkbox" name="fb_add_main_image" id="fb_add_main_image" <?php 
									echo fb_get_option('fb_add_main_image') ? 'checked="checked"' : ''; ?> />
									<label for="fb_add_main_image"><?php _e('Allow to select a default image for each post/page', 'fbconnect') ?></label>
							</td>
							</tr>
								<tr valign="top">
							<th scope="row"><label for="fb_add_wpmain_image"><?php _e('Add Wordpress 2.9 post Thumbnails:', 'fbconnect') ?></label></th>
							<td>
								<p><input type="checkbox" name="fb_add_wpmain_image" id="fb_add_wpmain_image" <?php 
									echo fb_get_option('fb_add_wpmain_image') ? 'checked="checked"' : ''; ?> />
									<label for="fb_add_main_image"><?php _e('Allow to select a default image for each post/page', 'fbconnect') ?></label>
							</td>
							</tr>
						
							<tr valign="top">
							<th scope="row"><label for="fb_show_fbcomments"><?php _e('Show Facebook Comments:', 'fbconnect') ?></label></th>
							<td>
								<p><input type="checkbox" name="fb_show_fbcomments" id="fb_show_fbcomments" <?php 
									echo fb_get_option('fb_show_fbcomments') ? 'checked="checked"' : ''; ?> />
									<label for="fb_show_fbcomments"><?php _e('Show Facebook comments widgets', 'fbconnect') ?></label>
							</td>
							</tr>
							<tr valign="top">
								<th width="30%" scope="row"><label for="fb_fbcomments_width"><?php _e('FB comments width:', 'fbconnect') ?></label></th>
								<td width="70%">
								<input type="text" size="62" name="fb_fbcomments_width" id="fb_fbcomments_width" value="<?php echo fb_get_option('fb_fbcomments_width'); ?>"/> 
								</td>
							</tr>
							<tr valign="top">
								<th width="30%" scope="row"><label for="fb_fbcomments_canvas_width"><?php _e('FB comments canvas width:', 'fbconnect') ?></label></th>
								<td width="70%">
								<input type="text" size="62" name="fb_fbcomments_canvas_width" id="fb_fbcomments_canvas_width" value="<?php echo fb_get_option('fb_fbcomments_canvas_width'); ?>"/> 
								</td>
							</tr>
							<tr valign="top">
								<th width="30%" scope="row"><label for="fb_fbcomments_numposts"><?php _e('FB comments num posts:', 'fbconnect') ?></label></th>
								<td width="70%">
								<input type="text" size="62" name="fb_fbcomments_numposts" id="fb_fbcomments_numposts" value="<?php echo fb_get_option('fb_fbcomments_numposts'); ?>"/> 
								</td>
							</tr>
							<tr valign="top">
							<th scope="row"><label for="fb_fbcomments_home"><?php _e('FB comments in home:', 'fbconnect') ?></label></th>
							<td>
								<p><input type="checkbox" name="fb_fbcomments_home" id="fb_fbcomments_home" <?php 
									echo fb_get_option('fb_fbcomments_home') ? 'checked="checked"' : ''; ?> />
							</td>
							</tr>
							<tr valign="top">
								<th width="30%" scope="row"><label for="fb_fbcomments_home_numposts"><?php _e('FB comments home num posts:', 'fbconnect') ?></label></th>
								<td width="70%">
								<input type="text" size="62" name="fb_fbcomments_home_numposts" id="fb_fbcomments_home_numposts" value="<?php echo fb_get_option('fb_fbcomments_home_numposts'); ?>"/> 
								</td>
							</tr>
							<tr valign="top">
							<th scope="row"><label for="fb_hide_wpcomments"><?php _e('Hide Wordpress Comments:', 'fbconnect') ?></label></th>
							<td>
								<p><input type="checkbox" name="fb_hide_wpcomments" id="fb_hide_wpcomments" <?php 
									echo fb_get_option('fb_hide_wpcomments') ? 'checked="checked"' : ''; ?> />
							</td>
							</tr>
							<tr valign="top">
							<th scope="row"><label for="fb_fbcomments_color"><?php _e('FB Comments color:', 'fbconnect') ?></label></th>
							<td>
								<select name="fb_fbcomments_color" id="fb_fbcomments_color">
								<option value="" <?php if( fb_get_option('fb_fbcomments_color')=="") echo 'selected="selected"'; ?>>Light</option>
								<option value="dark" <?php if( fb_get_option('fb_fbcomments_color')=='dark') echo 'selected="selected"'; ?>>Dark</option>
								</select>
								
							</td>
							</tr>
							<tr valign="top">
							<th scope="row"><label for="fb_fbcomments_usexid"><?php _e('FB Comments id:', 'fbconnect') ?></label></th>
							<td>
								<select name="fb_fbcomments_usexid" id="fb_fbcomments_usexid">
								<option value="xid" <?php if( fb_get_option('fb_fbcomments_usexid')=="" || fb_get_option('fb_fbcomments_usexid')=="xid") echo 'selected="selected"'; ?>>Use Wordpress postID as facebook comments XID</option>
								<option value="href" <?php if( fb_get_option('fb_fbcomments_usexid')=='href') echo 'selected="selected"'; ?>>Use post URL as href for facebook comments</option>
								</select>
								 <br/>(To use full Facebook comments features you need to select "post URL as href". If you change this option, old comments will be lost.)
							</td>
							</tr>
	     				</table>

						<?php wp_nonce_field('wp-fbconnect-info_update'); ?>
	     				<p class="submit"><input class="button-primary" type="submit" name="template_update" value="<?php _e('Update Configuration', 'fbconnect') ?> &raquo;" /></p>
	     			</form>
			</div>
