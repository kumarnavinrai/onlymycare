<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 05/10/2008
 * @license: GPLv2
 */
require FBCONNECT_PLUGIN_PATH.'/twitter/tmhOAuth.php';
require FBCONNECT_PLUGIN_PATH.'/twitter/tmhUtilities.php';

if (!class_exists('WPtConnect_Logic')):

/**
 * Basic logic for Twitter.
 */
class WPtConnect_Logic {
public $oauthclient;

function init(){
	$this->oauthclient = new tmhOAuth(array(
  	'consumer_key'    => trim(fb_get_option('tw_api_key')),
  	'consumer_secret' => trim(fb_get_option('tw_api_secret')),
	));
}	

function user_getInfo(){
	$code = $this->oauthclient->request('GET', $this->oauthclient->url('1/account/verify_credentials'));
	  if ($code == 200) {
	    $meprofile = json_decode($this->oauthclient->response['response']);
		$userinfo = array();
		$userinfo['uid'] = $meprofile->screen_name;
		$userinfo['username'] = $meprofile->screen_name;
		$userinfo['website'] = $meprofile->url;
		$userinfo['about_me'] = "";
		$userinfo['email'] = $meprofile->email;
		$userinfo['profile_url'] = "http://twitter.com/".$meprofile->screen_name;
		$userinfo['name'] = $meprofile->name;
		$namearray = explode(" ",$meprofile->name);
		$firstname = $namearray[0];
		$lastname = "";
		if (count($namearray)>0){
			$lastname = $namearray[1];
		}
		$userinfo['first_name'] = $firstname;
		$userinfo['last_name'] = $lastname;
		//$userinfo['sex'] = $meprofile->gender;
		$userinfo['locale'] = $meprofile->lang;
		$userinfo['id'] = $meprofile->id_str;
	  } else {
	    $this->outputError($tmhOAuth);
	  }
	  return $userinfo;

}

function check_rate_limit($response) {
  $headers = $response['headers'];
  if ($headers['x_ratelimit_remaining'] == 0) :
    $reset = $headers['x_ratelimit_reset'];
    $sleep = time() - $reset;
    echo 'rate limited. reset time is ' . $reset . PHP_EOL;
    echo 'sleeping for ' . $sleep . ' seconds';
    //sleep($sleep);
  endif;
}

function user_getFriends(){
	$ids = array();
	$cursor = '-1';

	while (true) :
	  if ($cursor == '0')
	    break;
	
	  $this->oauthclient->request('GET', $this->oauthclient->url('1/friends/ids'), array(
	    'cursor' => $cursor
	  ));
	
	  // check the rate limit
	  $this->check_rate_limit($this->oauthclient->response);
	
	  if ($this->oauthclient->response['code'] == 200) {  
	    $data = json_decode($this->oauthclient->response['response'], true);
	    $ids += $data['ids'];
	    $cursor = $data['next_cursor_str'];
	  } else {
	    echo $this->oauthclient->response['response'];
	    break;
	  }
	endwhile;
	return $ids;
}
function createAuthUrl(){
	$siteUrl= fb_get_option('siteurl');
	$pageurl = $siteUrl."/";
	if (!is_home()){
		global $post;
		if ($post!=""){
			$pageurl = get_permalink($post->ID);
		}
	}
	$pageurl = WPfbConnect_Logic::add_urlParam($pageurl,"oauth_login=twitter");	
		
	$params = array(
	    'oauth_callback'     => $pageurl
	);
	$code = $this->oauthclient->request('POST', $this->oauthclient->url('oauth/request_token', ''), $params);

	if ($code == 200) {
	    $_SESSION['oauth'] = $this->oauthclient->extract_params($this->oauthclient->response['response']);
	    $method = 'authorize';
	    $force  = '&force_login=1';
	    $authurl = $this->oauthclient->url("oauth/{$method}", '') .  "?oauth_token={$_SESSION['oauth']['oauth_token']}{$force}";
	} else {
	    $this->outputError($this->oauthclient);
	}
	return $authurl; 
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

		if ( isset($_GET['oauth_login']) && $_GET['oauth_login']=="twitter" ) { //Intenta hacer login estando registrado en facebook
			if (isset($_GET['oauth_verifier'])) {
			  $this->oauthclient->config['user_token']  = $_SESSION['oauth']['oauth_token'];
			  $this->oauthclient->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];
			
			  $code = $this->oauthclient->request('POST', $this->oauthclient->url('oauth/access_token', ''), array(
			    'oauth_verifier' => $_REQUEST['oauth_verifier']
			  ));
			  if ($code == 200) {
			    $_SESSION['access_token'] = $this->oauthclient->extract_params($this->oauthclient->response['response']);
			    unset($_SESSION['oauth']);
			  } else {
			    $this->outputError($this->oauthclient);
			  }
			  //header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
			}
			if (isset($_SESSION['access_token'])) {
			  	$this->oauthclient->config['user_token']  = $_SESSION['access_token']['oauth_token'];
  				$this->oauthclient->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];
			
				require_once(ABSPATH . WPINC . '/registration.php');

				$usersinfo = $this->user_getInfo();

				if (!isset($usersinfo) || $usersinfo["uid"]==""){
					WPfbConnect::log("[fbConnectLogic::wp_login_fbconnect] fb_user_getInfo ERROR: ".$fb_user,FBCONNECT_LOG_ERR);
					return;	
				}
				$fb_user = $usersinfo["uid"];
				$_SESSION["facebook_usersinfo"] = $usersinfo;
				$_SESSION["fbconnect_netid"] = "twitter";
				
				$wpid = "";
				$fbwpuser = WPfbConnect_Logic::get_userbyFBID($usersinfo["uid"],"twitter");
				if ($fbwpuser =="" && $usersinfo["email"]!=""){
					$fbwpuser = WPfbConnect_Logic::get_userbyEmail($usersinfo["email"]);
				}
				//echo "LEER:".$fb_user;
				//print_r($fbwpuser);
				$wpid = "";
				$new_fb_user= false;
				
				if(is_user_logged_in() && $fbwpuser && $user->ID==$fbwpuser->ID && ($user->fbconnect_userid =="" || $user->fbconnect_userid =="0")){ // Encuentra por email el usuario y no está asociado al de FB
					WPfbConnect_Logic::set_userid_fbconnect($user->ID,$fb_user,"twitter");
					$wpid = $user->ID;
				}else if(FBCONNECT_CANVAS!="web" && is_user_logged_in() && $fbwpuser && $user->ID != $fbwpuser->ID){ // El usuario FB está asociado a un usaurio WP distinto al logeado
					$wpid = $fbwpuser->ID;
				}elseif(is_user_logged_in() && !$fbwpuser && ($user->fbconnect_userid =="" || $user->fbconnect_userid =="0")){ // El usuario WP no está asociado al de FB
					WPfbConnect_Logic::set_userid_fbconnect($user->ID,$fb_user,"twitter");
					$wpid = $user->ID;
				}elseif (!is_user_logged_in() && $fbwpuser && ($fbwpuser->fbconnect_userid =="" || $fbwpuser->fbconnect_userid =="0")){
					WPfbConnect_Logic::set_userid_fbconnect($fbwpuser->ID,$fb_user,"twitter");
					$wpid = $fbwpuser->ID;	
				}elseif(!is_user_logged_in() && $fbwpuser && ($fbwpuser->fbconnect_userid ==$fb_user)){
					$wpid = $fbwpuser->ID;	
				}elseif (($fbwpuser && $fbwpuser->fbconnect_userid != $fb_user) || (!is_user_logged_in() && !$fbwpuser) || (!$fbwpuser && is_user_logged_in() && $user->fbconnect_userid != $fb_user)){
					if(isset($usersinfo) && $usersinfo!=""){
						$username = WPfbConnect_Logic::fbusername_generator($usersinfo["uid"],$usersinfo["uid"],$usersinfo["first_name"],$usersinfo["last_name"]);

						$user_data = array();
						$user_data['user_login'] = $username;
						
						$user_data['user_pass'] = substr( md5( uniqid( microtime() ).$_SERVER["REMOTE_ADDR"] ), 0, 15);
	
						$user_data['user_nicename'] = $username;
						$user_data['display_name'] = $usersinfo["name"];
	
						$user_data['user_url'] = $usersinfo["profile_url"];

						$user_data['user_email'] = "";
						
						if ($usersinfo["email"]!=""){
							$user_data['user_email'] = $usersinfo["email"];
						}
						
						define ( 'WP_IMPORTING', true);
						
						$wpid = wp_insert_user($user_data);
						
						if ( !is_wp_error($wpid) ) {
							update_usermeta( $wpid, "first_name", $usersinfo["first_name"] );
							update_usermeta( $wpid, "last_name", $usersinfo["last_name"] );
							update_usermeta( $wpid, "twitter_id", $usersinfo["id"] );
							WPfbConnect_Logic::set_userid_fbconnect($wpid,$fb_user,"twitter");
							$new_fb_user= true;
						}else{ // no ha podido insertar el usuario
							return;
						}
					}
					
				}else{
					return;
				}
				$userdata = WPfbConnect_Logic::get_userbyFBID($fb_user,"twitter");
				WPfbConnect_Logic::set_lastlogin_fbconnect($userdata->ID);
				global $current_user;
	
				$current_user = null;
				
	
				WPfbConnect_Logic::fb_set_current_user($userdata);
				
				//Cache friends
				$friends = $this->user_getFriends();
				WPfbConnect_Logic::deletefriends($fb_uid,"twitter");
				foreach($friends as $friend){
					$userfriend = WPfbConnect_Logic::get_userbymetavalue("twitter_id",$friend);

					if ($userfriend!=""){
						$wpuserfriend = get_userdata($userfriend->user_id);
				  		$userid = $userdata->fbconnect_userid;
						$friendid = $wpuserfriend->fbconnect_userid;
						$wpuserid = $userdata->ID;
						$wpfriendid = $userfriend->user_id;
						$netid = "twitter";
                        $blog_id = WPfbConnect_Logic::get_blogid();	
						global $wpdb;
						$friends_table = WPfbConnect::friends_table_name();	 		  		
                        $wpdb->insert( $friends_table, compact( 'userid', 'friendid', 'wpuserid','wpfriendid','netid','blog_id' ) );
					}
				}
				
				global $userdata;
				if (isset($userdata) && $userdata!=""){
					$userdata->fbconnect_userid = $fb_user;
					$userdata->fbconnect_netid = "twitter";
				}
				$siteUrl= fb_get_option('siteurl');
				$urlthick = WPfbConnect_Logic::add_urlParam($siteUrl,"fbconnect_action=register&height=400&width=370");	
				
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
					//window.opener.fb_registeruserthick();
					window.opener.fb_refreshlogininfo('twitter','<?php echo $fb_user;?>',<?php echo $new_fb_usertxt;?>,'<?php echo $terms;?>');
					window.close();
				</script>
				</body>
				</html>
				<?php
				exit;
				//Cache friends
				//$this->user_getFriends();
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
				
			}
		}
	}
	
	function outputError($oauth) {
	  echo 'Error: ' . $this->oauthclient->response['response'] . PHP_EOL;
	  tmhUtilities::pr($this->oauthclient);
	}
} 
endif; // end if-class-exists test

global $tLogic;
$tLogic = new WPtConnect_Logic;
$tLogic->init();
?>
