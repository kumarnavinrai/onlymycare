<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 05/10/2008
 * @license: GPLv2
 */

if (!class_exists('WPgConnect_Logic')):

/**
 * Basic logic for Google.
 */
class WPgConnect_Logic {
public $googleoauthclient;
public $googleplus;

function init(){
	$this->googleoauthclient = new apiClient();
	$this->googleoauthclient->setApplicationName("Sociable Wordpress Plugin");
	$this->googleoauthclient->addService("userprofile", "v1");
    $this->googleoauthclient->addService("useremail", "v1");
	$this->googleplus = new apiPlusService($this->googleoauthclient);	
}	

function user_getInfo(){
	  //$me = $this->googleplus->people->get('me');
	  $token = json_decode($_SESSION['access_token']);
	  $meprofile = WPfbConnect_Logic::get_url_content("https://www.googleapis.com/oauth2/v1/userinfo?access_token=".$token->access_token);
	  $meprofile = json_decode($meprofile);
	  $userinfo = array();
	  $userinfo['uid'] = $meprofile->id;
	  //$userinfo['username'] = $meprofile->email;
	  $userinfo['website'] = $meprofile->link;
	  $userinfo['about_me'] = "";
	  $userinfo['email'] = $meprofile->email;
	  $userinfo['profile_url'] = $meprofile->link;
	  $userinfo['name'] = $meprofile->name;
	  $userinfo['first_name'] = $meprofile->given_name;
	  $userinfo['last_name'] = $meprofile->family_name;
	  $userinfo['sex'] = $meprofile->gender;
	  $userinfo['locale'] = $meprofile->locale;
	  return $userinfo;

}

function createAuthUrl(){
	$authUrl = $this->googleoauthclient->createAuthUrl();
	return $authUrl; 
}


function wp_login() {
		global $wp_version,$new_fb_user;

		if ( isset($_REQUEST["fbconnect_action"]) && ($_REQUEST["fbconnect_action"]=="delete_user" || $_REQUEST["fbconnect_action"]=="postlogout" || $_REQUEST["fbconnect_action"]=="logout")){
			return;
		}
		
		$self = basename( $GLOBALS['pagenow'] );
	
			
		$user = wp_get_current_user();
		if (isset($user) && $user->ID==0){
			$user = "";	
		}
		
		if ( isset($_GET['oauth_login']) && $_GET['oauth_login']=="google" && (!is_user_logged_in() || $user->fbconnect_userid == "" || $user->fbconnect_userid == 0)) { //Intenta hacer login estando registrado en facebook
			if (isset($_GET['code'])) {
			  $this->googleoauthclient->authenticate();
			  $_SESSION['access_token'] = $this->googleoauthclient->getAccessToken();
			  //header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
			}
			if (isset($_SESSION['access_token'])) {
			  $this->googleoauthclient->setAccessToken($_SESSION['access_token']);
			
			
				require_once(ABSPATH . WPINC . '/registration.php');
				$usersinfo = $this->user_getInfo();
				if (!isset($usersinfo) || $usersinfo["uid"]==""){
					WPfbConnect::log("[fbConnectLogic::wp_login_fbconnect] fb_user_getInfo ERROR: ".$fb_user,FBCONNECT_LOG_ERR);
					return;	
				}
				$fb_user = $usersinfo["uid"];
				$_SESSION["facebook_usersinfo"] = $usersinfo;
				$_SESSION["fbconnect_netid"] = "google";

				$wpid = "";
				$fbwpuser = WPfbConnect_Logic::get_userbyFBID($usersinfo["uid"],"google");
				if ($fbwpuser =="" && $usersinfo["email"]!=""){
					echo "busca por email";
					$fbwpuser = WPfbConnect_Logic::get_userbyEmail($usersinfo["email"]);
					print_r($fbwpuser);
				}

				//echo "LEER:".$fb_user;
				//print_r($fbwpuser);
				$wpid = "";
				$new_fb_user= false;
		
				if(is_user_logged_in() && $fbwpuser && $user->ID==$fbwpuser->ID && ($user->fbconnect_userid =="" || $user->fbconnect_userid =="0")){ // Encuentra por email el usuario y no está asociado al de FB
					WPfbConnect_Logic::set_userid_fbconnect($user->ID,$fb_user,"google");
					$wpid = $user->ID;
				}else if(FBCONNECT_CANVAS!="web" && is_user_logged_in() && $fbwpuser && $user->ID != $fbwpuser->ID){ // El usuario FB está asociado a un usaurio WP distinto al logeado
					$wpid = $fbwpuser->ID;
				}elseif(is_user_logged_in() && !$fbwpuser && ($user->fbconnect_userid =="" || $user->fbconnect_userid =="0")){ // El usuario WP no está asociado al de FB
					WPfbConnect_Logic::set_userid_fbconnect($user->ID,$fb_user,"google");
					$wpid = $user->ID;
				}elseif (!is_user_logged_in() && $fbwpuser && ($fbwpuser->fbconnect_userid =="" || $fbwpuser->fbconnect_userid =="0")){
					WPfbConnect_Logic::set_userid_fbconnect($fbwpuser->ID,$fb_user,"google");
					$wpid = $fbwpuser->ID;	
				}elseif(!is_user_logged_in() && $fbwpuser && ($fbwpuser->fbconnect_userid ==$fb_user)){
					$wpid = $fbwpuser->ID;	
				}elseif (($fbwpuser && $fbwpuser->fbconnect_userid != $fb_user) || (!is_user_logged_in() && !$fbwpuser) || (!$fbwpuser && is_user_logged_in() && $user->fbconnect_userid != $fb_user)){
					if(isset($usersinfo) && $usersinfo!=""){
						$username = WPfbConnect_Logic::fbusername_generator($usersinfo["uid"],$usersinfo['username'],$usersinfo["first_name"],$usersinfo["last_name"]);
		
						$user_data = array();
						$user_data['user_login'] = $username;
						
						$user_data['user_pass'] = substr( md5( uniqid( microtime() ).$_SERVER["REMOTE_ADDR"] ), 0, 15);
	
						$user_data['user_nicename'] = $username;
						$user_data['display_name'] = $usersinfo["name"];
	
						$user_data['user_url'] = $usersinfo["profile_url"];
						//$user_data['user_email'] = $usersinfo["proxied_email"];
						$user_data['user_email'] = "";
						
						if ($usersinfo["email"]!=""){
							$user_data['user_email'] = $usersinfo["email"];
						}
						//Permitir email en blanco y duplicado
						define ( 'WP_IMPORTING', true);

						$wpid = wp_insert_user($user_data);
						if ( !is_wp_error($wpid) ) {
							update_usermeta( $wpid, "first_name", $usersinfo["first_name"] );
							update_usermeta( $wpid, "last_name", $usersinfo["last_name"] );
	
							if (isset($usersinfo["sex"]) && $usersinfo["sex"] != ""){
								update_usermeta( $wpid, "sex", $usersinfo["sex"] );
							}
							WPfbConnect_Logic::set_userid_fbconnect($wpid,$fb_user,"google");
							$new_fb_user= true;
						}else{ // no ha podido insertar el usuario
							return;
						}
					}
					
				}else{
					return;
				}
	
				$userdata = WPfbConnect_Logic::get_userbyFBID($fb_user,"google");
				WPfbConnect_Logic::set_lastlogin_fbconnect($userdata->ID);
				global $current_user;
	
				$current_user = null;
				
	
				WPfbConnect_Logic::fb_set_current_user($userdata);
	
				global $userdata;
				if (isset($userdata) && $userdata!=""){
					$userdata->fbconnect_userid = $fb_user;
					$userdata->fbconnect_netid = "google";
				}
				header('Content-type: text/html');
				if ($new_fb_user){
					$new_fb_usertxt = "true";
				}else{
					$new_fb_usertxt = "false";
				}
				$terms = get_user_meta($userdata->ID, "terms", true);
				?>
				<html>
				<body>
				<script>
					window.opener.fb_refreshlogininfo('google','<?php echo $fb_user;?>',<?php echo $new_fb_usertxt;?>,'<?php echo $terms;?>');
					window.close();
				</script>
				</body>
				</html>
				<?php
				exit;
				//Cache friends
				/*WPfbConnect_Logic::get_connected_friends();
				if (fb_get_option('fb_permsToRequestOnConnect')!="" ){
					if (strrpos(fb_get_option('fb_permsToRequestOnConnect'),"offline_access")===false){
						//Not found
					}elseif($userdata!=""){
						$token = fb_get_access_token();
						//update_usermeta( $userdata->ID, "access_token", $token );
						WPfbConnect_Logic::set_useroffline($userdata->ID,$token,1);
						
					}
				}*/
				
			}else{
				header('Content-type: text/html');
				echo "ERROR NO ACCESS TOKEN";
				exit;
			}
		}
	}
	
} 
endif; // end if-class-exists test

require_once FBCONNECT_PLUGIN_PATH.'/google/apiClient.php';
require_once FBCONNECT_PLUGIN_PATH.'/google/contrib/apiPlusService.php';

global $gLogic;
$gLogic = new WPgConnect_Logic;
$gLogic->init();
?>
