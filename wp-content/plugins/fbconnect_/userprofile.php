<?php
	global $userprofile;
	$userprofile = WPfbConnect_Logic::get_user();
	$sizeimg="small";
	if(fb_get_option('fb_connect_use_thick')){
			//$sizeimg="square";
			$style='fbthickprofile';
	}
?>	
	
<div class="fbconnect_userprofile <?php echo $style;?>">
	<div class="alignleft fbconnect_userpicmain">
		<?php
		    $fbuser = $userprofile->fbconnect_userid;
			$netid = $userprofile->fbconnect_netid;
			$size = 100;
			if ($netid=="twitter"){
				echo "<img src=\"http://api.twitter.com/1/users/profile_image/".$fbuser."?size=bigger\" class=\"avatar photo avatar-$size\" width=\"$size\" />";
			}elseif ($netid=="google"){
				echo "<img src=\"http://profiles.google.com/s2/photos/profile/$fbuser?sz=$size\" class=\"avatar photo avatar-$size\" width=\"$size\" />";
			}elseif($netid=="facebook"){
				echo "<img class=\"avatar photo avatar-".$size."\"  width=\"".$size."\" src=\"http://graph.facebook.com/".$fbuser."/picture?type=normal\" />";
			}else{
				echo get_avatar($userprofile->ID,100);
			}
		?>
	</div>

		<b><?php _e('Name:', 'fbconnect') ?> </b><?php echo $userprofile->display_name; ?>
<!--		<br/><b><?php _e('Nickname:', 'fbconnect') ?> </b><?php echo $userprofile->nickname; ?> -->
		<br/><b><?php _e('Member since:', 'fbconnect') ?> </b><?php echo $userprofile->user_registered; ?>
		<br/><b><?php _e('Website URL:', 'fbconnect') ?> </b><a href="<?php echo $userprofile->user_url; ?>" rel="external nofollow"><?php echo $userprofile->user_url; ?></a>
		<br/><b><?php _e('About me:', 'fbconnect') ?> </b><?php echo $userprofile->description; ?><br/>
		<?php if (isset($userprofile->fbconnect_userid) && $userprofile->fbconnect_userid!="" && $userprofile->fbconnect_userid!="0" && $userprofile->fbconnect_netid=="facebook") : ?>
			<br/><b><a target="_blank" href="http://www.facebook.com/profile.php?id=<?php echo $userprofile->fbconnect_userid; ?>"><img class="icon-text-middle" src="<?php echo FBCONNECT_PLUGIN_URL; ?>/images/facebook_24.png"/><?php _e('Facebook profile', 'fbconnect') ?> </a></b>
			<a href="#" onclick="FB.Connect.showAddFriendDialog(<?php echo $userprofile->fbconnect_userid; ?>, null);">[ Add as friend ]</a>
			<?php 
			$fb_user = fb_get_loggedin_user();
			if ($fb_user!="" && $userprofile->fbconnect_userid==$fb_user) : ?>
				<br/><br/><b><a target="_blank" href="<?php echo fb_get_option('siteurl')."/?fbconnect_action=delete_user"; ?>">[delete profile]</a></b>
			<?php endif; ?>
		<?php endif; ?>
	
</div>
