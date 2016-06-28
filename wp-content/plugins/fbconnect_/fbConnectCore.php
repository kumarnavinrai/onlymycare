<?php
/*
 Plugin Name: Facebook OpenGraph (Social Layer)
 Plugin URI: http://www.sociable.es/facebook-connect
 Description: Facebook, Google and Twitter login, share and widgets. 
 Author: Javier Reyes
 Author URI: http://www.sociable.es/
 Version: 5.0.11
 License: GPL (http://www.fsf.org/licensing/licenses/info/GPLv2.html) 
 */
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  echo "Direct load not allowed";
  exit;
}

add_filter('option_siteurl', 'sslFBConnectFilter');
add_filter('option_home', 'sslFBConnectFilter');
add_filter('option_url', 'sslFBConnectFilter');
add_filter('option_wpurl', 'sslFBConnectFilter');
add_filter('option_stylesheet_url', 'sslFBConnectFilter');
add_filter('option_template_url', 'sslFBConnectFilter');

function fb_get_option($option, $default = false ) {
	$pos = strpos($option, "fb_");
	$pos2 = strpos($option, "t_");
	$pos3 = strpos($option, "g_");
	$pos4 = strpos($option, "tuenti_");
	$pos5 = strpos($option, "sjworkspaces_");
	
	if ($pos !== false || $pos2 !== false || $pos3 !== false || $pos4 !== false || $pos5 !== false) {
		$alloptions = wp_load_alloptions();
	
		if ( isset( $alloptions[$option] ) ) {
			return $alloptions[$option];
		} else {
			return "";
		}
	}else{
		return get_option($option, $default);
	}
}

function sslFBConnectFilter($value) {
	if(fb_get_option('fb_ssllinkrewrite')!="" && is_ssl() ) {
		$value = preg_replace('|/+$|', '', $value);
		$value = preg_replace('|http://|', 'https://', $value);
		//echo "value:".$value;
	}
	return $value;
}

define('FBCONNECT_LOG_EMERG',    1);     /** System is unusable */
define('FBCONNECT_LOG_ERR',      2);     /** Error conditions */
define('FBCONNECT_LOG_WARNING',  3);     /** Warning conditions */
define('FBCONNECT_LOG_INFO',     4);     /** Informational */
define('FBCONNECT_LOG_DEBUG',    5);     /** Debug-level messages */

define('FBCONNECT_LOG_LEVEL', fb_get_option('fb_connect_log_level')); 

define ( 'FBCONNECT_PLUGIN_REVISION', 138); 

define ( 'FBCONNECT_DB_REVISION', 26);
define ( 'FBCONNECT_TICKWIDTH', 435);
define ( 'FBCONNECT_TICKHEIGHT', 400);

if (! defined('WP_CONTENT_DIR'))
    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');

if (! defined('WP_THEME_DIR'))
    define('WP_THEME_DIR', WP_CONTENT_DIR . '/themes');


define('WP_CONTENT_URL_FB', fb_get_option('siteurl') . '/wp-content');

if (! defined('WP_PLUGIN_DIR'))
    define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');

define('WP_PLUGIN_URL_FB', WP_CONTENT_URL_FB . '/plugins');
	
define ('FBCONNECT_PLUGIN_BASENAME', plugin_basename(dirname(__FILE__)));
define ('FBCONNECT_PLUGIN_PATH', WP_PLUGIN_DIR."/".FBCONNECT_PLUGIN_BASENAME);
define ('FBCONNECT_PLUGIN_PATH_STYLE', FBCONNECT_PLUGIN_PATH."/fbconnect.css");
define ('FBCONNECT_PLUGIN_PATH_LOG', WP_PLUGIN_DIR."/".FBCONNECT_PLUGIN_BASENAME."/Log/fbconnectwp.txt");
define ('FBCONNECT_PLUGIN_URL_LOG', WP_PLUGIN_URL_FB."/".FBCONNECT_PLUGIN_BASENAME."/Log/fbconnectwp.txt");
define ('FBCONNECT_PLUGIN_URL', WP_PLUGIN_URL_FB."/".FBCONNECT_PLUGIN_BASENAME);
define ('FBCONNECT_PLUGIN_URL_IMG', WP_PLUGIN_URL_FB."/".FBCONNECT_PLUGIN_BASENAME."/images");
define ('FBCONNECT_PLUGIN_LANG', FBCONNECT_PLUGIN_BASENAME."/lang");
define ('FBCONNECT_PAGE_URL', "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);

global $fb_reg_formfields;
$fb_reg_formfields = array("name","nickname","email","password","sex","birthdate","user_url","location_city","location_state","location_country","location_zip","about","company_name","phone","terms","twitter","locale");

//set_include_path( dirname(__FILE__) . PATH_SEPARATOR . get_include_path() );   

if  (!class_exists('WPfbConnect')):
class WPfbConnect {
	
	function WPfbConnect() {
		
	}

	function log($msg,$level=1){
		if (FBCONNECT_LOG_LEVEL!=-1 && FBCONNECT_LOG_LEVEL!="" && $level<= FBCONNECT_LOG_LEVEL){
			$text_level = array('EMERG','ERROR','WARN ','INFO ','DEBUG');
			$date = date('d/m/Y H:i:s'); 
			$msg = "[".$date."] [".$text_level[$level-1]."] ".$msg."\n"; 
			$resp = error_log($msg, 3, FBCONNECT_PLUGIN_PATH_LOG);
		}elseif($level<3 && $level>-1){
			$resp = error_log($msg);
		}
	}

	function netId() {
		$fbconnect_netid = $_SESSION["fbconnect_netid"];

		if ($fbconnect_netid==""){
			return "facebook";
		}else{
			return $fbconnect_netid;
		}
	}
	
	function textdomain() {
		load_plugin_textdomain('fbconnect', PLUGINDIR ."/".FBCONNECT_PLUGIN_LANG);
	}

	function table_prefix() {
		global $wpdb;
		return isset($wpdb->base_prefix) ? $wpdb->base_prefix : $wpdb->prefix;
	}

	function comments_table_name() { global $wpdb; return $wpdb->comments; }
	function usermeta_table_name() { global $wpdb; return $wpdb->usermeta; }
	function users_table_name() { global $wpdb; return $wpdb->users; }
	function friends_table_name() 
	{ 
		global $wpdb; 
		$aux=$wpdb->users;

        $pos = strpos($aux, "users");
        return substr($aux, 0, $pos) . 'fb_friends'; 
	}
	
	function lastlogin_table_name()
	{
		global $wpdb; 
		$aux=$wpdb->users;

        $pos = strpos($aux, "users");
        return substr($aux, 0, $pos) . 'fb_lastlogin'; 

	}
}
endif;



if ( ! session_id() ){
	 session_start();
}

require_once(FBCONNECT_PLUGIN_PATH.'/fbConnectLogic.php');

require_once(FBCONNECT_PLUGIN_PATH.'/fbConnectInterface.php');

if(file_exists (FBCONNECT_PLUGIN_PATH.'/pro/fbConnectCorePro.php')){
	require_once(FBCONNECT_PLUGIN_PATH.'/pro/fbConnectCorePro.php');
}else{
	define ('FBCONNECT_CANVAS', "web");
}

//Tuenti API
if(file_exists (FBCONNECT_PLUGIN_PATH.'/pro/tuentiConnectLogic.php')){
	require_once FBCONNECT_PLUGIN_PATH.'/pro/tuentiConnectLogic.php';
	require_once FBCONNECT_PLUGIN_PATH.'/pro/tuentiConnectInterface.php';
	
	function login_tuenti(){
		global $tuentiLogic;
		$tuentiLogic->wp_login();
	}
	
	if(fb_get_option('tuenti_login_enabled')){
		add_action( 'init', "login_tuenti" ,103 );
	}
}

define('WP_THEME', fb_get_option('siteurl') . '/wp-content/themes');
/*if (!defined('WP_THEME') && FBCONNECT_CANVAS=="web"){
	define('WP_THEME', fb_get_option('siteurl') . '/wp-content/themes');
}elseif(!defined('WP_THEME')){
	define('WP_THEME', FBCONNECT_PLUGIN_URL . '/themes');
}*/
define('WP_THEME_FB', fb_get_option('siteurl') . '/wp-content/themes');
/*if (FBCONNECT_CANVAS=="web"){
	define('WP_THEME_FB', fb_get_option('siteurl') . '/wp-content/themes');
}else{
	define('WP_THEME_FB', FBCONNECT_PLUGIN_URL . '/themes');
}*/
if (! defined('WP_CURRENT_THEME_PATH'))
    define('WP_CURRENT_THEME_PATH', get_template_directory());
		
define('WP_CURRENT_THEME_URL_FB', get_bloginfo('stylesheet_directory'));

//restore_include_path();

	
WPfbConnect_Logic::updateplugin();

$rutaTemplate=get_bloginfo('stylesheet_directory', $filter = 'raw');

add_action('init', 'fb_load_scripts',1);

function fb_load_scripts(){
	wp_enqueue_script('jquery',"","","",false);
	
	if(file_exists (WP_CURRENT_THEME_PATH.'/fbconnect.js')){
		wp_enqueue_script('fbconnect_script', WP_CURRENT_THEME_URL_FB.'/fbconnect.js',array(),FBCONNECT_PLUGIN_REVISION); 
	}else{
		wp_enqueue_script('fbconnect_script', FBCONNECT_PLUGIN_URL.'/fbconnect.js',array(),FBCONNECT_PLUGIN_REVISION); 
	}
	
	
	if (fb_get_option('tw_add_post_head_share')!="" || fb_get_option('tw_add_post_share')!="") {
		wp_enqueue_script("twitter","http://platform.twitter.com/widgets.js");
	}
	
	if (fb_get_option('li_add_post_head_share')!="" || fb_get_option('li_add_post_share')!="") {
		wp_enqueue_script("linkedin","http://platform.linkedin.com/in.js");
	}
	
	if (fb_get_option('fb_add_post_head_google1')!="" || fb_get_option('fb_add_post_google1')!="") {
		wp_enqueue_script("plusone","https://apis.google.com/js/plusone.js");
	}
	
	if (fb_get_option('fb_add_post_head_pinterest')!="" || fb_get_option('fb_add_post_pinterest')!="") {
		wp_enqueue_script("pinterest","http://assets.pinterest.com/js/pinit.js","","1",true);
	}
	add_thickbox();
}

if (!function_exists('fbconnect_title')):
function fbconnect_title($title) {
	if($_REQUEST['fbconnect_action']=="community"){
		return __('Community', 'fbconnect')." - ".$title;
	}else if($_REQUEST['fbconnect_action']=="myhome"){
		$userprofile = WPfbConnect_Logic::get_user();
		return $userprofile->display_name." - ".$title;
	}else if($_REQUEST['fbconnect_action']=="invite"){
		return _e('Invite your friends', 'fbconnect')." - ".$title;
	}
		
	return $title;
}
endif;

/*
Ver rewrite.php
if (!function_exists('fbconnect_add_custom_urls')):
function fbconnect_add_custom_urls() {
  add_rewrite_rule('(userprofile)/[/]?([0-9]*)[/]?([0-9]*)$', 
  'index.php?fbconnect_action=myhome&fbuserid=$matches[2]&var2=$matches[3]');
  add_rewrite_tag('%fbuserid%', '[0-9]+');
  add_rewrite_tag('%var2%', '[0-9]+');
}
endif;
*/
//wp_enqueue_script( 'prototype' );
// -- Register actions and filters -- //

add_filter('wp_title', 'fbconnect_title');

// runs the function in the init hook
//add_action('init', 'fbconnect_add_custom_urls');

add_filter('get_comment_author_url', array('WPfbConnect_Logic', 'get_comment_author_url'));
add_filter('get_avatar_comment_types', array('WPfbConnect_Logic', 'get_avatar_comment_types'));

add_filter('get_comment_author_link', array('WPfbConnect_Logic','fbc_remove_nofollow'));
if (fb_get_option('fb_hide_wpcomments') || fb_get_option('fb_show_fbcomments')){
	add_filter('comments_template', array('WPfbConnect_Logic','fbc_comments_template'));
}

add_action('the_content', array( 'WPfbConnect_Interface', 'add_fbshare' ),9 );

//add_filter('get_the_excerpt', array( 'WPfbConnect_Interface','remove_share'), 1); 

//if(fb_get_option('fb_add_main_image')){
	add_action('admin_menu', array( 'WPfbConnect_Interface','fbconnect_add_main_img_box') );
	add_action('save_post', array( 'WPfbConnect_Interface','fbconnect_save_post'));
//}

if(fb_get_option('fb_add_wpmain_image')){
	if(function_exists('add_theme_support')):
		add_theme_support( 'post-thumbnails' );	
	endif;
}
add_action( 'init', array( 'WPfbConnect','textdomain') ,1 ); // load textdomain

register_activation_hook(FBCONNECT_PLUGIN_BASENAME.'/fbConnectCore.php', array('WPfbConnect_Logic', 'activate_plugin'));
register_deactivation_hook(FBCONNECT_PLUGIN_BASENAME.'/fbConnectCore.php', array('WPfbConnect_Logic', 'deactivate_plugin'));

add_action( 'admin_menu', array( 'WPfbConnect_Interface', 'add_admin_panels' ) );

add_filter('language_attributes', array('WPfbConnect_Logic', 'html_namespace'));
add_filter('get_avatar', array('WPfbConnect_Logic', 'fb_get_avatar'),10,4);
// Add hooks to handle actions in WordPress

//add_action( 'wp_authenticate', array( 'WPfbConnect_Logic', 'wp_authenticate' ) );
add_action( 'logout_url', array( 'WPfbConnect_Logic', 'fb_logout'),1);

if(fb_get_option('fb_facebooklogin_enabled')){
	add_action( 'init', array( 'WPfbConnect_Logic', 'wp_login_fbconnect' ),100 ); 
}

require_once(FBCONNECT_PLUGIN_PATH.'/gConnectInterface.php');
if(fb_get_option('g_login_enabled')){
	//Google API
	require_once FBCONNECT_PLUGIN_PATH.'/gConnectLogic.php';
	add_action( 'init', "login_google" ,101 );
}
require_once(FBCONNECT_PLUGIN_PATH.'/tConnectInterface.php');
if(fb_get_option('t_login_enabled')){
	//Twitter API
	require_once FBCONNECT_PLUGIN_PATH.'/tConnectLogic.php';
	add_action( 'init', "login_twitter" ,102 );
}

function login_google(){
	global $gLogic;
	$gLogic->wp_login();
}
function login_twitter(){
	global $tLogic;
	$tLogic->wp_login();
}

add_action( 'init', 'fb_remove_admin_bar' ,1 ); 
if(!function_exists('fb_remove_admin_bar')):
function fb_remove_admin_bar(){
	if (fb_get_option('fb_removeadminbar') && current_user_can('subscriber')){
		show_admin_bar(false);
		wp_deregister_script('admin-bar');
		wp_deregister_style('admin-bar');
		remove_filter('wp_head','wp_admin_bar');
		remove_filter('wp_footer','wp_admin_bar');
		remove_filter('admin_head','wp_admin_bar');
		remove_filter('admin_footer','wp_admin_bar');
		remove_filter('wp_head','wp_admin_bar_class');
		remove_filter('wp_footer','wp_admin_bar_class');
		remove_filter('admin_head','wp_admin_bar_class');
		remove_filter('admin_footer','wp_admin_bar_class');
		remove_action('wp_head','wp_admin_bar_render',1000);
		remove_filter('wp_head','wp_admin_bar_render',1000);
		remove_action('wp_footer','wp_admin_bar_render',1000);
		remove_filter('wp_footer','wp_admin_bar_render',1000);
		remove_action('admin_head','wp_admin_bar_render',1000);
		remove_filter('admin_head','wp_admin_bar_render',1000);
		remove_action('admin_footer','wp_admin_bar_render',1000);
		remove_filter('admin_footer','wp_admin_bar_render',1000);
		remove_action('init','wp_admin_bar_init');
		remove_filter('init','wp_admin_bar_init');
		remove_action('wp_head','wp_admin_bar_css');
		remove_action('wp_head','wp_admin_bar_dev_css');
		remove_action('wp_head','wp_admin_bar_rtl_css');
		remove_action('wp_head','wp_admin_bar_rtl_dev_css');
		remove_action('admin_head','wp_admin_bar_css');
		remove_action('admin_head','wp_admin_bar_dev_css');
		remove_action('admin_head','wp_admin_bar_rtl_css');
		remove_action('admin_head','wp_admin_bar_rtl_dev_css');
		remove_action('wp_footer','wp_admin_bar_js');
		remove_action('wp_footer','wp_admin_bar_dev_js');
		remove_action('admin_footer','wp_admin_bar_js');
		remove_action('admin_footer','wp_admin_bar_dev_js');
		remove_action('wp_ajax_adminbar_render','wp_admin_bar_ajax_render');
		remove_filter('wp_ajax_adminbar_render','wp_admin_bar_ajax_render');
		remove_action('personal_options','_admin_bar_pref');
		remove_filter('personal_options','_admin_bar_pref');
		remove_action('personal_options','_get_admin_bar_pref');
		remove_filter('personal_options','_get_admin_bar_pref');
		remove_filter('locale','wp_admin_bar_lang');
		remove_filter('admin_footer','wp_admin_bar_render');
	}
}
endif;

// Comment filtering
add_action( 'comment_post', array( 'WPfbConnect_Logic', 'comment_fbconnect' ), 5 );

add_filter( 'comment_post_redirect', array( 'WPfbConnect_Logic', 'comment_post_redirect'), 0, 2);
if( fb_get_option('fb_enable_approval') ) {
	add_filter( 'pre_comment_approved', array('WPfbConnect_Logic', 'comment_approval'));
}


// include internal stylesheet
add_action( 'wp_head', array( 'WPfbConnect_Interface', 'style'),100);
add_action( 'login_head', array( 'WPfbConnect_Interface', 'style'),1);

if( fb_get_option('fb_enable_commentform') ) {
	add_action( 'comment_form', array( 'WPfbConnect_Interface', 'comment_form'), 10);
	add_action( 'comment_form_must_log_in_after', array( 'WPfbConnect_Interface', 'comment_form'), 10);
}

add_action( 'admin_init', 'fb_admin_init' ); 
add_action( 'admin_init', array( 'WPfbConnect_Logic', 'wp_login_fbconnect' ),1 ); 	
if(!function_exists('fb_admin_init')):
	function fb_admin_init(){
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-selectable');
		//wp_enqueue_script('jquery-form');
		wp_enqueue_script("thickbox");
		
		// Add only in Rich Editor mode
	   /*if ( get_user_option('rich_editing') == 'true') {
    	 add_filter("mce_external_plugins", "add_fb_tinymce_plugin");
	     add_filter('mce_buttons', 'register_fb_button');
	   }*/
	}
endif;

add_filter( 'single_template', array( 'WPfbConnect_Interface', 'fbconnect_single_template'));
add_filter( 'page_template', array( 'WPfbConnect_Interface', 'fbconnect_single_template'));
add_filter('user_contactmethods', 'fb_set_user_contactmethods');

if(!function_exists('fb_set_user_contactmethods')):
function fb_set_user_contactmethods($contactslist){
	$fb_form_fields = fb_get_option('fb_form_fields');
	$fb_form_fields_bool = array();
	global $fb_reg_formfields;
	if ($fb_reg_formfields){
		foreach($fb_reg_formfields as $field){
		 	$pos = strrpos($fb_form_fields, ";".$field.";");
			if (!is_bool($pos)) {
				$fb_form_fields_bool[$field] = $field;
			}
		}
	}
	$fulllist = array_merge($contactslist,$fb_form_fields_bool);
	unset($fulllist["email"]);
	unset($fulllist["name"]);
	return $fulllist;
}
endif;

if(!function_exists('register_fb_button')):
function register_fb_button($buttons) {
   array_push($buttons, "separator", "fbconnect");
   return $buttons;
}
endif;
if (get_appId()!=""){
	add_action( 'login_form', 'fb_wp_login_form');
}
if(!function_exists('fb_wp_login_form')):
function fb_wp_login_form() {
	//echo '<hr style="clear: both; margin-bottom: 1.0em; border: 0; border-top: 1px solid #999; height: 1px;" />';
	echo '<div style="margin-top:5px;margin-bottom:20px;">';
	echo '<div style="margin-bottom:3px;with:100%">Or login with your social platform:</div>';
	$url = fb_get_option('siteurl');	
	if (isset($_REQUEST['redirect_to']) && $_REQUEST['redirect_to']!=""){
	   $url =$_REQUEST['redirect_to'];
	}
	$url = FBCONNECT_PLUGIN_URL . "/fbconnect_redirect.php?urlredirect=".urlencode($url);
	$loginbutton = "medium";
	$fb_hide_edit_profile = true;
	$fb_hide_invite_link = true;
	$fb_breaklinesprofile = "off";
	$hidelogintext = true;
	$fb_mainavatarsize = 30;
	include FBCONNECT_PLUGIN_PATH."/fbconnect_widget_login.php";
		
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		set_fb_redirect_login_url("<?php echo $url;?>");
	});
	</script>
	</div>
<?php
	WPfbConnect_Logic::fbconnect_init_scripts();
}
endif;

/* TODO: Facebook tags
 * 
 */
if(!function_exists('add_fb_tinymce_plugin')):
function add_fb_tinymce_plugin($plugin_array) {
   $plugin_array['fbconnect'] = FBCONNECT_PLUGIN_URL.'/editor_plugin.js';
   return $plugin_array;
}
endif;


add_action( 'admin_head', 'fb_admin_head' );

if(!function_exists('fb_admin_head')):
	function fb_admin_head(){
		echo '<link rel="stylesheet" href="'.fb_get_option('siteurl').'/'.WPINC.'/js/thickbox/thickbox.css" type="text/css" media="screen" />';
	}
endif;

add_action('admin_head', array( 'WPfbConnect_Interface', 'style'),1);
add_action('admin_footer', array( 'WPfbConnect_Logic', 'fbconnect_init_scripts'), 1);

add_action( 'wp_footer', array( 'WPfbConnect_Logic', 'fbconnect_init_scripts'), 1);

if(!function_exists('carga_template')):
function carga_template() {
	
	if (isset($_REQUEST['fbconnect_action']) && $_REQUEST["fbconnect_action"]!="postlogout"){
		//set_include_path( TEMPLATEPATH . PATH_SEPARATOR . dirname(__FILE__) .PATH_SEPARATOR. WP_PLUGIN_DIR.'/'.FBCONNECT_PLUGIN_BASENAME. PATH_SEPARATOR . get_include_path() );   
		if($_REQUEST['fbconnect_action']=="community"){
			include( FBCONNECT_PLUGIN_PATH.'/community.php');
		}else if($_REQUEST['fbconnect_action']=="redirectoauthgoogle"){
			global $gLogic;
			$authUrl = $gLogic->createAuthUrl();
			header('Location: ' . $authUrl);
			exit;
		}else if($_REQUEST['fbconnect_action']=="redirectoauthtwitter"){
			global $tLogic;
			$tauthUrl = $tLogic->createAuthUrl();
			header('Location: ' . $tauthUrl);
			exit;
		}else if($_REQUEST['fbconnect_action']=="delete_user"){
			fb_delete_user();
		}else if($_REQUEST['fbconnect_action']=="register"){
			include( FBCONNECT_PLUGIN_PATH.'/fbconnect_register.php');
		}else if($_REQUEST['fbconnect_action']=="register_update"){
			WPfbConnect_Logic::register_update();
			//include( 'pro/fbconnect_register.php');
			wp_redirect( fb_get_option('siteurl') );
		}else if($_REQUEST['fbconnect_action']=="myhome"){
			include( FBCONNECT_PLUGIN_PATH.'/myhome.php');
		}else if($_REQUEST['fbconnect_action']=="sidebar"){
			include(FBCONNECT_PLUGIN_PATH.'/pro/fbconnect_sidebar.php');
		}else if($_REQUEST['fbconnect_action']=="cacheimage"){
			include(FBCONNECT_PLUGIN_PATH.'/pro/fbconnect_cacheimage.php');
		}else if($_REQUEST['fbconnect_action']=="tab"){
			include(FBCONNECT_PLUGIN_PATH.'/fbconnect_tab.php');
		}else if($_REQUEST['fbconnect_action']=="invite"){
			include(FBCONNECT_PLUGIN_PATH.'/invitefriends.php');
		}else if($_REQUEST['fbconnect_action']=="userpages"){
			include(FBCONNECT_PLUGIN_PATH.'/fbUserPages.php');
		}else if($_REQUEST['fbconnect_action']=="updatecomments"){
			WPfbConnect_Logic::update_comments();
			exit;
		}else if($_REQUEST['fbconnect_action']=="logout"){
			fb_clearAllPersistentData();
			setcookie('fbsr_' . get_appId(), '', time()-3600, '/', '.'.$_SERVER['SERVER_NAME']);
			if(function_exists('wp_logout')){
				wp_logout();
				//wp_clear_auth_cookie();
			}
			session_destroy();
			
			if(function_exists('wp_redirect')){
				if (isset($_SERVER["HTTP_REFERER"]) && $_SERVER["HTTP_REFERER"]!=""){
					$urlredir = WPfbConnect_Logic::add_urlParam($_SERVER["HTTP_REFERER"],"fbconnect_action=postlogout");
				}else{
					$urlredir = WPfbConnect_Logic::add_urlParam(fb_get_option('siteurl'),"fbconnect_action=postlogout") ;
				}
				$urlredir = apply_filters('fb_logoutredirecturl', $urlredir);
				wp_redirect( $urlredir );
			}
		}else if($_REQUEST['fbconnect_action']=="ajaxperms"){
			//echo "OK";
			$fb_user = fb_get_loggedin_user();
			if ($fb_user!=""){
				$perms = fb_get_userPrmisions($fb_user);
				print_r($perms);
				if ($perms!="ERROR"){
					foreach($perms as $keyperm=>$valperm){
						if ($valperm && $keyperm!="uid"){
							echo $keyperm.",";
						}
					}
				}else{
					echo "";
				}
				/*if ($perms!="ERROR" && $perms["publish_stream"]&& $perms["read_stream"]){
					echo 'yes';
				}else{
					echo 'no';					
				}*/
			}else{
				echo '';
			}
			exit;
			//print_r($_REQUEST);
		}else if($_REQUEST['fbconnect_action']=="fbfeed"){
			include( FBCONNECT_PLUGIN_PATH.'/pro/fbfeed.php');
		}else if($_REQUEST['fbconnect_action']=="stream"){
			include(FBCONNECT_PLUGIN_PATH.'/pro/fbStream.php');
		}else if($_REQUEST['fbconnect_action']=="friendsStream"){
			include(FBCONNECT_PLUGIN_PATH.'/pro/fbFriendsStream.php');
		}else if($_REQUEST['fbconnect_action']=="friendsSearch"){
			include(FBCONNECT_PLUGIN_PATH.'/pro/fbFriendsSearch.php');
		}else if($_REQUEST['fbconnect_action']=="publishStream"){
			include(FBCONNECT_PLUGIN_PATH.'/pro/fbPublishStream.php');
		}else if($_REQUEST['fbconnect_action']=="publisher"){
			//print_r($_REQUEST);
		}else if($_REQUEST['fbconnect_action']=="mainimage"){
			WPfbConnect_Interface::fbconnect_img_selector($_REQUEST["postid"]);		
		}

		//restore_include_path();
		exit;
	}
}
endif;
add_action('template_redirect', 'carga_template');

if(!function_exists('fb_delete_user')):
function fb_delete_user() {

include( ABSPATH.'wp-admin/includes/user.php');
		$url = fb_get_option('siteurl');
		if ( current_user_can('subscriber')){
			/*Reasign posts
			 * $wp_user_search = new WP_User_Search( '', '', "admin");
			$admin = "";
			if ($wp_user_search!="" && $wp_user_search->results!=""){
				$admin = $wp_user_search->results[0];
			}*/
 			$user = wp_get_current_user();
			$fb_user = fb_get_loggedin_user();
			
			$url .= "?fbconnect_action=postlogout";
			if (  isset($user) && $user!="" && $fb_user!="" && $user->fbconnect_userid == $fb_user){
				wp_delete_user( $user->ID, $admin );
				if(function_exists('wp_logout')){
					wp_logout();
				}
				//$url =  wp_logout_url( fb_get_option('siteurl') ); 
				
			}
			
		}
		wp_redirect( $url);
}
endif;			
/**
 * If the current comment was submitted with FacebookConnect, return true
 * useful for  <?php echo ( is_comment_fbconnect() ? 'Submitted with FacebookConnect' : '' ); ?>
 */
if(!function_exists('is_comment_fbconnect')):
function is_comment_fbconnect() {
	global $comment;
	return ( $comment->fbconnect == 1 );
}
endif;

/**
 * If the current user registered with FacebookConnect, return true
 */
if(!function_exists('is_user_fbconnect')):
function is_user_fbconnect($id = null) {
	global $current_user;
    $user = $current_user;
	if ($id != null) {
		$user = get_userdata($id);
	}
	if($user!=null && $user->fbconnect_userid){
		return true;
	}else{
		return false;
	}
}
endif;

require_once(FBCONNECT_PLUGIN_PATH.'/Widget_FacebookConnector.php');
require_once(FBCONNECT_PLUGIN_PATH.'/Widget_FanBox.php');
require_once(FBCONNECT_PLUGIN_PATH.'/Widget_LastFriends.php');
require_once(FBCONNECT_PLUGIN_PATH.'/Widget_FriendsFeed.php');
require_once(FBCONNECT_PLUGIN_PATH.'/Widget_CommentsFeed.php');
require_once(FBCONNECT_PLUGIN_PATH.'/Widget_LastUsers.php');
require_once(FBCONNECT_PLUGIN_PATH.'/Widget_ActivityRecommend.php');
require_once(FBCONNECT_PLUGIN_PATH.'/Widget_Recommend.php');

?>