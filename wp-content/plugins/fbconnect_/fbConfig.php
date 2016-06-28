<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 05/10/2008
 * @license: GPLv2
 */

	
	if  (!class_exists('FacebookSociable')){
		WPfbConnect::log("[Load Facebook PHP SDK server API for PHP5]:",FBCONNECT_LOG_DEBUG);	
		
			try{
				include_once FBCONNECT_PLUGIN_PATH.'/facebook-sdk/facebook.php';
			}catch(Exception $e){
	  			WPfbConnect::log("[Error loading Facebook SDK]: ".$e->getMessage(),FBCONNECT_LOG_ERR);
	  		}
		
	}	


	try{	
		include_once FBCONNECT_PLUGIN_PATH.'/fbConfig_phpsdk.php';
	}catch(Exception $e){
		WPfbConnect::log("[Error loading Facebook SDK]: ".$e->getMessage(),FBCONNECT_LOG_ERR);
	}
	


function is_config_setup() {
  return (get_appId() && get_api_secret());
}

// Whether the site is "connected" or not
function is_fbconnect_enabled() {
  if (!is_config_setup()) {
    return false;
  }

  // Change this if you want to turn off Facebook connect
  return true;
}
function get_api_key() {
		return trim(fb_get_option('fb_api_key'));
}
function get_api_secret() {

		if (isset($_REQUEST["fbtabpage"]) && $_REQUEST["fbtabpage"]!=""){
			$appsecret = get_post_meta($_REQUEST["fbtabpage"], "workspace_appsecret", true);
			if (trim($appsecret)!=""){
				return trim($appsecret);
			}
		}
		return trim(fb_get_option('fb_api_secret'));
}

function get_appId(){
	if (isset($_REQUEST["fbtabpage"]) && $_REQUEST["fbtabpage"]!=""){
		$appid = get_post_meta($_REQUEST["fbtabpage"], "workspace_appid", true);
		if (trim($appid)!=""){
			return trim($appid);
		}
	}
	return trim(fb_get_option('fb_appId'));
}

function get_base_fb_url() {
  return "connect.facebook.com";
}

function get_ssl_root() {
  return 'https://www.'.get_base_fb_url();
}


function get_static_root() {
  return 'http://static.ak.'.get_base_fb_url();
}

function get_graph_url() {
  return 'https://graph.facebook.com';
}

function get_graph_call($function,$params=array(),$addToken=true) {
  if ($addToken){
	  $params['access_token'] = fb_get_access_token();
	}
  return get_graph_url().$function.'?'.http_build_query($params);
}

function get_feed_bundle_id() {
  return fb_get_option('fb_templates_id');
}

/*
 * Get the facebook client object for easy access.
 */
function facebook_client() {
	
  static $facebook = null;
  $api_key = get_appId();
  if ($api_key==""){
	  $api_key = get_api_key();
  }
  $api_secret = get_api_secret();

  if(isset($_REQUEST["fb_sig_in_canvas"]) || isset($_REQUEST["fb_sig_in_profile_tab"])){
  	$facebook = new FacebookSociable($api_key, $api_secret, false, get_base_fb_url());
  }else{

	  if (class_exists('FacebookSociable') && $facebook === null && $api_key!="" && $api_secret!="") {
	  	//new SDK

			$facebook = new FacebookSociable(array(
			  'appId'  => get_appId(),
			  'secret' => $api_secret,
			  'cookie' => true, // enable optional cookie support
			));
			FacebookSociable::$CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = false;
			FacebookSociable::$CURL_OPTS[CURLOPT_SSL_VERIFYHOST] = 2;

	  }
  }
  return $facebook;
}

function facebook_construct(&$facebook,$api_key, $api_secret) {
	if (version_compare("5", phpversion(),"<")){
		$wprest = new WPFacebookRestClient($api_key, $api_secret, null);
	}else{
		$wprest = new WPFacebookRestClient($api_key, $api_secret,$facebook, null);
	}
	$facebook->api_client = $wprest;

    // Set the default user id for methods that allow the caller to
    // pass an explicit uid instead of using a session key.

    if (isset($facebook->fb_params['friends'])) {
      $facebook->api_client->friends_list =
        array_filter(explode(',', $facebook->fb_params['friends']));
    }
    if (isset($facebook->fb_params['added'])) {
      $facebook->api_client->added = $facebook->fb_params['added'];
    }
    if (isset($facebook->fb_params['canvas_user'])) {
      $facebook->api_client->canvas_user = $facebook->fb_params['canvas_user'];
    }
  }
  
/*
 * Get the facebook mobile client object for easy access.
 */
function facebook_mobile_client() {
  static $facebook_mobile = null;
  $api_key = get_api_key();
  $api_secret = get_api_secret();
  if ($facebook === null && $api_key!="" && $api_secret!="") {
	$facebook = new FacebookMobile($api_key, $api_secret);
  }
  return $facebook;
}

function fb_streamPublishDialogCode($url,$imgurl,$title,$caption,$body,$action_links="",$callback="",$targetuid="" ){
	if($callback=="")
		$callback="null";

		if ($callback==""){
			$callback= "function(response) {}";	
		}
		if ($targetuid!=""){
			$targetsrc = ",uid: ".$targetuid;
		}else{
			$targetsrc ="";
		}
		if ($action_links!=""){
			$actionsrc = ",actions: ".fb_json_encode($action_links);
		}else{
			$actionsrc = "";
		}
		if ($attachment!=""){
			$attachmentsrc = ",attachment: ".fb_json_encode($attachment);
		}else{
			$attachmentsrc = "";
		}
		?>
		 FB.ui(
				   {
				     method: 'feed',
				     name: <?php echo fb_json_encode(strip_tags($title));?>,
				     caption: <?php echo fb_json_encode(strip_tags($caption));?>,
				     description: <?php echo fb_json_encode(strip_tags($body));?>,
				     picture: '<?php echo $imgurl;?>',
				     link: '<?php echo $url;?>'
				     <?php echo $actionsrc;?>
				     <?php echo $targetsrc;?>
				   },
				   <?php echo $callback;?>
				 );
		<?php 
		
}

function fb_streamPublishDialog(){
		
		if (isset($_SESSION["template_data"]) && $_SESSION["template_data"]!=""){
				$template_data = $_SESSION["template_data"];
				//echo "<script type='text/javascript'>\n";
				echo fb_streamPublishDialogCode($_SESSION["template_data"]['url'],$_SESSION["template_data"]['imgurl'],$_SESSION["template_data"]['title'],$_SESSION["template_data"]['caption'],$_SESSION["template_data"]['body'],$_SESSION["template_data"]['actions'],"","" );
				$_SESSION["template_data"] = "";
		}
}