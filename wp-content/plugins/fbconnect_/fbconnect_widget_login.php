<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 23/12/2008
 * @license: GPLv2
 */

?> 
<div id="fbconnect_widget_proflogin" class="fb_widget_proflogin">	
<?php
global $fbforzelogin;
//print_r($_REQUEST);
$fb_user = fb_get_loggedin_user();
$user = wp_get_current_user();
$siteurl = fb_get_option('siteurl');
	
$pageurl = $siteurl."/";
if (!is_home()){
	global $post;
	if ($post!=""){
		$pageurl = get_permalink($post->ID);
	}
}

if ($loginbutton != "small" && $loginbutton != "medium" && $loginbutton != "large" && $loginbutton != "xlarge"){
	$loginbutton = "medium";
}

if($fb_mainavatarsize==""){
	$fb_mainavatarsize = 50;
}

global $fb_showwplogin;
if($fb_showwplogin==""){
	$fb_showwplogin = "off";
}

if($hidelogintextwp==""){
	$hidelogintextwp = "off";
}

if (!isset($fb_hide_edit_profile)){
	$fb_hide_edit_profile = fb_get_option('fb_hide_edit_profile');
}

if ($fb_breaklinesprofile=="off"){
	$brlines=" ";
}else{
	$brlines="<br/> ";
}

if( fb_get_option('fb_connect_use_thick')){
	$classthick = "thickbox";
}
	
	echo '<div class="fbconnect_miniprofile">';
	if ( (($fb_user && $user->ID!=0) || ($user->ID && $user->ID!="" && $user->ID!=0))) {
		$userid = "";
		$user_name= "";
		$user_email = "";
		if ($user!="" && $user->ID!="0"){
			$userid = $user->ID;
			$user_name = $user->first_name;
			$user_email = $user->user_email;
		}
		?>
		
		<script type='text/javascript'> 
		fb_init_user("<?php echo $userid;?>","<?php echo $user_name;?>","<?php echo $user_email;?>");	
		</script>
		
		<?php
		echo $welcometext;
		echo '<div style="margin:2px;clear:both;"></div>';
		echo '<div class="fbconnect_userpicmain_cont">';
		echo '<div class="fbconnect_userpicmain">'.get_avatar( $user->ID,$fb_mainavatarsize ).'</div>';
		echo '</div>';
		$linked = fb_get_option('fb_connect_avatar_link');

		if ($linked=="on"){
				echo '<a title="'.__('User profile', 'fbconnect').'" id="fb_userprofilename" href="http://www.facebook.com/profile.php?id='.$fb_user.'">'.$user->first_name.'</a>'.$brlines;
		}else{
			if( fb_get_option('fb_connect_use_thick')){
				echo '<a title="'.__('User profile', 'fbconnect').'" id="fb_userprofilename" class="'.$classthick.'" href="'.$siteurl.'/?fbconnect_action=myhome&amp;userid='.$user->ID.'&height='.FBCONNECT_TICKHEIGHT.'&width='.FBCONNECT_TICKWIDTH.'">'.$user->first_name.'</a>'.$brlines;
			}else{
				echo '<a title="'.__('User profile', 'fbconnect').'" id="fb_userprofilename" href="'.$siteurl.'/?fbconnect_action=myhome&amp;userid='.$user->ID.'">'.$user->first_name.'</a>'.$brlines;
			}
		}
		if (!$fb_hide_edit_profile && FBCONNECT_CANVAS=="web"){
			if(fb_get_option('fb_custom_reg_form') && fb_get_option('fb_custom_reg_form')!=""){
				echo '<a title="'.__('User profile', 'fbconnect').'" class="fb_userprofilelinks" href="'.$siteurl.fb_get_option('fb_custom_reg_form').'">'.__('Edit profile', 'fbconnect').'</a>'.$brlines;
			}elseif(fb_get_option('fb_show_reg_form')){
				echo '<a title="'.__('User profile', 'fbconnect').'" class="fb_userprofilelinks '.$classthick.'" href="'.$siteurl.'/?fbconnect_action=register&width=450">'.__('Edit profile', 'fbconnect').'</a>'.$brlines;
			}else{
				echo '<a title="'.__('User profile', 'fbconnect').'" class="fb_userprofilelinks" href="'.$siteurl.'/wp-admin/profile.php">'.__('Edit profile', 'fbconnect').'</a>'.$brlines;
			}
		}
		
		if ( !$fb_hide_invite_link && $fb_user && FBCONNECT_CANVAS=="web"){
			//echo '<a href="'.$siteurl.'/?fbconnect_action=invite">[ '.__('Invite', 'fbconnect').' ]</a>'.$brlines;
			$requestfriends = "fbInviteFriends('".fb_get_option('blogname')." : ".fb_get_option('blogdescription')."')";
			echo '<a class="fb_userprofilelinks" href="#" onclick="'.$requestfriends.'">'.__('Invite', 'fbconnect').'</a>'.$brlines;
		}
		//if ( FBCONNECT_CANVAS=="web"){
			echo '<a class="fb_userprofilelinks" id="fb_userprofilelogout" href="#" onclick="logout_facebook();return false;">'.__('Logout', 'fbconnect').'</a>'.$brlines;
		//echo '<br/><a href="#" onclick="FB.logout(function(result) { window.location = \''.$siteurl.'/?fbconnect_action=logout'.'\'; });return false;">[ '.__('Logout', 'fbconnect').' ]</a>';	
		//echo '<div style="text-align:center;"><a onclick="FB.Connect.showBookmarkDialog()"><img src="'.FBCONNECT_PLUGIN_URL.'/images/Bookmark.png"/></a></div>';
		//}
	}
	echo '</div>';
    if (!isset($fb_nolinksocialprofiles)){
    	$fb_nolinksocialprofiles = true;
    }
	if (!$fbforzelogin && $user!="" && $user->ID && ($fb_nolinksocialprofiles || ($user->fbconnect_userid!="" && $user->fbconnect_userid!="0"))){

	}else{
		echo "<div class=\"fbsocialloginbuttons\">";
		
		if(!isset($hidelogintext) && !$hidelogintext){
			if ($fblogintext==""){
				$fblogintext = __('Login with:', 'fbconnect');;
			}
			echo '<span class="fb_loginsocialtext fb_headtext">';
			echo __($fblogintext, 'fbconnect');	
			echo '</span>'.$brlines;
		}
		/*if ( fb_get_option('fb_show_reg_form') && fb_get_option('fb_show_reg_form')!=""){
			echo "<fb:login-button size=\"medium\" length=\"".$loginbutton."\" onlogin=\"javascript:login_facebook2();\" ></fb:login-button>\n";
		}else{*/
		$requestperms="";	
		if (fb_get_option('fb_permsToRequestOnConnect')!=""){
				$requestperms = 'scope="'.fb_get_option('fb_permsToRequestOnConnect').'"';
		}
		
		if (fb_get_option('fb_loginreload')){
			$fb_loginreloadtxt="true";
		}else{
			$fb_loginreloadtxt="false";
		}

		if(isset($fb_loginreload) && $fb_loginreload){
			$fb_loginreloadtxt="true";
		}elseif(isset($fb_loginreload)){
			$fb_loginreloadtxt="false";	
		}
		
		$authUrl = $siteurl.'/?fbconnect_action=redirectoauthgoogle';
	
		$tauthUrl = $siteurl.'/?fbconnect_action=redirectoauthtwitter';
		if (fb_get_option('fb_facebooklogin_enabled')!=""){
			if ($fbloginbuttontext==""){
				$fbloginbuttontext = 'Facebook';
			}
		?>
		<script>
			var fb_loginreload = "<?php echo $fb_loginreloadtxt;?>";
		</script>
		<span class="fbloginbuttoncontainer fb_facebookloggincontainer">
		<a class="fb_login_button fb_login_button_<?php echo $loginbutton;?>" onclick="login_facebook2(<?php echo $fb_loginreloadtxt;?>)">
		<span class="fb_login_button_text"><?php echo $fbloginbuttontext;?></span>
		</a>
		</span>
		<?php 
		}
		if (fb_get_option('g_login_enabled')!=""){?>
		<span class="fbloginbuttoncontainer fb_googleloggincontainer">	
		<a class="gp_login_button gp_login_button_<?php echo $loginbutton;?>"  onclick="fb_windowopen('<?php echo $authUrl;?>','GoogleLogin');return false;" href="#">
		<span class="gp_login_button_text">Google</span>
		</a>
		</span>
		<?php 
		}
		if (fb_get_option('t_login_enabled')!=""){
		?>
		<span class="fbloginbuttoncontainer fb_twitterloggincontainer">
		<a class="tw_login_button tw_login_button_<?php echo $loginbutton;?>"  onclick="fb_windowopen('<?php echo $tauthUrl;?>','TwitterLogin');return false;" href="#">
		<span class="tw_login_button_text">Twitter</span>
		</a>
		</span>
		<?php 
		}
		?>
		</div>
		<?php
		if($fb_showwplogin=="on"){
		?>
		<div class="fbloginwordpressform">
		<?php 
		$fbloginwordpresstext = apply_filters('fb_loginheadtext', $fbloginwordpresstext);
		if($fbloginwordpresstext!=""){?>
		<span class="fb_loginwordpresstext fb_headtext">
			<?php echo __($fbloginwordpresstext, 'fbconnect');?>
		</span>
		<?php }
		if ($fbloginlabel==""){
			$fbloginlabel = __("Your username", 'fbconnect');
			$fbloginlabel = apply_filters('fb_loginlabel', $fbloginlabel);
		}
		?>
		<form method="post" action="<?php echo $siteurl;?>/wp-login.php" id="fb_loginform" name="fb_loginform" class="fbloginform workspaceform">
			<span id="fbuserloginfield" class="fbformfield">
				<label for="log"><?php echo $fbloginlabel;?></label>
				<input type="text" placeholder="<?php echo $fbloginlabel;?>" tabindex="10" size="10" value="" class="input inputsmall " id="log" name="log"/>
			</span>
			<span id="fbuserpasswordfield" class="fbformfield">
				<label for="pwd"><?php _e("Password", 'fbconnect');?></label>
				<input type="password" placeholder="<?php _e("Password", 'fbconnect');?>" tabindex="20" size="10" value="" class="input inputsmall" id="pwd" name="pwd"/>
			</span>
		
						
			<span class="submit fbformfield">
				<input type="submit" onclick="fb_refreshloginwp(true);return false;" tabindex="100" value="<?php _e("Log In", 'fbconnect');?>" class="button-primary submit wsbuttonSecondary" id="wp-submit" name="wp-submit"/>
				<input type="hidden" value="<?php echo $pageurl;?>" name="redirect_to"/>
				<input type="hidden" value="1" name="testcookie"/>
				<input type="hidden" value="<?php echo rand(1,32000);?>" name="randfbparam"/>
				<input type="hidden" value="forever" name="rememberme"/>
			</span> 
		</form>

		<div id="registerlink">
			<span id="fbnotregisteredtext"><?php _e('Not registered?', 'fbconnect');?> </span>
			<?php 
				if(fb_get_option('fb_custom_reg_form') && fb_get_option('fb_custom_reg_form')!=""){
					echo '<a title="'.__('User registration', 'fbconnect').'" class="fb_userprofilelinks" href="'.$siteurl.fb_get_option('fb_custom_reg_form').'">'.__('Register here', 'fbconnect').'</a>'.$brlines;
				}elseif(fb_get_option('fb_show_reg_form')){
					echo '<a title="'.__('User registration', 'fbconnect').'" class="fb_userprofilelinks '.$classthick.'" href="'.$siteurl.'/?fbconnect_action=register&height=390&width=435">'.__('Register here', 'fbconnect').'</a>'.$brlines;
				}else{
					echo '<a title="'.__('User registration', 'fbconnect').'" class="fb_userprofilelinks" href="'.$siteurl.'/wp-admin/profile.php">'.__('Register here', 'fbconnect').'</a>'.$brlines;
				}
			?>
		</div>
		<?php 
		if (fb_get_option('fb_custom_lostpasswd_form')!="" ){?>
		<div id="forgetlink">
			<span id="fbforgettext"><?php _e('Forgot your password?', 'fbconnect');?> </span><a id="fbforgetlink" title="<?php _e('Forgot password', 'fbconnect');?>" href="<?php echo $siteurl.fb_get_option('fb_custom_lostpasswd_form');?>"><?php _e('Click here to reset', 'fbconnect');?></a>
		</div>
		<?php
		} ?>
		</div>
		<?php
		}		
	}
	
?> 
</div>