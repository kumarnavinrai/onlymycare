<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 05/10/2008
 * @license: GPLv2
 */

include_once FBCONNECT_PLUGIN_PATH.'/fbConfig.php';
global $wp_version, $fbconnect,$fb_reg_formfields;

			if (isset($_POST['og_update'])){
				check_admin_referer('wp-fbconnect-info_update');

				$error = '';
				update_option('fb_disable_ogmetas',$_POST['fb_disable_ogmetas']);
				update_option('fb_comments_logo',$_POST['fb_comments_logo']);
				update_option( 'fb_og_type', $_POST['_fbconnect_og_type'] );	
				update_option( 'fb_admins', $_POST['fb_admins'] );	
				update_option( 'fb_og_latitude', $_POST['fb_og_latitude'] );
				update_option( 'fb_og_longitude', $_POST['fb_og_longitude'] );		
				update_option( 'fb_og_street_address', $_POST['fb_og_street_address'] );	
				update_option( 'fb_og_locality', $_POST['fb_og_locality'] );	
				update_option( 'fb_og_region', $_POST['fb_og_region'] );	
				update_option( 'fb_og_postal_code', $_POST['fb_og_postal_code'] );	
				update_option( 'fb_og_country_name', $_POST['fb_og_country_name'] );																	
				update_option('fb_fanpage_id',$_POST['fb_fanpage_id']);
				update_option('fb_fanpage_name',$_POST['fb_fanpage_name']);
				update_option('fb_fanpage_url',$_POST['fb_fanpage_url']);
				update_option('fb_og_namespace',$_POST['fb_og_namespace']);
				update_option('fb_og_posttype',$_POST['fb_og_posttype']);
				
				
			}
			
			// Display the options page form
			$siteurl = fb_get_option('home');
			if( substr( $siteurl, -1, 1 ) !== '/' ) $siteurl .= '/';
			?>
			<div class="wrap">
				<h2><?php _e('Facebook Configuration', 'fbconnect') ?> : <?php _e('Open Graph Options', 'fbconnect') ?></h2>

				<form method="post">

					<h3><?php _e('Open Graph MetaData', 'fbconnect') ?></h3>
	<?php
							
						$fb_user = fb_get_loggedin_user();

							?>
						<script type='text/javascript'>
							function callbackSelectPage(pageid,pagename,pageurl){
								jQuery("#fb_fanpage_id").attr("value",pageid);
								jQuery("#fb_fanpage_name").attr("value",pagename);
								jQuery("#fb_fanpage_url").attr("value",pageurl);
								tb_remove();
								jQuery("#pagepic").html('<fb:profile-pic uid="'+pageid+'" linked="true" />');
								FB.XFBML.parse();
							}
							
							function selectFBPage(){
									tb_show("Select Page", "<?php echo fb_get_option('siteurl'); ?>?fbconnect_action=userpages&height=450&width=630&callback=callbackSelectPage", "");
							}
						</script>
	     				<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
	     					<tr valign="top">
								<th width="30%" scope="row"><label for="fb_disable_ogmetas"><?php _e('Disable OpenGraph metatags:', 'fbconnect') ?></label></th>
								<td width="70%">
								<input type="checkbox" name="fb_disable_ogmetas" id="fb_disable_ogmetas" <?php
								if( fb_get_option('fb_disable_ogmetas') ) echo 'checked="checked"'
								?> />
								</td>
							</tr>
	     					<tr valign="top">
								<th width="30%" scope="row"><label for="fb_og_type"><?php _e('OpenGraph type:', 'fbconnect') ?></label></th>
								<td width="70%">
									<div style="width:380px;">
								<?php WPfbConnect_Interface::print_ogtypes_select(fb_get_option('fb_og_type')); ?>
								</div>
								</td>
							</tr>
							<tr valign="top">
								<th width="30%" scope="row"><label for="fb_og_namespace"><?php _e('OpenGraph namespace:', 'fbconnect') ?></label></th>
								<td width="70%">
								<input type="text" name="fb_og_namespace" id="fb_og_namespace" value="<?php echo fb_get_option('fb_og_namespace'); ?>" size="25" />
								</td>
							</tr>
							<tr valign="top">
								<th width="30%" scope="row"><label for="fb_og_posttype"><?php _e('Default OpenGraph post type:', 'fbconnect') ?></label></th>
								<td width="70%">
								<input type="text" name="fb_og_posttype" id="fb_og_posttype" value="<?php echo fb_get_option('fb_og_posttype'); ?>" size="25" />
								</td>
							</tr>
							<tr valign="top">
								<th width="30%" scope="row"><label for="fb_admins"><?php _e('Facebook admin IDs:', 'fbconnect') ?></label></th>
								<td width="70%">
								<input type="text" name="fb_admins" id="fb_admins" value="<?php echo fb_get_option('fb_admins'); ?>" size="25" /> (Your ID: <?php echo $fb_user;?>)  		
								</td>
							</tr>
							<tr valign="top">
								<th style="width: 33%" scope="row"><label for="fb_fanpage_id"><?php _e('Facebook page:', 'fbconnect') ?></label></th>
								<td>
								<div style="display:block;overflow:hidden;">
								<div style="float:left;width:200px;">
								<label for="fb_fanpage_id"><?php _e('ID:', 'fbconnect') ?></label><br/>
								<input type="text" size="25" name="fb_fanpage_id" id="fb_fanpage_id" value="<?php echo fb_get_option('fb_fanpage_id'); ?>"/><br/>
								</div>
								<div id="pagepic" style="float:left;width:80px;">
								<fb:profile-pic width="40" height="40" uid="<?php echo fb_get_option('fb_fanpage_id'); ?>" linked="true" />
								</div>
								</div>
								<label for="fb_fanpage_name"><?php _e('Name:', 'fbconnect') ?></label><br/>
								<input type="text" size="50" name="fb_fanpage_name" id="fb_fanpage_name" value="<?php echo fb_get_option('fb_fanpage_name'); ?>"/><br/>
								<label for="fb_fanpage_url"><?php _e('URL:', 'fbconnect') ?></label><br/>
								<input type="text" size="50" name="fb_fanpage_url" id="fb_fanpage_url" value="<?php echo fb_get_option('fb_fanpage_url'); ?>"/>
								<br/>
								<span class="submit"><input class="button" type="button" onclick="selectFBPage();" name="selectPage" value="<?php _e('Select page', 'fbconnect') ?> &raquo;" /> <a class="button" target="_blank" href="http://www.facebook.com/add.php?api_key=<?php echo get_appId();?>&pages=1&page=<?php echo fb_get_option('fb_fanpage_id'); ?>"><?php _e('Add tab to my page', 'fbconnect') ?> &raquo;</a></span>
								 
						
								</td>
						</tr>
							<tr valign="top">
								<th width="30%" scope="row"><label for="fb_comments_logo"><?php _e('Default image:', 'fbconnect') ?></label></th>
								<td width="70%">
								<input type="text" size="62" name="fb_comments_logo" id="fb_comments_logo" value="<?php echo fb_get_option('fb_comments_logo'); ?>"/> 
								</td>
							</tr>
							<tr valign="top">
								<th width="30%" scope="row"><label for="fb_og_latitude "><?php _e('Latitude:', 'fbconnect') ?></label></th>
								<td width="70%">
								<input type="text" size="62" name="fb_og_latitude" id="fb_og_latitude" value="<?php echo fb_get_option('fb_og_latitude'); ?>"/> 
								</td>
							</tr>
							<tr valign="top">
								<th width="30%" scope="row"><label for="fb_og_longitude"><?php _e('Longitude:', 'fbconnect') ?></label></th>
								<td width="70%">
								<input type="text" size="62" name="fb_og_longitude" id="fb_og_longitude" value="<?php echo fb_get_option('fb_og_longitude'); ?>"/> 
								</td>
							</tr>
							<tr valign="top">
								<th width="30%" scope="row"><label for="fb_og_street_address"><?php _e('Street address:', 'fbconnect') ?></label></th>
								<td width="70%">
								<input type="text" size="62" name="fb_og_street_address" id="fb_og_street_address" value="<?php echo fb_get_option('fb_og_street_address'); ?>"/> 
								</td>
							</tr>
							<tr valign="top">
								<th width="30%" scope="row"><label for="fb_og_locality"><?php _e('Locality:', 'fbconnect') ?></label></th>
								<td width="70%">
								<input type="text" size="62" name="fb_og_locality" id="fb_og_locality" value="<?php echo fb_get_option('fb_og_locality'); ?>"/> 
								</td>
							</tr>
							<tr valign="top">
								<th width="30%" scope="row"><label for="fb_og_region"><?php _e('Region:', 'fbconnect') ?></label></th>
								<td width="70%">
								<input type="text" size="62" name="fb_og_region" id="fb_og_region" value="<?php echo fb_get_option('fb_og_region'); ?>"/> 
								</td>
							</tr>
							<tr valign="top">
								<th width="30%" scope="row"><label for="fb_og_postal_code"><?php _e('Postal code:', 'fbconnect') ?></label></th>
								<td width="70%">
								<input type="text" size="62" name="fb_og_postal_code" id="fb_og_postal_code" value="<?php echo fb_get_option('fb_og_postal_code'); ?>"/> 
								</td>
							</tr>
							<tr valign="top">
								<th width="30%" scope="row"><label for="fb_og_country_name"><?php _e('Country name:', 'fbconnect') ?></label></th>
								<td width="70%">
								<input type="text" size="62" name="fb_og_country_name" id="fb_og_country_name" value="<?php echo fb_get_option('fb_og_country_name'); ?>"/> 
								</td>
							</tr>

	     				</table>

						<?php wp_nonce_field('wp-fbconnect-info_update'); ?>
	     				<p class="submit"><input class="button-primary" type="submit" name="og_update" value="<?php _e('Update Configuration', 'fbconnect') ?> &raquo;" /></p>
	     			</form>
			</div>
