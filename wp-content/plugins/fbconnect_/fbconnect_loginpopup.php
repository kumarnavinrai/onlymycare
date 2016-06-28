<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 23/12/2008
 * @license: GPLv2
 */
error_reporting(E_ALL & ~E_DEPRECATED);

if (! defined('FBCONNECT_PLUGIN_PATH'))
	require_once("../../../wp-config.php");

$user = wp_get_current_user();
$fb_user = fb_get_loggedin_user();
?> 
<script>
fb_errormsgcontainer = "#errormsgcontainerthick";
</script>
	<div id="fbloginpopup">
		<div id="errormsgcontainerthick" class="errormsgcontainer"></div>
			<?php 
		$loginbutton = "xlarge";
		$fb_hide_edit_profile = true;
		$fb_hide_invite_link = true;
		$fb_breaklinesprofile = "on";
		$fb_showwplogin="on";
		$fblogintext = __("Log in with your social platform", 'fbconnect');
		$fbloginwordpresstext = __("Log In with your user/password", 'fbconnect');
		$fb_mainavatarsize = 30;
		if(isset($_REQUEST["loginreload"]) && $_REQUEST["loginreload"]=="true"){
			$fb_loginreload = true;
		}elseif(isset($_REQUEST["loginreload"]) && $_REQUEST["loginreload"]=="false"){
			$fb_loginreload = false;
		}

		include FBCONNECT_PLUGIN_PATH."/fbconnect_widget_login.php";
		?>
		
	</div>
