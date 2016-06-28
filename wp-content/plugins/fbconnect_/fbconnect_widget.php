<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 23/12/2008
 * @license: GPLv2
 */
if (isset($_REQUEST["maxlastusers"])){
	$maxlastusers = $_REQUEST["maxlastusers"];
}
if (isset($_REQUEST["avatarsize"])){
	$avatarsize = $_REQUEST["avatarsize"];
}
if (!isset($maxlastusers) || $maxlastusers==""){
	$maxlastusers = 10;
}
if (!isset($themecolor) || $themecolor==""){
	$themecolor = "fbthemelight";
}
$fb_user = fb_get_loggedin_user();

$user = wp_get_current_user();

$users = WPfbConnect_Logic::get_lastusers_fbconnect($maxlastusers);
$siteurl = fb_get_option('siteurl');

if (!isset($_GET['fbajaxlogin']) && !isset($_GET['fbajaxlogout'])){
?>

<div id="fbconnect_widget_div" class="fbconnect_widget_divclass <?php echo $themecolor;?>">

<?php
}
	$hidefacepile=false;
/*	global $fbforzelogin;
	if (!isset($_GET['fbajaxlogin']) && !isset($_GET['fbajaxlogout'])){
		$fbforzelogin = true;
	}else{
		$fbforzelogin = false;
	}*/
	include(FBCONNECT_PLUGIN_PATH."/fbconnect_widget_login.php");

	if ($avatarsize==""){
		$avatarsize = 35;
	}
?> 	
<div class="fbTabs">
        <ul class="tabNavigation">
            <li><a id="fbFirstA" class="fbtablink selected" href="#fbFirst" onclick="fb_showTab('fbFirst');return false;"><?php _e('Visitors', 'fbconnect'); ?></a></li>
            <li><a id="fbSecondA" class="fbtablink" href="#fbSecond" onclick="fb_showTab('fbSecond');return false;"><?php _e('Friends', 'fbconnect'); ?></a></li>
			<li><a id="fbThirdA" class="fbtablink" href="#fbThird" onclick="fb_showTab('fbThird');return false;"><?php _e('Comments', 'fbconnect'); ?></a></li>
        </ul>

	<div id="fbFirst" class="fbtabdiv" >
	<div class="fbconnect_LastUsers">
	<div class="fbconnect_userpics">
	
<?php
	if(fb_get_option('fb_connect_use_thick')){
		$addthickbox= "thickbox";
	}else{
		$addthickbox= "";			
	}
	
	foreach($users as $last_user){
		if (isset($user) && $user->ID!=0 && $last_user->ID==$user->ID){
			echo '<a title="'.__("User profile","fbconnect").'" class="'.$addthickbox.'" href="'.WPfbConnect_Logic::get_user_socialurl($user->fbconnect_userid,$user->fbconnect_netid,$user->ID).'">';
			$cierrelink = "</a>";
		}else{
			$cierrelink = "";
		}
		echo get_avatar( $last_user,$avatarsize );
		echo $cierrelink;
	}
	
?>
	</div>
	<div class="fbwidgetfooter">
<?php 
	echo '<a class="'.$addthickbox.'" title="'.__("Community","fbconnect").'" class="thickbox" href="'.$siteurl.'/?fbconnect_action=community&amp;height=400&amp;width=450">'.__('view more...', 'fbconnect').' </a>';
?>
	</div>
	</div>
	</div>
		
	<div id="fbSecond" class="fbtabdiv">
	<div class="fbconnect_LastUsers">
<?php 	
	if(isset($user) && $user->ID!="0"){
	//if(isset($fb_user) && $fb_user!=""){
		$friends = WPfbConnect_Logic::get_friends($user->ID,0,$maxlastusers);
		if(count($friends)>0){
			echo '<div class="fbconnect_userpics">';
			foreach($friends as $user){
						echo get_avatar( $user,$avatarsize );
			}
		}else{
			echo '<div>';
			_e("You don't have friends on this site", 'fbconnect');
			echo ': <b><a href="'.$siteurl.'/?fbconnect_action=invite">'.__('Invite your friends!', 'fbconnect').'</a> </b> ';
		}
	}else{
		echo '<div>';
		_e("To see your friends on this site, you must be logged in", 'fbconnect');
	}
	?>
	</div>
	
	<div class="fbwidgetfooter"><a href="<?php echo $siteurl.'/?fbconnect_action=community';?>"><?php _e('view more...', 'fbconnect')?></a></div>
	</div>
	</div>

	<div id="fbThird" class="fbtabdiv">
	<div id="fbconnect_feedhead">
	<div class="fbTabs_feed">
	        <ul class="tabNavigation_feed">
<?php 	      
	if(isset($fb_user) && $fb_user!=""){
		echo '<li><a id="fbAllFriendsCommentsA" href="#fbAllFriendsComments" onclick="fb_showComments(\'fbAllFriendsComments\');return false;">'.__('Friends', 'fbconnect').'</a> </li>';
		echo '<li><a id="fbAllCommentsA" class="selected" href="#fbAllComments" onclick="fb_showComments(\'fbAllComments\');return false;">'.__('Full site', 'fbconnect').'</a></li>';	
	}
?>
	</ul>
	</div>
	</div>

	<div id="fbAllComments" class="fbconnect_LastComments">
<?php 
	global $fbconnect_filter;
	$fbconnect_filter="fbAllComments";
	global $showPostTitle;
	$showPostTitle = true;
	include( FBCONNECT_PLUGIN_PATH.'/fbconnect_feed.php');
?>
	</div>
<?php 
	if(isset($fb_user) && $fb_user!=""){
		echo '<div id="fbAllFriendsComments" style="display:none;visibility:hidden;" class="fbconnect_LastComments">';
		$fbconnect_filter="fbAllFriendsComments";
		include( FBCONNECT_PLUGIN_PATH.'/fbconnect_feed.php');
		echo '</div>';
	}
?>
	</div>
</div>
<?php 

if (!isset($_GET['fbajaxlogin']) && !isset($_GET['fbajaxlogout'])){
?> 
</div>
<div class="fbcreditos"><?php _e('Powered by', 'fbconnect'); ?> <a href="http://www.sociable.es">Sociable!</a></div>
<?php 
}
 ?>
<script>
fb_showTab('fbFirst');
</script>