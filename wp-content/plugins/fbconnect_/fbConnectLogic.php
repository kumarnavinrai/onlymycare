<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 05/10/2008
 * @license: GPLv2
 */
if (!class_exists('WPfbConnect_Logic')):

/**
 * Basic logic for wp-fbConnect plugin.
 */
class WPfbConnect_Logic {
 		
 	function get_url_content($url){
 		if (function_exists('curl_init')) {
	 		$ch = curl_init(); 
			$timeout = 0; 
			curl_setopt ($ch, CURLOPT_URL, $url); 
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
			$file_contents = curl_exec($ch); 
			
			if ($file_contents === false) {
		      throw new Exception(curl_error($ch));
			  curl_close($ch);
			  throw $e;
			}
			curl_close($ch); 
		}elseif(function_exists('file_get_contents')){
			$file_contents = file_get_contents($url);
		}else{
			throw new Exception('fbcnnect plugin needs the CURL PHP extension or file_get_contents support.');
		}
		return $file_contents;
 	}
	
	function getmicrotime()	{
		list($usec, $sec) = explode(" ",microtime()); 
		return round(((float)$usec + (float)$sec)); 
	} 
	
	function cutWords($tamano,$texto){

		$contador = 0;
		$arrayTexto = split(' ',$texto);
		$texto = '';

		while($tamano >= strlen($texto) + strlen($arrayTexto[$contador])){
		    $texto .= ' '.$arrayTexto[$contador];
		    $contador++;
		}
		return $texto;

	}
	function redirect($url=null){
		if ($url==null){
			$url = fb_get_option('siteurl');
		}
		
		wp_redirect( $url );
		
	}
	
	function getMobileClient() {
		//return "iphone";
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$useragents = array(		
			"iphone",  			// Apple iPhone
			"ipod", 			// Apple iPod touch
			"aspen", 			// iPhone simulator
			"dream", 			// Pre 1.5 Android
			"android", 			// 1.5+ Android
			"cupcake", 			// 1.5+ Android
			"blackberry9500",	// Storm
			"blackberry9530",	// Storm
			"opera mini", 		// Experimental
			"webos",			// Experimental
			"incognito", 		// Other iPhone browser
			"webmate" 			// Other iPhone browser
		);
		foreach ( $useragents as $useragent ) {
			if ( eregi( $useragent, $user_agent )  ) {
				return $useragent;
			} 	
		}
		return "";
	}
	
	function get_friends_data() {
	  global $wpdb, $fbconnect;
      
      $fb_blogid = WPfbConnect_Logic::get_blogid();
	  
	  $friends_table = WPfbConnect::friends_table_name();
	  $user = wp_get_current_user();
	  if ( isset($user) && $user!="" && $user->fbconnect_userid != '' && $user->fbconnect_userid != '0'){
			$fb_uid = $user->fbconnect_userid ;
			
			$results = array();

			$query = 'SELECT uid, significant_other_id ,online_presence ,verified ,username,first_name, last_name,pic_small,birthday,birthday_date,sex,has_added_app,family,current_location,wall_count,notes_count   FROM user WHERE strpos(lower(first_name),lower(\'ier\'))>=0 AND uid IN '.
			'(SELECT uid2 FROM friend WHERE uid1 = '.$fb_uid.') ORDER BY wall_count DESC LIMIT 50';
			$rows = fb_fql_query($query);
			print_r($rows);
			if ($rows!=null && !empty($rows)) {
			  foreach ($rows as $row) {
			      $results[] = $row;
                  //$wpdb->insert( $friends_table, compact( 'userid', 'friendid', 'wpuserid','wpfriendid','netid','blog_id' ) );
			    //}
			  }
			}
			//$users_table = WPfbConnect::users_table_name();
			//$users = $wpdb->get_results("SELECT * FROM $friends_table friends,$users_table users WHERE friends.wpuserid=".$user->ID." AND friends.wpfriendid=users.ID ORDER BY users.fbconnect_lastlogin DESC ");
			return $rows;
		 }else{
		 	return "";
		 }
	}


	function add_facebook_tag($tagname,$tagdesc,$tagid,$category,$location="",$link="",$website=""){
		$idterm = term_exists($tagname, "facebooktags");
        if ($idterm){
            return $idterm["term_id"];
        }else{
        	$name = $tagname;
			$description = $tagdesc;
			$slug = "";
			$parent = "";
        	$args = compact('name', 'slug', 'parent', 'description');
        	$cat_ID = wp_insert_term($tagname, "facebooktags", $args);
			WPfbConnect_Logic::set_termsid_fbconnect($cat_ID["term_id"], $tagid, "facebook",$category,$location,$link,$website);
			return $cat_ID["term_id"];
        }
	}
	
	function cache_user_likes($fb_uid=""){
		global $wpdb, $fbconnect;
      	$fb_blogid = WPfbConnect_Logic::get_blogid();
		$friends_table = WPfbConnect::friends_table_name();
		if ($fb_uid==""){
	 		$user = wp_get_current_user();
			$fb_uid = $user->fbconnect_userid ;
		}else{
			$user = WPfbConnect_Logic::get_userbyFBID($fb_uid);
		}
		$rows = fb_get_user_likes($fb_uid);
		$rows2 = fb_get_user_likes($fb_uid,"interests");
		//print_r($rows2);
		//print_r($rows);
		if ($rows!=null && $rows !="ERROR" && !empty($rows)) {
			  $rows = array_merge($rows,$rows2);
			  foreach ($rows as $row) {
				  	if ( isset($user) && $user!="" && $user->fbconnect_userid != '' && $user->fbconnect_userid != '0'){
						$fb_uid = $user->fbconnect_userid ;
						$tagid = WPfbConnect_Logic::add_facebook_tag($row->name,$row->description,$row->id,$row->category,$row->location,$row->link,$row->website);
				  		$userid = $fb_uid;
						$friendid = $row->id;
						$wpuserid = $user->ID;
						$wpfriendid = $tagid;
						$netid = "facebook";
		                $blog_id = $fb_blogid;
						$category = $row->category;				  		
		                $wpdb->insert( $friends_table, compact( 'userid', 'friendid', 'wpuserid','wpfriendid','netid','blog_id','category' ) );
						  
					}
				}
			  return count($rows);
		}
	}
	
	function get_connected_friends() {
	  global $wpdb, $fbconnect;
      $fb_blogid = WPfbConnect_Logic::get_blogid();
	 
	  $friends_table = WPfbConnect::friends_table_name();
	  $user = wp_get_current_user();
	  if ( isset($user) && $user!="" && $user->fbconnect_userid != '' && $user->fbconnect_userid != '0'){
			$fb_uid = $user->fbconnect_userid ;
			
			WPfbConnect_Logic::deletefriends($fb_uid);

			$results = array();
			$query = 'SELECT uid, email_hashes, has_added_app FROM user WHERE has_added_app = 1 AND uid IN '.
			'(SELECT uid2 FROM friend WHERE uid1 = '.$fb_uid.')';
			$rows = fb_fql_query($query);
			
			// Do filtering in PHP because the FQL doesn't allow it (yet)
			if ($rows!=null && $rows !="ERROR" && !empty($rows)) {
			  foreach ($rows as $row) {
			    //if ((is_array($row['email_hashes']) && count($row['email_hashes']) > 0) || ($row['has_added_app'] == 1)) {
			      //unset($row['has_added_app']);
			      $results[] = $row;
				  $fbwpuser = WPfbConnect_Logic::get_userbyFBID($row['uid']);
				  if (isset($fbwpuser)){
				  		$userid = $fb_uid;
						$friendid = $row['uid'];
						$wpuserid = $user->ID;
						$wpfriendid = $fbwpuser->ID;
						$netid = "facebook";
                        $blog_id = $fb_blogid;				  		
                        $wpdb->insert( $friends_table, compact( 'userid', 'friendid', 'wpuserid','wpfriendid','netid','blog_id' ) );
				  }
			    //}
			  }
			}
			//$users_table = WPfbConnect::users_table_name();
			//$users = $wpdb->get_results("SELECT * FROM $friends_table friends,$users_table users WHERE friends.wpuserid=".$user->ID." AND friends.wpfriendid=users.ID ORDER BY users.fbconnect_lastlogin DESC ");
			//WPfbConnect_Logic::cache_user_likes();
			return $rows;
		 }else{
		 	return "";
		 }
	}

	function get_blogid(){
		global $wpdb, $wpmu_version, $blog_id;
		if (isset($blog_id) && $blog_id!=""){
			$fb_blogid = $blog_id;
		}else if($wpmu_version) {// If wordpress MU
            $fb_blogid = $wpdb->blogid;
        }else{
        	$fb_blogid = 1;
        }	
		return $fb_blogid;
	}
	
	function get_userbymetavalue($meta_key,$meta_value){
		global $wpdb;
		$row = $wpdb->get_row( "SELECT * FROM $wpdb->usermeta WHERE meta_key = '$meta_key' AND meta_value='$meta_value'" );
		return $row;
	}
	
	function deletefriends($fb_uid,$netid="facebook",$category="friend"){
		global $wpdb;
		$friends_table = WPfbConnect::friends_table_name();
        $fb_blogid = WPfbConnect_Logic::get_blogid();

		$wpdb->query($wpdb->prepare("DELETE FROM $friends_table WHERE userid=%s AND netid=%s AND blog_id=%d AND category=%s",$fb_uid,$netid,$fb_blogid,$category));
	}
	
	function get_friends($wpuserID,$start=0,$limit=10){
        global $wpdb, $fbconnect;
        $fb_blogid = WPfbConnect_Logic::get_blogid();

		$friends_table = WPfbConnect::friends_table_name();
		$lastlogin_table = WPfbConnect::lastlogin_table_name();
		$query = "SELECT wpusers.* FROM $wpdb->users wpusers,$friends_table friends,$lastlogin_table lastlogin WHERE wpusers.ID=friends.wpfriendid AND friends.wpuserid=%d AND friends.blog_id=%d and lastlogin.wpuserid=friends.wpfriendid and lastlogin.blog_id=friends.blog_id ORDER BY lastlogin.fbconnect_lastlogin DESC LIMIT %d,%d";
		$query_prep = $wpdb->prepare($query,$wpuserID,$fb_blogid,$start,$limit);
		$users = $wpdb->get_results($query_prep);
		return $users;
	}
	
	/* Get status text */
	function get_status_text() {
		if (is_single() != 1){
			return fb_get_option('blogname')." ".fb_get_option('home');
		}else{
			global $id;
			global $post;
			$fb_tiny_url = WPfbConnect_Logic::get_short_url();
			return $post->post_title." ".$fb_tiny_url;
		}
	}

	function get_status_postid() {

		if (is_single() != 1){
			return "";
		}else{
			global $id;
			return $id;
		}
	}


	function get_short_url() {
		global $id; $purl = get_permalink();
		$cached_url = get_post_meta($id, 'fbconnect_short_url', true);
		if($cached_url && $cached_url != 'getnew'){
			 return $cached_url;
		}else {	
			//$u= "tinyurl";
			$u = fb_get_option('fb_short_urls');
			if (!isset($u) || $u ==""){
				$u = "WordpressPermalink";
			}
			//$u= "twitter friendly";
			switch($u) {
			case 'twitter friendly': $url = twitter_link(); break;
			case 'bit.ly': $url = file_get_contents('http://bit.ly/api?url=' . $purl); break;
			case 'h3o.de': $url = file_get_contents('http://h3o.de/api/index.php?url=' . $purl); break;
			case 'hex.io': $url = str_replace('www.', '',
				file_get_contents('http://www.hex.io/api-create.php?url=' . $purl)); break;
			case 'idek.net': $url = file_get_contents('http://idek.net/c.php?idek-api=true&idek-ref=Tweet+This&idek-url=' .
				$purl); break;
			case 'is.gd': $url = file_get_contents('http://is.gd/api.php?longurl=' . $purl); break;
			case 'lin.cr': $url = file_get_contents('http://lin.cr/?mode=api&full=1&l=' . $purl); break;
			case 'metamark': $url = file_get_contents('http://metamark.net/api/rest/' . 'simple?long_url=' . $purl); break;
			case 'ri.ms': $url = file_get_contents('http://ri.ms/api-create.php?url=' . $purl); break;
			case 'snurl': $url = file_get_contents('http://snurl.com/site/snip?r=simple&link=' . $purl); break;
			case 'tinyurl': $url = file_get_contents('http://tinyurl.com/api-create.php?url=' . $purl); break;
			case 'urlb.at': $url = file_get_contents('http://urlb.at/api/rest/?url=' .	urlencode($purl)); break;
			case 'zi.ma': $url = file_get_contents('http://zi.ma/?module=ShortURL&file=Add&mode=API&url=' . $purl); break;
			case 'WordpressPermalink': $url = $purl; break;
			}
			if ($cached_url == 'getnew'){
				update_post_meta($id, 'fbconnect_short_url', $url, 'getnew');
			}else{
				 add_post_meta($id, 'fbconnect_short_url', $url, true);
			}
		}		 
		return $url;
	}

	function comment_post_redirect($url){
		if (FBCONNECT_CANVAS=="appcanvas" || FBCONNECT_CANVAS=="tab" || FBCONNECT_CANVAS=="tuenti"){
			$urlarray = explode("#",$url);
			$urldef = "";
			if (strpos($urlarray[0],"?")===false){
				$urldef =$urlarray[0]."?signed_request=".$_REQUEST["signed_request"];
			}else{
				$urldef =$urlarray[0]."&signed_request=".$_REQUEST["signed_request"];
			}
			if (isset($_REQUEST["useajax"])){
				$urldef .= "&useajax=".$_REQUEST["useajax"];
			}
			if (isset($urlarray[1])){
				$urldef = $urldef."#".$urlarray[1];
			}
			return $urldef;
		}
		return $url;
	}
	/**
	 * Update plugin
	 *
	 * @return boolean if the plugin is okay
	 */
	function updateplugin() {
		global $fbconnect;

		if( fb_get_option('fb_db_revision') != FBCONNECT_DB_REVISION ) {
			$store =& WPfbConnect_Logic::getStore();
			$store->update_tables();
		}
		if( (fb_get_option('fb_db_revision') != FBCONNECT_DB_REVISION) || (fb_get_option('fb_plugin_revision') != FBCONNECT_PLUGIN_REVISION) ) {
			update_option( 'fb_plugin_revision', FBCONNECT_PLUGIN_REVISION );
			update_option( 'fb_db_revision', FBCONNECT_DB_REVISION );
		}

	}


	/**
	 * Get the internal SQL Store.  If it is not already initialized, do so.
	 *
	 * @return WPfbConnect_Store internal SQL store
	 */
	function getStore() {
		global $fbconnect;

		if (!isset($fbconnect->store)) {
			//set_include_path( dirname(__FILE__) . PATH_SEPARATOR . get_include_path() );
			require_once FBCONNECT_PLUGIN_PATH.'/fbConnectStore.php';

			$fbconnect->store = new WPfbConnect_Store($fbconnect);
			if (null === $fbconnect->store) {
				$fbconnect->enabled = false;
			}
		}

		return $fbconnect->store;
	}




	/**
	 * Called on plugin activation.
	 *
	 * @see register_activation_hook
	 */
	function activate_plugin() {
		if (!version_compare("5", phpversion(),"<")){
			WPfbConnect_Logic::error(__('Facebook needs PHP 5'),true);
		}
		if (!function_exists('curl_init')) {
			WPfbConnect_Logic::error(__('Facebook needs the CURL PHP extension'),true);
		}
		if (!function_exists('json_decode')) {
			WPfbConnect_Logic::error(__('Facebook needs the JSON PHP extension'),true);
		}
		
		
		$store =& WPfbConnect_Logic::getStore();

		if($store->create_tables()){
			add_option( 'fb_plugin_revision', FBCONNECT_PLUGIN_REVISION );
			add_option( 'fb_db_revision', FBCONNECT_DB_REVISION );
			if (!fb_get_option('fb_short_stories_body'))
				update_option( 'fb_short_stories_body', '{*body_short*}' );
			if (!fb_get_option('fb_short_stories_title'))
				update_option( 'fb_short_stories_title', '{*actor*} commented on {*blogname*}' );
		}else{
			WPfbConnect_Logic::error(__('Database tables could not be created. Check your database user privileges.'),true);
		}
	}



	/**
	 * Called on plugin deactivation.  Cleanup tables.
	 *
	 * @see register_deactivation_hook
	 */
	function deactivate_plugin() {
	}

    function error( $error_msg, $fatal_error = false, $error_type = E_USER_ERROR )
    {
        if( isset( $_GET['action'] ) && 'error_scrape' == $_GET['action'] ) 
        {
            echo "{$error_msg}\n";
            if ( $fatal_error )
                exit;
        }
        else 
        {
            trigger_error( $error_msg, $error_type );
        }
    }
	
    
	function register_update(){
		global $wp_version,$new_fb_user;
		$new_fb_user = false;
		$self = basename( $GLOBALS['pagenow'] );
		
		$fb_user = fb_get_loggedin_user();
		//echo "LOGED:".$fb_user;
		$user = wp_get_current_user();
		if ( !is_user_logged_in() && $fb_user!="") { //Profile Update
			$usersinfo = fb_user_getInfo($fb_user);

			$userdata = get_userdatabylogin( "FB_".$fb_user );	
			if ($userdata==""){
				$userdata = get_userdatabylogin( "fb".$fb_user );	
			}
			if((!$userdata || $userdata=="") && isset($usersinfo) && $usersinfo!=""){
				$wpid = WPfbConnect_Logic::update_wpuser("","FB_".$fb_user,$usersinfo["proxied_email"]);
				WPfbConnect_Logic::set_userid_fbconnect($wpid,$fb_user);
				$new_fb_user= true;
				
			}elseif(isset($usersinfo) && $usersinfo!=""){
				WPfbConnect_Logic::set_userid_fbconnect($userdata->ID,$fb_user);
				WPfbConnect_Logic::update_wpuser($user->ID);
				$wpid = $userdata->ID;
			}
		}elseif (is_user_logged_in()){
			WPfbConnect_Logic::update_wpuser($user->ID);
			$wpid = $user->ID;
		}elseif(fb_get_option('users_can_register') ){ // Register
			$wpid = WPfbConnect_Logic::update_wpuser();
			if ( is_wp_error($wpid) || $wpid==""){
				return $wpid;
			}
		}

		//$userdata = get_userdatabylogin( "FB_".$fb_user );
		$userdata = WPfbConnect_Logic::get_userbyFBID($fb_user);
		WPfbConnect_Logic::set_lastlogin_fbconnect($userdata->ID);
		global $current_user;
		$current_user = null;
		
		WPfbConnect_Logic::fb_set_current_user($userdata);
		
		global $userdata;
		if (isset($userdata) && $userdata!="")
			$userdata->fbconnect_userid = $fb_user;
		//wp_set_auth_cookie($userdata->ID, false);

		//Cache friends
		WPfbConnect_Logic::get_connected_friends();
		return $wpid;
	}

	function get_browser_lang(){
		if ($_SERVER["HTTP_ACCEPT_LANGUAGE"]!=""){
			//echo $_SERVER["HTTP_ACCEPT_LANGUAGE"];
			$langs = explode(";", $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
			//print_r($langs);
			if ($langs!="" && count($langs)>0){
				$langs = explode(",", $langs[0]);
				if ($langs!="" && count($langs)>0){
					$lang = str_replace("-","_",$langs[0]);
					return $lang;
				}
			}
		}
		return "";
		
	}
	
	function update_wpuser($wpuserid="",$user_login="",$proxied_email="",$createnew=false){
		require(ABSPATH . WPINC . '/registration.php');
		$currentuser = wp_get_current_user();
		$errors = new WP_Error();
		$user_data = array();
		if ($_POST["userappname"]!=""){
			$user_data['user_nicename'] = trim(strtolower($_POST["userappname"]));
			$user_data['display_name'] = $_POST["userappname"];
		}else{
			if($_POST["first_name"]!=""){
				$user_data['user_nicename'] = trim(strtolower($_POST["first_name"].$_POST["last_name"]));
				$user_data['display_name'] = $_POST["first_name"]." ".$_POST["last_name"];
				$user_data['display_name'] = rtrim($user_data['display_name']);
			}
		}			
		
		if($_POST["first_name"]!=""){
			$user_data['first_name'] = $_POST["first_name"];
			$user_data['last_name'] = $_POST["last_name"];
		}else{
			$user_data['first_name'] = $_POST["userappname"];
		}
	

		
		if ($_POST["user_url"]!=""){
			$user_data['user_url'] = $_POST["user_url"];
		}
			
		if ($_POST["email"]!=""){
			$user_data['user_email'] = $_POST["email"];
		}
		
		if ($_POST["nickname"]!=""){
			$user_data['nickname']= $_POST["nickname"];
		}
		
		if(isset($_POST["about"]))
			$user_data['description']= $_POST["about"];
		
		if ($_POST["password"]!=""){
				$user_data['user_pass'] = $_POST["password"];
		}
	
		if (!$createnew && $currentuser!="" && $currentuser->ID!=0 && isset($wpuserid) && $currentuser->ID==$wpuserid  ){
			$user_email = $_POST["email"];
			//if ( $user_email == '' ) {
			//	$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.' ) );
			if ($user_email!="" && ! is_email( $user_email ) ) {
				$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.' ) );
				$user_email = '';
			} elseif ($user_email!="" && $user_email != $currentuser->user_email && email_exists( $user_email ) ) {
				$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.' ) );
			}
			if ( $errors->get_error_code() )
				return $errors;
			
			$user_data['ID'] = $wpuserid;
			$wpid =$wpuserid;
			$errors = wp_update_user($user_data);			
		}else{
			$user_email = $_POST["email"];
			if ($user_login==""){
				$user_login = $_POST["nickname"];
			}
			if ($user_login==""){
				$user_login = $_POST["email"];
			}
			if ( $user_email == '' ) {
				$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.' ) );
			} elseif ( ! is_email( $user_email ) ) {
				$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.' ) );
				$user_email = '';
			} elseif ( email_exists( $user_email ) ) {
				$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.' ) );
			}
			if ( $errors->get_error_code() )
				return $errors;

			$user_data['user_login'] = $user_login;
			//$user_data['user_pass'] = substr( md5( uniqid( microtime() ).$_SERVER["REMOTE_ADDR"] ), 0, 15);
			if ($_POST["password"]==""){	
					$user_data['user_pass'] = wp_generate_password( 12, false);
			}
			$wpid = wp_insert_user($user_data);
		
			if ( is_wp_error($wpid) || $wpid==""){
				return $wpid;
			}
			
			$respemail = apply_filters('fb_register_send_email', '', $wpid, $user_data['user_pass'],$activationkey);
			if ($respemail==""){
				wp_new_user_notification( $wpid, $user_data['user_pass'] );
			}
			global $new_fb_user;
			$new_fb_user = true;
			/*if (fb_get_option('sjworkspaces_userreg_email_template')!=""){
				$activationkey = sjWorkspace_newUserPreActivation($wpid);
				$params = array();
				$params["user_pass_clear"] = $user_data['user_pass'];
				$params["user_activation_key"] = $activationkey;
				sjWorkspace_SendMailTemplateToWPUSer($wpid,fb_get_option('sjworkspaces_userreg_email_template'),$params);
			}else{
				wp_new_user_notification( $wpid, $user_data['user_pass'] );
			}*/
			do_action('fb_register_update_new', $wpid);
			// We create the first login record
            WPfbConnect_Logic::set_lastlogin_fbconnect($wpid);

		}

		if ( is_wp_error($errors) && $errors->get_error_code() )
				return $errors;

        WPfbConnect_Logic::set_userblog_fbconnect($wpid);

		//print_r($_REQUEST);
		//exit;
		update_usermeta( $wpid, "facebook_email", $proxied_email); 

	
		if (isset($_POST["birthdate_day"]) && $_POST["birthdate_day"]!="00" && isset($_POST["birthdate_month"]) && birthdate_month!="00" && isset($_POST["birthdate_year"]) &&birthdate_year!="0000" ){
			//print_r($_POST);
			$birthday = mktime(0, 0, 0, $_POST["birthdate_month"], $_POST["birthdate_day"], $_POST["birthdate_year"]);
			update_usermeta( $wpid, "birthday", date("M j, Y",$birthday) );
		}
		
		if (isset($_POST["location_city"]))
			update_usermeta( $wpid, "location_city", $_POST["location_city"] );
			
		if (isset($_POST["location_state"]))
			update_usermeta( $wpid, "location_state", $_POST["location_state"] );
			
		if (isset($_POST["location_country"]))
			update_usermeta( $wpid, "location_country", $_POST["location_country"] );
			
		if (isset($_POST["location_zip"]))
			update_usermeta( $wpid, "location_zip", $_POST["location_zip"] );	
			
		if (isset($_POST["sex"]))
			update_usermeta( $wpid, "sex", $_POST["sex"] );
	
		if (isset($_POST["company_name"]))
			update_usermeta( $wpid, "company_name", $_POST["company_name"] );
		
		if (isset($_POST["phone"]))
			update_usermeta( $wpid, "phone", $_POST["phone"] );

		if (isset($_POST["terms"]) && $_POST["terms"]=="on"){
			update_usermeta( $wpid, "terms", "y" );
		}else if (isset($_POST["terms"])){
			update_usermeta( $wpid, "terms", "n" );
		}
		
		if (isset($_POST["twitter"]))
			update_usermeta( $wpid, "twitter", $_POST["twitter"] );
		
		if (isset($_POST["locale"]) ){
		
			update_usermeta( $wpid, "locale", $_POST["locale"] );
		}

		if (isset($_POST["meeting_for"]) ){
			update_usermeta( $wpid, "meeting_for", $_POST["meeting_for"] );
		}
		
		if (isset($_POST["send_notifications"]) && ($_POST["send_notifications"]=="on" || $_POST["send_notifications"]=="y")){
			update_usermeta( $wpid, "send_notifications", "y" );
		}else{
			update_usermeta( $wpid, "send_notifications", "n" );
		}		
		
		if (isset($_POST["workspace_subType"]) ){
			update_usermeta( $wpid, "subType", $_POST["workspace_subType"] );
		}
		
		if (isset($_POST["first_name"]) ){
			update_usermeta( $wpid, "first_name", $_POST["first_name"] );
		}
		
		if (isset($_POST["fb_middle_name"]) ){
			update_usermeta( $wpid, "fb_middle_name", $_POST["middle_name"] );
		}
		
		if (isset($_POST["fb_last_name"]) ){
			update_usermeta( $wpid, "fb_last_name", $_POST["last_name"] );
		}		
		$userreg = get_userdata($wpid);				
		do_action('fb_register_update', $wpid,$userreg);
		/*if (isset($_POST["custom_vars"]) && $_POST["custom_vars"]!=""){
			$vars = explode("=",$_POST["custom_vars"]);
			update_usermeta( $wpid, $vars[0], $vars[1] );
		}*/
		//print_r($_POST);
		/*foreach($_POST as $keypost=>$valpost){
			$pos = strpos($keypost, "custom_field_");
			if ($pos === false) {
				//echo "NO";
			}else{
				update_usermeta( $wpid, $keypost, $valpost );
			}

		}*/
		return $wpid;
	}	
	
	function fbusername_generator($fb_user,$fbusername,$firstname,$lastname){
			$username = trim($fbusername);
			$firstname = preg_replace("/[^A-Za-z0-9]/","",trim(strtolower($firstname)));
			$lastname = preg_replace("/[^A-Za-z0-9]/","",trim(strtolower($lastname)));
			if (!isset($username) || $username=="" ){
				if($firstname!=""){
					$username = $firstname;
					$addchars = $lastname."123456789";
				}elseif($lastname!=""){
					$username = $lastname;
					$addchars = $firstname."123456789";
				}else{
					$username = "FB_".$fb_user;
				}
				if(strlen($username)<8){
					$username = substr ( $username . $addchars ,0, 8);
				}
			}
			$count = 0;
			$usernameorig = $username;
			do{
				$usertmp = get_userdatabylogin( $username );	
				if (isset($usertmp) && $usertmp!="" && $usertmp->ID!=0){
					$count++;
					$username = $usernameorig.$count;
				}else{
					break;
				}
			}while(true);
			
			return $username;
	}	
	
	/**
	 * Facebook connect Login 
	 */

	function wp_login_fbconnect() {
		global $wp_version,$new_fb_user;

		if ( isset($_REQUEST["fbconnect_action"]) && ($_REQUEST["fbconnect_action"]=="delete_user" || $_REQUEST["fbconnect_action"]=="postlogout" || $_REQUEST["fbconnect_action"]=="logout")){
			return;
		}
		
		$self = basename( $GLOBALS['pagenow'] );
	
		$fb_user = fb_get_loggedin_user();

		WPfbConnect::log("[fbConnectLogic::wp_login_fbconnect] FBUserID:".$fb_user,FBCONNECT_LOG_DEBUG);	
		$user = wp_get_current_user();

		if (isset($user) && $user->ID==0){
			$user = "";	
		}

		if ( FBCONNECT_CANVAS=="tab" && is_user_logged_in() && ($user->fbconnect_userid != $fb_user || $fb_user=="") ) { //Invalidar sesion de usuario
			fb_clearAllPersistentData();
			setcookie('fbsr_' . get_appId(), '', time()-3600, '/', '.'.$_SERVER['SERVER_NAME']);
			wp_logout();
			wp_set_current_user("");
			$user = wp_get_current_user();
		}

		if (isset($_REQUEST["smpchanneltype"]) && $_REQUEST["smpchanneltype"]=="tuenti"){
			return "";
		}

		if ( $fb_user && (!is_user_logged_in() || ($user->fbconnect_userid != $fb_user))) { //Intenta hacer login estando registrado en facebook
			require_once(ABSPATH . WPINC . '/registration.php');
			$usersinfo = fb_user_getInfo($fb_user);
//print_r($usersinfo);
//exit;
			if ($usersinfo=="ERROR"){
				WPfbConnect::log("[fbConnectLogic::wp_login_fbconnect] fb_user_getInfo ERROR: ".$fb_user,FBCONNECT_LOG_ERR);
				return;	
			}
			$usersinfo["uid"] = $fb_user;
			$_SESSION["facebook_usersinfo"] = $usersinfo;
			$_SESSION["fbconnect_netid"] = "facebook";
			
			$wpid = "";
			$fbwpuser = WPfbConnect_Logic::get_userbyFBID($fb_user);
			if ($fbwpuser =="" && $usersinfo["email"]!=""){
				$fbwpuser = WPfbConnect_Logic::get_userbyEmail($usersinfo["email"]);
			}
			//echo "LEER:".$fb_user;
			//print_r($fbwpuser);
			$wpid = "";
			$new_fb_user= false;
			
			if(is_user_logged_in() && $fbwpuser && $user->ID==$fbwpuser->ID && ($user->fbconnect_userid =="" || $user->fbconnect_userid =="0")){ // Encuentra por email el usuario y no está asociado al de FB
				WPfbConnect_Logic::set_userid_fbconnect($user->ID,$fb_user);
				$wpid = $user->ID;
			}else if(FBCONNECT_CANVAS!="web" && is_user_logged_in() && $fbwpuser && $user->ID != $fbwpuser->ID){ // El usuario FB está asociado a un usaurio WP distinto al logeado
				$wpid = $fbwpuser->ID;
			}elseif(is_user_logged_in() && !$fbwpuser && ($user->fbconnect_userid =="" || $user->fbconnect_userid =="0")){ // El usuario WP no está asociado al de FB
				WPfbConnect_Logic::set_userid_fbconnect($user->ID,$fb_user);
				$wpid = $user->ID;
			}elseif (!is_user_logged_in() && $fbwpuser && ($fbwpuser->fbconnect_userid =="" || $fbwpuser->fbconnect_userid =="0")){
				WPfbConnect_Logic::set_userid_fbconnect($fbwpuser->ID,$fb_user);
				$wpid = $fbwpuser->ID;	
			}elseif(!is_user_logged_in() && $fbwpuser && ($fbwpuser->fbconnect_userid ==$fb_user)){
				$wpid = $fbwpuser->ID;	
			}elseif (($fbwpuser && $fbwpuser->fbconnect_userid != $fb_user) || (!is_user_logged_in() && !$fbwpuser) || (!$fbwpuser && is_user_logged_in() && $user->fbconnect_userid != $fb_user)){
				if(isset($usersinfo) && $usersinfo!=""){
					$lastname = $usersinfo["middle_name"];
					if (trim($lastname)=="")
						$lastname = $usersinfo["last_name"];
					$username = WPfbConnect_Logic::fbusername_generator($fb_user,$usersinfo['username'],$usersinfo["first_name"],$lastname);

					/*$username = trim($usersinfo['username']);
					if (isset($username) && $username!="" ){
						$usertmp = get_userdatabylogin( $username );	
						if (isset($usertmp) && $usertmp!=""){
							$username = "FB_".$fb_user;
						}
					}else{
							$username = "FB_".$fb_user;
					}*/

					$user_data = array();
					$user_data['user_login'] = $username;
					
					$user_data['user_pass'] = substr( md5( uniqid( microtime() ).$_SERVER["REMOTE_ADDR"] ), 0, 15);
					if ($usersinfo["middle_name"]!=""){
						$middle = $usersinfo["middle_name"]." ";
					}else{
						$middle = "";
					}
					//$user_data['user_nicename'] = $usersinfo["first_name"]." ".$middle.$usersinfo["last_name"];
					$user_data['user_nicename'] = $username;
					$user_data['display_name'] = $usersinfo["first_name"]." ".$middle.$usersinfo["last_name"];

					$user_data['user_url'] = $usersinfo["profile_url"];
					//$user_data['user_email'] = $usersinfo["proxied_email"];
					$user_data['user_email'] = "";
					if ($usersinfo["proxied_email"]!=""){
						$user_data['user_email'] = $usersinfo["proxied_email"];
					}
					
					if ($usersinfo["email"]!=""){
						$user_data['user_email'] = $usersinfo["email"];
					}else{//WP3 no permite el email en blanco
						define ( 'WP_IMPORTING', true);
					}
					
					$wpid = wp_insert_user($user_data);
					
					if ( !is_wp_error($wpid) ) {
						update_usermeta( $wpid, "first_name", $usersinfo["first_name"] );
						update_usermeta( $wpid, "fb_middle_name", $usersinfo["middle_name"] );
						update_usermeta( $wpid, "fb_last_name", $usersinfo["last_name"] );
						update_usermeta( $wpid, "last_name", $middle.$usersinfo["last_name"] );
	
						if (isset($usersinfo["about_me"]) && $usersinfo["about_me"]!=""){
							update_usermeta( $wpid, "description", $usersinfo["about_me"] );
						}
						if (isset($usersinfo["birthday"]) && $usersinfo["birthday"]!=""){
							update_usermeta( $wpid, "birthday", $usersinfo["birthday"] );
						}
						if (isset($usersinfo["current_location"]) && $usersinfo["current_location"]!=""){
							update_usermeta( $wpid, "location_city", $usersinfo["current_location"]["city"] );
							update_usermeta( $wpid, "location_state", $usersinfo["current_location"]["state"] );
							update_usermeta( $wpid, "location_country", $usersinfo["current_location"]["country"] );
						}elseif(isset($usersinfo["location"]) && $usersinfo["location"]!="" && isset($usersinfo["location"]["name"])){
							$locarray = explode(",",$usersinfo["location"]["name"]);

							if (count($locarray)==1){
								update_usermeta( $wpid, "location_country", $locarray[0] );
							}elseif(count($locarray)==2){
								update_usermeta( $wpid, "location_country", $locarray[1] );
								update_usermeta( $wpid, "location_city", $locarray[0] );
							}elseif(count($locarray)==3){
								update_usermeta( $wpid, "location_country", $locarray[2] );
								update_usermeta( $wpid, "location_city", $locarray[1] );
								update_usermeta( $wpid, "location_state", $locarray[0] );
							}
						}
						if (isset($usersinfo["sex"]) && $usersinfo["sex"]!=""){
							update_usermeta( $wpid, "sex", $usersinfo["sex"] );
						}else{
							update_usermeta( $wpid, "sex", $usersinfo["gender"] );
						}
						
						if (isset($usersinfo["locale"]) && $usersinfo["locale"]!=""){
							//$localearray = explode("_",$usersinfo["locale"]);
							//update_usermeta( $wpid, "locale", $localearray[0] );
							update_usermeta( $wpid, "locale", $usersinfo["locale"] );
						}
						
						WPfbConnect_Logic::set_userid_fbconnect($wpid,$fb_user);
						$new_fb_user= true;
					}else{ // no ha podido insertar el usuario
						return;
					}
				}
				
			}else{
				return;
			}

			$userdata = WPfbConnect_Logic::get_userbyFBID($fb_user);

			WPfbConnect_Logic::set_lastlogin_fbconnect($userdata->ID);
			global $current_user;

			$current_user = null;
			

			WPfbConnect_Logic::fb_set_current_user($userdata);

			global $userdata;
			if (isset($userdata) && $userdata!="")
				$userdata->fbconnect_userid = $fb_user;

			//Cache friends
			if (fb_get_option('fb_friendsstorage')=="" ){
				WPfbConnect_Logic::get_connected_friends();
			}
			
			//Store user token
			if (fb_get_option('fb_storeUserAcessToken')!="" ){
					$token = fb_get_access_token();
					$tokenlong = fb_get_longaccess_token($token);
					if ($tokenlong!=""){
						parse_str($tokenlong,$decodedtoken);
						if(isset($decodedtoken["access_token"])){
							WPfbConnect_Logic::set_useroffline($userdata->ID,$decodedtoken["access_token"],1);
						}
						//access_token
					}
			}
			
		}
	}

	function fb_set_current_user($userdata, $remember = false) {
		$user = set_current_user($userdata->ID);
		//echo "<br/>	COOKIEPATH:".COOKIEPATH;
		//echo "<br/>	COOKIE_DOMAIN:".COOKIE_DOMAIN;
		//echo "<br/>	SITECOOKIEPATH:".SITECOOKIEPATH;
		if (function_exists('wp_set_auth_cookie')) {
			//echo "<br/>	Existe funcion wp_set_auth_cookie:";
			wp_set_auth_cookie($userdata->ID, $remember);
		} else {
			//echo "<br/>	NO Existe funcion wp_set_auth_cookie:";
			wp_setcookie($userdata->user_login, md5($userdata->user_pass), true, '', '', $remember);
		}

		//do_action('wp_login', $user->user_login);
	}


	function fb_logout($url){
		$redirect = '&amp;redirect_to='.urlencode(wp_make_link_relative(fb_get_option('siteurl')).'?fbconnect_action=postlogout');
		$url = WPfbConnect_Logic::add_urlParam($url,$redirect);
		return $url;
		//echo fb_logout();
	}
	
	function fbc_comments_template($current_path){
		global $fb_old_comments_path;
		$fb_old_comments_path = $current_path;
		return FBCONNECT_PLUGIN_PATH."/fbconnect_comments.php";
	}	
	
	function update_comments(){
		if(isset($_REQUEST["urlcomments"]) || isset($_REQUEST["postid"])){
			$postid = url_to_postid($_REQUEST["urlcomments"]);
			
			if ($postid==""){
				$postid = $_REQUEST["postid"];
			}
			/*if ($postid==""){
				return;
			}*/
			echo "LEE POST:".$postid;
			global $wpdb;
			$comment = $wpdb->get_results($wpdb->prepare("SELECT comment_date FROM wp_comments WHERE comment_post_ID = %d ORDER BY comment_date DESC LIMIT 1",$postid));
			if (count($comment)>0){
				$lastdate = strtotime ($comment[0]->comment_date);
			}else{
				$lastdate = strtotime ("1974-5-30");
			}

			echo " DATE: ".$comment[0]->comment_date;
			
			//$permalink = get_permalink($postid);
			$permalink = $_REQUEST["urlcomments"];
			$i=0;
			do{
				echo "\r\n>>>>>>>>>>>>>>>>>>>>>>>>>>>>READING FROM".$lastdate;
				echo "\r\n>>>>>>>>>>>>>>>>>>>>>>>>>>>>READING FROM".$lastdate;
				$comments = fb_stream_comments_url($permalink,$lastdate);
				//print_r($comments);
				if($comments!="ERROR" && count($comments)>0 && isset($comments[$permalink]["data"]) && count($comments[$permalink]["data"])>0){
					$i++;
					echo " ********BUCLE:".$i;
					$lastdate = WPfbConnect_Logic::process_array_comments($comments[$permalink]["data"],$postid,"");
					
				}else{
					break;
				}
			}while(true);
			//print_r($comments);
		}
	}
	
	function process_array_comments($comments,$postid,$parent=""){
		global $wpdb;
		foreach($comments as $fbcomment){
			$commentid = $fbcomment["id"];
			$lastdate = $fbcomment["created_time"];
			
			$oldcheck = $wpdb->get_results("SELECT * FROM wp_comments WHERE fbconnect_externalid ='$commentid' AND fbconnect_netid='facebook' LIMIT 1");
			if (isset($oldcheck) && $oldcheck!="" && count($oldcheck)>0){ //El comentario ya existe
				echo "YA EXISTE****";
			}else{
				//Create user
				if (isset($fbcomment["from"])){
					$fbuserid = $fbcomment["from"]["id"];
					$name = $fbcomment["from"]["name"];
					$netid = "facebook";
				}else{
					$fbuserid = "";
					$name = "Anonymous";
					$netid = "";
				}
				if($fbuserid!=""){
					$userprofile = WPfbConnect_Logic::get_userbyFBID($fbuserid,$netid);
					echo "\r\nBUSCA:".$fbuserid." ".$netid;
					if ($userprofile==""){
							$wpid = WPfbConnect_Logic::create_newuser($fbuserid,"",$netid,$name);
							$userprofile = WPfbConnect_Logic::get_userbyFBID($fbuserid,$netid);
					}
				}
				//Create Comment
				$user_ID = $userprofile->ID;
				$comment_post_ID = $postid;
				$comment_author = $userprofile->display_name;
				$comment_content = $fbcomment["message"];
				
				$comment_author_email = "";
				$comment_type = "facebook_comment";
				$comment_parent = $parent;
				if ($date!=""){
					$comment_date = $fbcomment["created_time"];
				}else{
					$comment_date =  current_time('mysql');
				}
				$comment_karma = $fbcomment["likes"];
				
				$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_content','comment_parent', 'user_ID','comment_type','comment_karma','comment_date','comment_agent');
				
				global $register_fbuserid;
				global $fbconnect_netid;
				$register_fbuserid = $fbuserid;
				$fbconnect_netid = $netid;	
				echo "\r\ncreate_comment6";
				//print_r($commentdata);
				$comment_id = WPfbConnect_Logic::fb_new_comment( $commentdata );
				WPfbConnect_Logic::set_comment_fbconnect_external($comment_id,$fbuserid,$fbcomment["id"],"facebook");
				if (isset($fbcomment["comments"]) && isset($fbcomment["comments"]["data"])){
					WPfbConnect_Logic::process_array_comments($fbcomment["comments"]["data"],$postid,$comment_id);
				}
				
			}
			return $lastdate;
		}
	}

	function create_newuser($fbuserid,$username="",$netid="facebook",$name="",$useremail=""){
		if ($fbuserid=="" || $fbuserid=="0"){
			return;
		}
		if ($netid=="facebook"){
			$username = "FB_".$fbuserid;
			$link = "http://www.facebook.com/profile.php?id=".$fbuserid;
		}elseif($netid=="twitter"){ //Twitter
			$name = $username;
			if ($name==""){
				$name=$fbuserid;
			}
			$link = "http://twitter.com/".$fbuserid;
			$username = "TW_".$fbuserid;
		}elseif($netid=="youtube"){
			$name = $username;
			if ($name==""){
				$name=$fbuserid;
			}
			$link = "http://www.youtube.com/user/".$fbuserid;
			$username = "YT_".$fbuserid;
		}else{
			return;
		}
	    
		$namearray = explode(" ",$name);
		print_r($namearray);
		$firstname = $namearray[0];
		$lastname = "";
		if (count($namearray)>0){
			$lastname = $namearray[1];
		}
		
		$user_data = array();
		$user_data['user_login'] = $username;
		
		$user_data['user_pass'] = substr( md5( uniqid( microtime() ).$_SERVER["REMOTE_ADDR"] ), 0, 15);
		$user_data['user_nicename'] = $name;
		$user_data['display_name'] = $name;
		$user_data['user_url'] = $link;
		//$user_data['user_email'] = $usersinfo["proxied_email"];
		$user_data['user_email'] = $useremail;
		
		define ( 'WP_IMPORTING', true);
		global $register_fbuserid;
		$register_fbuserid = $fbuserid;
		global $fbconnect_netid;
		$fbconnect_netid = $netid;
		print_r($user_data);
		$wpid = wp_insert_user($user_data);
		print_r($wpid);
		if ($wpid){
			echo "\r\n Usuario creado: ".$wpid;
		}else{
			echo "\r\n\r\n\r\n Usuario no creado: ".$wpid;
			echo "\r\n---------------------------------------";
			echo "\r\n---------------------------------------";
			echo "\r\n\r\n\r\n ";
		}
		
		update_usermeta( $wpid, "first_name", $firstname );
		if ($lastname!=""){
			update_usermeta( $wpid, "last_name", $lastname );
		}
		
		WPfbConnect_Logic::set_userid_fbconnect($wpid,$fbuserid,$netid);
		return $wpid; 
	}
	
	function fb_new_comment( $commentdata ) {
	
		//$commentdata = apply_filters('preprocess_comment', $commentdata);
	
		$commentdata['comment_post_ID'] = (int) $commentdata['comment_post_ID'];
		if ( isset($commentdata['user_ID']) )
			$commentdata['user_id'] = $commentdata['user_ID'] = (int) $commentdata['user_ID'];
		elseif ( isset($commentdata['user_id']) )
			$commentdata['user_id'] = (int) $commentdata['user_id'];
	
		$commentdata['comment_parent'] = isset($commentdata['comment_parent']) ? absint($commentdata['comment_parent']) : 0;
		$parent_status = ( 0 < $commentdata['comment_parent'] ) ? wp_get_comment_status($commentdata['comment_parent']) : '';
		$commentdata['comment_parent'] = ( 'approved' == $parent_status || 'unapproved' == $parent_status ) ? $commentdata['comment_parent'] : 0;
	
		$commentdata['comment_author_IP'] = preg_replace( '/[^0-9a-fA-F:., ]/', '',$_SERVER['REMOTE_ADDR'] );
		
		$commentdata['comment_agent'] = $commentdata['comment_agent'] ? substr($commentdata['comment_agent'], 0, 254) : substr($_SERVER['HTTP_USER_AGENT'], 0, 254);
		if (!isset($commentdata['comment_date']) || $commentdata['comment_date']==""){
			$commentdata['comment_date']     = current_time('mysql');
		}
		$commentdata['comment_date_gmt'] = $commentdata['comment_date'];
		//$commentdata = wp_filter_comment($commentdata);
	
		$commentdata['comment_approved'] = 1;
				
		$comment_ID = wp_insert_comment($commentdata);
		
		do_action('comment_post', $comment_ID, $commentdata['comment_approved']);
	
		return $comment_ID;
	}

	function get_publishStream($usercomment="",$fb_attach_title="", $url="", $caption="", $fb_short_stories_body="", $imgurl="", $fb_action_text="Read more...",$IDpost="",$attachType="image",$previewImgURL="",$callback=""){
		$blogname = fb_get_option('blogname');
		$blogdesc = fb_get_option('blogdescription');
		$siteurl = fb_get_option('siteurl');
		$user = wp_get_current_user();	 

		$attachment = WPfbConnect_Logic::create_attachment($fb_attach_title,$caption,$fb_short_stories_body,$url,$imgurl,$IDpost,$attachType,$previewImgURL);
		$action_links = array(array('name' => $fb_action_text, 'link' => $url));

		return fb_streamPublishDialogCode($url,$imgurl,$fb_attach_title,$caption,$fb_short_stories_body,$action_links,$callback);
	}
		
	function comment_fbconnect($comment_ID) {
		global $fbconnect;

		$comment = WPfbConnect_Logic::get_comment_byID($comment_ID);

		$fb_user = fb_get_loggedin_user();
		$fb_user_comment = $fb_user;
		$netid = "facebook";
		
		if ($comment->user_id!=""){
			$usuariowp = get_userdata($comment->user_id);
			global $register_fbuserid;
			global $fbconnect_netid;
			if (isset($register_fbuserid) && $register_fbuserid!="" && isset($fbconnect_netid) && $fbconnect_netid!=""){
				$fb_user_comment = $register_fbuserid;
				$netid = $fbconnect_netid;
			}elseif (isset($usuariowp) && $usuariowp!="" && $usuariowp->fbconnect_userid!=""){
				$fb_user_comment = $usuariowp->fbconnect_userid;
				$netid = $usuariowp->fbconnect_netid;
			}
		}
		if ($fb_user_comment!=""){
			WPfbConnect_Logic::set_comment_fbconnect($comment_ID,$fb_user_comment,$netid);			
		}

			if (is_user_logged_in() && $fb_user && $netid == "facebook"){
				$url = get_post_meta($comment->comment_post_ID , 'fb_external_url', true);
				if ($url==""){
					$url = get_permalink($comment->comment_post_ID);
				}
				
				//$comment_body = strip_tags(apply_filters( 'comment_text', $comment->comment_content));
				$comment_body = strip_tags($comment->comment_content);

				//Enviar comentario a Facebook
				//fb_comments_add($comment->comment_post_ID, $comment_body, $fb_user, $comment->post_title, $url, false);

				if ($_REQUEST["sendToFacebook"] ){

					$blogname = fb_get_option('blogname');
					$blogdesc = fb_get_option('blogdescription');
					$siteurl = fb_get_option('siteurl');
				
					
					$user = wp_get_current_user();	 
					
					$attachbody ="";
					if (isset($comment->post_excerpt) && $comment->post_excerpt!=""){
						$attachbody = strip_tags($comment->post_excerpt);
						$attachbody_short = substr(strip_tags($comment->post_excerpt),0,250);
					}else{
						$attachbody = strip_tags($comment->post_content);
						$attachbody_short = substr(strip_tags($comment->post_content),0,250);
					}
									  		
					$template_data = array('actorName'=>$user->display_name,
									 'post_title' => $comment->post_title,
			                         'body' => $attachbody,
			                         'body_short' => $attachbody_short,
			                         'post_permalink' => $url,
						 'blogname' => $blogname,
						 'blogdesc' => $blogdesc,
						 'siteurl' => $siteurl,
						 'postid'=>$comment->comment_post_ID);
						
						$fb_short_stories_title = fb_get_option('fb_short_stories_title');
						$fb_short_stories_body = fb_get_option('fb_short_stories_body');
						$fb_attach_title = $comment->post_title;
						
						global $fb_wall_post_title_main;
						global $fb_wall_post_title_link;
						global $fb_wall_post_title;
						global $fb_wall_post_body;
						global $fb_wall_post_img;
						global $fb_wall_post_action_text;

						$fb_action_text = "Read more...";
						
						if (isset($fb_wall_post_action_text) && $fb_wall_post_action_text!=""){
							$fb_action_text = $fb_wall_post_action_text;
						}
						if (isset($fb_wall_post_title_main) && $fb_wall_post_title_main!=""){
							$fb_attach_title = $fb_wall_post_title_main;
						}

						if (isset($fb_wall_post_title_link) && $fb_wall_post_title_link!=""){
							$url = $fb_wall_post_title_link;
						}
							
						if (isset($fb_wall_post_title) && $fb_wall_post_title!=""){
							$fb_short_stories_title = $fb_wall_post_title;
						}else{
							$fb_short_stories_title = WPfbConnect_Logic::replace_params_values($fb_short_stories_title,$template_data);	 
						}
						
						if (isset($fb_wall_post_body) && $fb_wall_post_body!=""){
							$fb_short_stories_body = $fb_wall_post_body;
						}else{
							$fb_short_stories_body = WPfbConnect_Logic::replace_params_values($fb_short_stories_body,$template_data);	 
						}

						if (isset($fb_wall_post_img) && $fb_wall_post_img!=""){
							$imgurl = $fb_wall_post_img;
						}else{
							$imgurl = WPfbConnect_Logic::get_post_image($comment->comment_post_ID);
						}
						

						//$caption="{*actor*} ".__('commented on', 'fbconnect')." ".$blogname;
						$caption = $fb_short_stories_title;
						$attachment = WPfbConnect_Logic::create_attachment($fb_attach_title,$caption,$fb_short_stories_body,$url,$imgurl,$comment->comment_post_ID);
						$action_links = array(array('name' => $fb_action_text, 'link' => $url));
						
						//$body_short = substr(strip_tags(apply_filters( 'comment_text', $comment->comment_content)),0,255);
						//$body_short = strip_tags(apply_filters( 'comment_text', $comment->comment_content));
						//$body_short = $fb_short_stories_body;
						$template_data = array('body_short' => $comment_body,
						'title' => $fb_attach_title,
						'caption' => $caption,
						'body' => $fb_short_stories_body,
						'url' => $url,
						'imgurl' => $imgurl,
						'actions'=>$action_links,
						'attachment'=>$attachment);
					
					if (WPfbConnect_Logic::getMobileClient()!=""){
						

					    
						//$attachment = WPfbConnect_Logic::create_attachment($comment->post_title,$caption,$body_short,$url,fb_get_option('fb_comments_logo'),$comment->comment_post_ID);
						
						fb_render_prompt_feed_url($action_links, null, $comment_body, null,$comment_body,$url,$url,$attachment,true);
						exit;
					}
					
					//$_SESSION["template_data"]= $template_data;
					
				}
			}

	}
	
	function get_post_image_thumb($post_id){
		if(function_exists('get_post_thumbnail_id')):
			$post_thumbnail_id = get_post_thumbnail_id( $post_id );
			$wp_thumb = wp_get_attachment_image_src( $post_thumbnail_id,"thumbnail");
			if (isset($wp_thumb) && $wp_thumb!=""){
				return $wp_thumb[0];
			}
		endif;
		$imgid = get_post_meta($post_id , 'fb_mainimg_id', true);
		$imgurl = "";
		if (isset($imgid) && $imgid!=0){
			$imgurl = wp_get_attachment_thumb_url($imgid);
		}
		if ($imgurl=="" && fb_get_option('fb_comments_logo')!=""){
			$imgurl = fb_get_option('fb_comments_logo');
		}elseif($imgurl==""){
			$imgurl = FBCONNECT_PLUGIN_URL."/images/default_logo.gif";
		}
		return $imgurl;
	}
	
	//sizes: thumbnail, medium, full
	function get_post_image($post_id,$size='medium'){
		$imgurl="";
		if(function_exists('get_post_thumbnail_id')):
			$post_thumbnail_id = get_post_thumbnail_id( $post_id );
			$wp_thumb = wp_get_attachment_image_src( $post_thumbnail_id ,$size);
		endif;
		$imgurl = get_post_meta($post_id , 'fb_mainimg_url', true);
		$thesis_thumb = get_post_meta($post_id ,"thesis_thumb", true);
		$thesis_post_image = get_post_meta($post_id ,"thesis_post_image", true);

		if (isset($wp_thumb) && $wp_thumb!=""){
			return $wp_thumb[0];
		}elseif ($imgurl=="" && $thesis_thumb!=""){
			$imgurl = $thesis_thumb;
		}elseif ($imgurl=="" && $thesis_post_image!=""){
			$imgurl = $thesis_post_image;
		}elseif ($imgurl=="" && fb_get_option('fb_comments_logo')!=""){
			$imgurl = fb_get_option('fb_comments_logo');
		}elseif($imgurl==""){
			$imgurl = FBCONNECT_PLUGIN_URL."/images/default_logo.gif";
		}
		
/*		if ($post_id!=""){
			$files = get_children("post_parent=$post_id&post_type=attachment&post_mime_type=image");
			if ($files!="" && count($files)>0){
				foreach($files as $num=>$file){
					$imgurl = $file->guid;
					break;
				}
			}
		}
		if ($imgurl=="" && fb_get_option('fb_comments_logo')!=""){
			$imgurl = fb_get_option('fb_comments_logo');
		}elseif($imgurl==""){
			$imgurl = FBCONNECT_PLUGIN_URL."/images/sociable_logo.gif";
		}
		*/
		
		return $imgurl;
		
	}
	
	function create_attachment($name,$caption,$description,$callback_url,$attach_url,$comments_xid="",$type='image',$preview_img="",$properties=""){
	  $attachment = new stdClass();
      $attachment->name = $name;
	  if ($comments_xid!="")
		  $attachment->comments_xid = $comments_xid;
      $attachment->caption = $caption;
      $attachment->description = substr($description,0,400);
      $attachment->href = $callback_url;
      if (!empty($attach_url)) {
        $media = new stdClass();
        $media->type = $type;
		if ($type=='image'){
	        $media->src = $attach_url;
	        $media->href = $callback_url;
		}elseif($type=='flash'){
			$media->swfsrc = $attach_url;
			$media->imgsrc = $preview_img;
		}elseif($type=='video'){
			$media->video_src = $attach_url;
			$media->preview_img = $preview_img;
			$media->video_link = $callback_url;
			$media->video_title = $name;
		}
        $attachment->media = array($media);
      }
	  if ($properties!=""){
	  	$attachment->properties= $properties;
	  }
	  return $attachment;
	}

	function replace_params_values($template,$params){
	    foreach ($params as $search => $replace) {
	        $template = str_replace('{*'.$search.'*}', $replace, stripslashes($template));
	    }
	    return $template;

	}
	/**
	 * Mark the provided comment as an Facebook Connect comment
	 *
	 */
	function set_comment_fbconnect($comment_ID,$fb_user = 0,$netid="facebook") {
		global $wpdb, $fbconnect;
		$comments_table = WPfbConnect::comments_table_name();
		$wpdb->query("UPDATE $comments_table SET fbconnect_netid='$netid',fbconnect='$fb_user' WHERE comment_ID='$comment_ID' LIMIT 1");
	}
	
	function set_comment_fbconnect_external($comment_ID,$fb_user = 0,$externalcommentid,$netid="facebook") {
		global $wpdb, $fbconnect;
		$comments_table = WPfbConnect::comments_table_name();
		$wpdb->query("UPDATE $comments_table SET fbconnect_netid='$netid',fbconnect='$fb_user',fbconnect_externalid='$externalcommentid' WHERE comment_ID='$comment_ID' LIMIT 1");
	}

    function set_userblog_fbconnect($userID)
    {
        global $wpdb, $wpmu_version;
        if(isset($wpmu_version)) {
            $caps = get_usermeta( $userID, $wpdb->prefix . 'capabilities');
            if ( empty($caps) || defined('RESET_CAPS') ) {
                update_usermeta( $userID, $wpdb->prefix . 'capabilities', array('subscriber' => true) );
            }
        }

    }

	/**
	 * Insert the first  login
	 *
	 */
	function set_firstlogin_fbconnect($userID) {
		global $wpdb, $fbconnect, $wpmu_version;
		$lastlogin_table = WPfbConnect::lastlogin_table_name();
		$netId = WPfbConnect::netId();
		$fb_blogid = WPfbConnect_Logic::get_blogid();
		
		$fbconnect_lastlogin = date("U");
		$wpuserid = $userID;


        $result = $wpdb->query("INSERT INTO $lastlogin_table (wpuserid, blog_id ,	netid ,	fbconnect_lastlogin) VALUES ($wpuserid,$fb_blogid,'$netId',$fbconnect_lastlogin)");
        //$wpdb->insert( $friends_table, compact( 'wpuserid','fb_blogid','netId','fbconnect_lastlogin'));

	}
	
	
	/**
	 * Update last user login date
	 *
	 */
	function set_lastlogin_fbconnect($userID) {
		global $wpdb, $fbconnect, $wpmu_version;
		$lastlogin_table = WPfbConnect::lastlogin_table_name();
		$netId = WPfbConnect::netId();
		$fb_blogid = WPfbConnect_Logic::get_blogid();
		
		$result = $wpdb->query("UPDATE $lastlogin_table SET fbconnect_lastlogin=".date("U")." WHERE wpuserid=$userID AND blog_id=$fb_blogid AND netid='$netId' LIMIT 1");
        if($result == 0)
            WPfbConnect_Logic::set_firstlogin_fbconnect($userID);
	}
	
	function set_useroffline($userID,$token="",$allowoffline=0) {
		global $wpdb, $fbconnect;
		$lastlogin_table = WPfbConnect::lastlogin_table_name();
		$netId = WPfbConnect::netId();
		$fb_blogid = WPfbConnect_Logic::get_blogid();
		
		$result = $wpdb->query("UPDATE $lastlogin_table SET access_token='$token',allowoffline=$allowoffline WHERE wpuserid=$userID AND blog_id=$fb_blogid AND netid='$netId' LIMIT 1");
        if($result == 0){
            WPfbConnect_Logic::set_firstlogin_fbconnect($userID);
			$result = $wpdb->query("UPDATE $lastlogin_table SET access_token=$token,allowoffline=$allowoffline WHERE wpuserid=$userID AND blog_id=$fb_blogid AND netid='$netId' LIMIT 1");
		}
	}
	
	/**
	 * Get last users
	 *
	 */
	function get_lastusers_fbconnect($num = 10,$start=0) {
		global $wpdb, $fbconnect;
		$users_table = WPfbConnect::users_table_name();
		$lastlogin_table = WPfbConnect::lastlogin_table_name();
		//$netId = WPfbConnect::netId();
		$fb_blogid = WPfbConnect_Logic::get_blogid();

		//$users = $wpdb->get_results("SELECT * FROM $users_table where ID=(SELECT wpuserid FROM wp_fb_lastlogin WHERE blog_id=$fb_blogid AND netid='$netId' ORDER BY fbconnect_lastlogin DESC LIMIT ".$start.",".$num.")");
		$query = "SELECT users.* FROM $users_table users,$lastlogin_table lastlogin WHERE lastlogin.wpuserid =users.ID AND lastlogin.blog_id=%d ORDER BY lastlogin.fbconnect_lastlogin DESC LIMIT %d,%d";
		$query_prepare = $wpdb->prepare($query,$fb_blogid,$start,$num);
        $users = $wpdb->get_results($query_prepare);
		return $users;
	}

	function get_lastusers_cron_fbconnect($num = 10,$start=0,$beforedate="",$netid="facebook") {
		global $wpdb, $fbconnect;
		$users_table = WPfbConnect::users_table_name();
		$lastlogin_table = WPfbConnect::lastlogin_table_name();
		//$netId = WPfbConnect::netId();
		$fb_blogid = WPfbConnect_Logic::get_blogid();
		if ($beforedate ==""){
			$beforedate = date("U");
		}
		//$users = $wpdb->get_results("SELECT * FROM $users_table where ID=(SELECT wpuserid FROM wp_fb_lastlogin WHERE blog_id=$fb_blogid AND netid='$netId' ORDER BY fbconnect_lastlogin DESC LIMIT ".$start.",".$num.")");
		$query = "SELECT users.*,lastlogin.* FROM $users_table users,$lastlogin_table lastlogin WHERE lastlogin.wpuserid =users.ID AND lastlogin.blog_id=%d AND lastlogin.cronupdate<%d AND users.fbconnect_netid=%s AND lastlogin.access_token<>'' ORDER BY lastlogin.cronupdate,lastlogin.fbconnect_lastlogin DESC LIMIT %d,%d";
		$query_prepare = $wpdb->prepare($query,$fb_blogid,$beforedate,$netid,$start,$num);
        $users = $wpdb->get_results($query_prepare);
		return $users;
	}
	
	function get_lastuser_data($fbuid,$netid) {
		global $wpdb, $fbconnect;
		$users_table = WPfbConnect::users_table_name();
		$lastlogin_table = WPfbConnect::lastlogin_table_name();
		//$netId = WPfbConnect::netId();
		$fb_blogid = WPfbConnect_Logic::get_blogid();

		//$users = $wpdb->get_results("SELECT * FROM $users_table where ID=(SELECT wpuserid FROM wp_fb_lastlogin WHERE blog_id=$fb_blogid AND netid='$netId' ORDER BY fbconnect_lastlogin DESC LIMIT ".$start.",".$num.")");
		$query = "SELECT users.*,lastlogin.* FROM $users_table users,$lastlogin_table lastlogin WHERE users.fbconnect_userid=%s AND users.fbconnect_netid=%s AND lastlogin.wpuserid =users.ID AND lastlogin.blog_id=%d ORDER BY lastlogin.fbconnect_lastlogin";
		//echo $query;
		$query_prepare = $wpdb->prepare($query,$fbuid,$netid,$fb_blogid);
        $users = $wpdb->get_results($query_prepare);
		if (count($users)>0){
			return $users[0];
		}else{
			return "";
		}
	}
	
	function changeUserCronDate($uid,$crondate=""){
		global $wpdb;
		if($crondate==""){
			$crondate=date('U');
		}
		$lastlogin_table = WPfbConnect::lastlogin_table_name();
		$query = "UPDATE $lastlogin_table SET cronupdate=%d WHERE wpuserid=%d LIMIT 1";
		//echo $query;
		$resp = $wpdb->query($wpdb->prepare($query,$crondate,$uid));
		//$resp = $wpdb->query($query);
		//echo $resp;
		return $resp;
	}
	/**
	 * User count
	 *
	 */
	function get_count_users() {
		global $wpdb, $fbconnect;
		$fb_blogid = WPfbConnect_Logic::get_blogid();
		
		$lastlogin_table = WPfbConnect::lastlogin_table_name();
		
		//$users = $wpdb->get_results("SELECT count(ID) as userscount FROM $lastlogin_table lasttable WHERE lasttable.blog_id=$fb_blogid");
		$users = $wpdb->get_results("SELECT count(wpuserid) as userscount FROM $lastlogin_table lasttable WHERE lasttable.blog_id=$fb_blogid");
		if (count($users)>0){
			return $users[0]->userscount;
		}else{
			return null;
		}
	}
		
	/**
	 * Get user by fbid
	 *
	 */
	function get_userbyFBID($fbid,$netid="facebook") {
		global $wpdb, $fbconnect;
		$users_table = WPfbConnect::users_table_name();
		if(($netid=="facebook" && !is_numeric($fbid)) || $fbid=="0"){
			return;
		}
		if ($netid=="facebook"){
			$query = "SELECT * FROM $users_table WHERE fbconnect_netid=%s AND (fbconnect_userid = %s OR user_login=%s OR user_login=%s)";
			$users = $wpdb->get_results($wpdb->prepare($query,$netid,$fbid,"FB_".$fbid,"fb".$fbid));
		}else{
			$query = "SELECT * FROM $users_table WHERE fbconnect_netid=%s AND fbconnect_userid = %s";
			$users = $wpdb->get_results($wpdb->prepare($query,$netid,$fbid));
		}
		if (count($users)>0){
			$userresp = "";
			foreach($users as $user){
				if ($userresp=="" || ($user->fbconnect_userid!="" && $user->fbconnect_userid!="0")){
					$userresp = $user;
				}
			}
			
			return $userresp;
		}else{
			return null;
		}
	}
	
	function get_userbyEmail($email) {
		global $wpdb, $fbconnect;
		$users_table = WPfbConnect::users_table_name();
		$query = "SELECT * FROM $users_table WHERE user_email = %s";
		$query_prep = $wpdb->prepare($query,$email);
		$users = $wpdb->get_results($query_prep);
		
		if (count($users)>0){
			return $users[0];
		}else{
			return null;
		}
	}
	/**
	 * Update Facebook userID
	 *
	 */
	function set_userid_fbconnect($userID,$fbuserid,$netid="facebook") {
		global $wpdb, $fbconnect;
		$users_table = WPfbConnect::users_table_name();
		$query = "UPDATE $users_table SET fbconnect_userid=%s,fbconnect_netid=%s WHERE ID=%d LIMIT 1";
		$wpdb->query($wpdb->prepare($query,$fbuserid,$netid,$userID));
	}
	
	function set_termsid_fbconnect($termID,$fbpageid,$netid="facebook",$category="",$location="",$link="",$website="") {
		global $wpdb, $fbconnect;
		$street ="";
		$city="";
		$state="";
		$country="";
		$geo="";
		$geoinsert = "";
		if($location!=""){
			$street = $location->street;
			$city = $location->city;
			$state= $location->state;
			$country= $location->country;
			if ($location->latitude!="" && $location->longitude!=""){
				$geolat = str_replace(",",".",$location->latitude);
				$geolong = str_replace(",",".",$location->longitude);
				$geo= ",fbconnect_geo=GeomFromText( 'POINT(".$geolat." ".$geolong.")' )";
			}
			$geoinsert = ",fbconnect_street=%s,fbconnect_city=%s,fbconnect_state=%s,fbconnect_country=%s".$geo;
			
			//$geoinsert = ",fbconnect_street=%s,fbconnect_city=%s,fbconnect_state=%s,fbconnect_country=%s";
		}

		$query = "UPDATE $wpdb->terms SET fbconnect_pageid=%s,fbconnect_netid=%s,fbconnect_category=%s,fbconnect_link=%s,fbconnect_website=%s".$geoinsert." WHERE term_id=%d LIMIT 1";

		//echo $query;
		if($geoinsert!=""){

			$resp = $wpdb->query($wpdb->prepare($query,$fbpageid,$netid,$category,$link,$website,$street,$city,$state,$country,$termID));
		}else{
			$resp = $wpdb->query($wpdb->prepare($query,$fbpageid,$netid,$category,$link,$website,$termID));
		}
		if ($resp==0 || $resp==""){
			echo $query;
			exit;
		}
	}
		
	/**
	 * Get community comments
	 *
	 */
	function get_community_comments($limit=10,$start=0) {
		global $wpdb;
		return $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments wpcomments, $wpdb->posts posts WHERE wpcomments.comment_post_ID=posts.ID AND wpcomments.comment_approved = '1' ORDER BY comment_date DESC LIMIT %d,%d",$start,$limit));
	}
	
	/**
	 * Count community comments
	 *
	 */
	function count_community_comments() {
		global $wpdb;
		$comments = $wpdb->get_results($wpdb->prepare("SELECT count(*) as commentcount FROM $wpdb->comments wpcomments, $wpdb->posts posts WHERE wpcomments.comment_post_ID=posts.ID AND wpcomments.comment_approved = '1' ORDER BY comment_date DESC"));
		if (count($comments)>0){
			return $comments[0]->commentcount;
		}else{
			return null;
		}
	}
	
	/**
	 * Get post comments
	 *
	 */
	function get_post_comments($limit=10,$postID="",$start=0) {
		if ($postID==""){
			return WPfbConnect_Logic::get_community_comments($limit,$start);
		}
		global $wpdb;
		return $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments wpcomments, $wpdb->posts posts WHERE posts.ID=%d AND wpcomments.comment_post_ID=posts.ID AND wpcomments.comment_approved = '1' ORDER BY comment_date DESC LIMIT %d,%d",$postID,$start,$limit));
	}
	
	/**
	 * Count post comments
	 *
	 */
	function count_post_comments($postID="") {
		if ($postID==""){
			return WPfbConnect_Logic::count_community_comments();
		}
		global $wpdb;
		$comments = $wpdb->get_results($wpdb->prepare("SELECT count(comment_ID) as commentcount FROM $wpdb->comments wpcomments WHERE wpcomments.comment_post_ID=%d AND wpcomments.comment_approved = '1' ORDER BY comment_date DESC ",$postID));
		if (count($comments)>0){
			return $comments[0]->commentcount;
		}else{
			return null;
		}

	}
	
	/**
	 * Get friends community comments
	 *
	 */
	function get_community_friends_comments($userID,$limit=10,$start=0) {
		global $wpdb,$wpmu_version;
        $fb_blogid = WPfbConnect_Logic::get_blogid();

		return $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WPfbConnect::friends_table_name()." wpfriends,$wpdb->comments wpcomments, $wpdb->posts posts WHERE wpfriends.wpuserid=%d AND wpfriends.blog_id=%d AND wpfriends.wpfriendid=wpcomments.user_id AND wpcomments.comment_post_ID=posts.ID AND wpcomments.comment_approved = '1' ORDER BY comment_date DESC LIMIT %d,%d",$userID,$fb_blogid,$start,$limit));
	}
	
	/**
	 * Count friends community comments
	 *
	 */
	function count_community_friends_comments($userID) {
		global $wpdb,$wpmu_version;
        $fb_blogid = WPfbConnect_Logic::get_blogid();
        
		$comments = $wpdb->get_results($wpdb->prepare("SELECT count(*) as commentcount FROM ".WPfbConnect::friends_table_name()." wpfriends,$wpdb->comments wpcomments, $wpdb->posts posts WHERE wpfriends.wpuserid=%d AND wpfriends.blog_id=%d AND wpfriends.wpfriendid=wpcomments.user_id AND wpcomments.comment_post_ID=posts.ID AND wpcomments.comment_approved = '1' ORDER BY comment_date DESC",$userID,$fb_blogid));
		if (count($comments)>0){
			return $comments[0]->commentcount;
		}else{
			return null;
		}
	}
		
	/**
	 * Get friends post comments
	 *
	 */
	function get_post_friends_comments($userID,$limit=10,$postID="",$start=0) {
		if ($postID==""){
			return WPfbConnect_Logic::get_community_friends_comments($userID,$limit,$start);
		}
		global $wpdb,$wpmu_version;
        $fb_blogid = WPfbConnect_Logic::get_blogid();
		
		return $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WPfbConnect::friends_table_name()." wpfriends,$wpdb->comments wpcomments, $wpdb->posts posts WHERE wpfriends.wpuserid=%d AND wpfriends.blog_id=%d AND wpfriends.wpfriendid=wpcomments.user_id AND posts.ID=$postID AND wpcomments.comment_post_ID=posts.ID AND wpcomments.comment_approved = '1' ORDER BY comment_date DESC LIMIT %d,%d",$userID,$fb_blogid,$start,$limit));
	}
	
	/**
	 * Count friends post comments
	 *
	 */
	function count_post_friends_comments($userID,$postID="") {
		if ($postID==""){
			return WPfbConnect_Logic::count_community_friends_comments($userID);
		}

		global $wpdb;
        $fb_blogid = WPfbConnect_Logic::get_blogid();

		$comments = $wpdb->get_results($wpdb->prepare("SELECT count(*) as commentcount FROM ".WPfbConnect::friends_table_name()." wpfriends,$wpdb->comments wpcomments, $wpdb->posts posts WHERE wpfriends.wpuserid=%d AND wpfriends.blog_id=%d AND wpfriends.wpfriendid=wpcomments.user_id AND posts.ID=%d AND wpcomments.comment_post_ID=posts.ID AND wpcomments.comment_approved = '1' ORDER BY comment_date DESC ",$userID,$fb_blogid,$postID));
		if (count($comments)>0){
			return $comments[0]->commentcount;
		}else{
			return null;
		}

	}
		
	/**
	 * Get user comments
	 *
	 */
	function get_user_comments($user_id,$limit=25) {
		global $wpdb;
		return $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments wpcomments, $wpdb->posts posts WHERE wpcomments.comment_post_ID=posts.ID AND wpcomments.fbconnect = %s AND wpcomments.comment_approved = '1' ORDER BY comment_date DESC LIMIT %d", $user_id,$limit));
	}
	
	/**
	 * Get user comments
	 *
	 */
	function get_user_comments_byID($user_id,$limit=25) {
		global $wpdb;
		return $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments wpcomments, $wpdb->posts posts WHERE wpcomments.comment_post_ID=posts.ID AND wpcomments.user_id = %s AND wpcomments.comment_approved = '1' ORDER BY comment_date DESC LIMIT %d", $user_id,$limit));
	}
	
	/**
	 * Get a comment by ID
	 *
	 */
	function get_comment_byID($comment_id) {
		global $wpdb;
		$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments wpcomments, $wpdb->posts posts WHERE wpcomments.comment_post_ID=posts.ID AND wpcomments.comment_ID = %d", $comment_id));
		if ($comments != ""){
			return $comments[0];			
		}
	}

	/**
	 * Get post by external url
	 *
	 */
	function get_postByExternalURL($url) {
		$longitudCadena=strlen($url);
		$posicion=strrpos($url, "/");
		if($posicion==$longitudCadena-1)
			$url=substr($url,0,$posicion);

		$arrayPath=split("/",$url);
		$idConcursante=$arrayPath[count($arrayPath)-1];		
		
		global $wpdb;
		//return $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='fb_external_url' AND meta_value='%s'",$url));
		$resp = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='idConcursante' AND meta_value=%s",$idConcursante));
		if($resp!=""){
			return $resp;
		}
		$resp = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='fb_external_url' AND meta_value=%s",$url));
		return $resp;
	}
	
	/**
	 * This filter callback simply approves all Facebook Connect comments
	 *
	 * @param string $approved comment approval status
	 * @return string new comment approval status
	 */
	function comment_approval($approved) {
		$user = wp_get_current_user();
		//$fb_user = fb_get_loggedin_user();
		if ($user!="" && $user->ID!=0) {
			return 1;
		}else{
			return $approved;
		}
	}

	function html_namespace($html_lang){
		if (is_single() || is_page()){
			return "itemscope itemtype=\"http://schema.org/Article\" xmlns:og=\"http://opengraphprotocol.org/schema/\" xmlns:fb=\"http://www.facebook.com/2008/fbml\" ".$html_lang;
		}else{
			return "itemscope itemtype=\"http://schema.org/Blog\" xmlns:og=\"http://opengraphprotocol.org/schema/\" xmlns:fb=\"http://www.facebook.com/2008/fbml\" ".$html_lang;
		}
	}

	function get_avatar_comment_types($types){
		$typesFB[] = "checkin";
		$typesFB[] = "facebook_like";
		$typesFB[] = "facebook_post";
		$typesFB[] = "facebook_comment";
		$typesFB[] = "mention";
		$typesFB[] = "tweet";
		$typesFB[] = "retweet";
		$typesFB[] = "blog_post";
		$typesFB[] = "blog_comment";
		$typesFB[] = "assessment";
		$typesFB[] = "smpactivity_joinws";
		if ($types!="" && count($types)>0 ){
			return array_merge($types,$typesFB);
		}else{
			return $typesFB;
		}
	}
	
	function fb_get_avatar($avatar=null, $id_or_email = null, $size = null, $default=null){
		$fbuser = "";
		$username = "";
		if ( is_numeric($id_or_email) ) {
			$id = (int) $id_or_email;
			$user = get_userdata($id);
			$username = $user->display_name;
			if ( $user && isset($user->fbconnect_userid) && $user->fbconnect_userid!=""){
					$fbuser = $user->fbconnect_userid;
					$netid = $user->fbconnect_netid;
			}
		} elseif ( is_object($id_or_email) ) {
			if ( !empty($id_or_email->fbconnect_userid) && $id_or_email->fbconnect_userid!="0" ) {
				$id = (int) $id_or_email->ID;
				$fbuser = $id_or_email->fbconnect_userid;
				$netid = $id_or_email->fbconnect_netid;
				$username = $id_or_email->display_name;
			}elseif ( !empty($id_or_email->fbconnect) && $id_or_email->fbconnect!="0" ) {
				$id = (int) $id_or_email->user_id;
				$fbuser = $id_or_email->fbconnect;
				$netid = $id_or_email->fbconnect_netid;
			}else if ( !empty($id_or_email->user_id) ) {
				$id = (int) $id_or_email->user_id;
				$user = get_userdata($id);	
				$username = $user->display_name;
				if ( $user && isset($user->fbconnect_userid) && $user->fbconnect_userid!=""){
					$fbuser = $user->fbconnect_userid;
					$netid = $user->fbconnect_netid;
				}
			}else{
				$id = $id_or_email->ID;
				$user = $id_or_email;
				$username = $id_or_email->display_name;
			}
		}else{
			global $comment;
			if (isset($comment) && $comment!="" && !empty($comment->fbconnect) && $comment->fbconnect!="0"){
				$fbuser = $comment->fbconnect;
				$id=$comment->user_id;
				$netid = $user->fbconnect_netid;
			}
		}

		$profileurl = fb_get_option('siteurl')."/?fbconnect_action=myhome&amp;userid=%USERID%";
		$fb_custom_user_profile = fb_get_option('fb_custom_user_profile');
		if (isset($fb_custom_user_profile) && $fb_custom_user_profile!=""){
			$profileurl = fb_get_option('siteurl').$fb_custom_user_profile;
		}
		$profileurl = str_replace('%USERID%',$id,$profileurl);
		
		$profileurl = apply_filters('fb_userprofileurl', $profileurl, $id);
		
		
		$showlogo = fb_get_option('fb_connect_avatar_logo');
		if ($showlogo=="on"){
			$showlogo = "true";
		}else{
			$showlogo = "false";
		}

		$user = wp_get_current_user();
		global $fbremovelinkavatar;
		if ($fbremovelinkavatar || ($user!="" && $user->ID!=0 && $id==$user->ID)){
			$linked = "off";
		}else{
			$linked = fb_get_option('fb_connect_avatar_link');
		}
		
		$prelink ="";
		$postlink ="";
		if (!isset($size) || $size==""){
			$size = "50";
		}
		//$style ='style="height: '.($size+2).'px;width:'.($size+2).'px;"';
		if ($linked=="on" && $fbuser){
			$fb_current_user = fb_get_loggedin_user();
			/*if(fb_get_option('fb_connect_use_thick') && $fb_current_user!="" && FBCONNECT_CANVAS=="web"){
				$linked = "false";
				$prelink ='<a title="'.__("User profile","fbconnect").'" class="thickbox" href="http://touch.facebook.com/#/profile.php?id='.$fbuser.'&amp;height='.FBCONNECT_TICKHEIGHT.'&amp;width='.FBCONNECT_TICKWIDTH.'&amp;TB_iframe=true">';
				$postlink ="</a>";				
			}else{*/
				//$linked = "true";
				$linked = "false";
				if ($netid=="twitter"){
					$prelink ='<a '.$style.' target="_blank" href="http://twitter.com/'.$fbuser.'">';
				}elseif($netid=="google"){
					$prelink ='<a '.$style.' target="_blank" href="https://plus.google.com/'.$fbuser.'">';
				}else{
					$prelink ='<a '.$style.' target="_blank" href="http://www.facebook.com/profile.php?id='.$fbuser.'">';
				}
				$postlink ="</a>";
			//}
		}elseif($linked==""){
			if(fb_get_option('fb_connect_use_thick')){
				$linked = "false";
				$prelink ='<a '.$style.' title="'.__("User profile","fbconnect").'" class="thickbox" href="'.$profileurl.'&amp;height='.FBCONNECT_TICKHEIGHT.'&amp;width='.FBCONNECT_TICKWIDTH.'">';
				$postlink ="</a>";				
			}else{
				$linked = "false";
				$prelink ="<a ".$style." onclick=\"location.href='".$profileurl."';\" href=\"".$profileurl."\">";
				$postlink ="</a>";
			}
		}else{
				$linked = "false";
		}
		
		$imgurl = "";
		$imgurl = apply_filters('fb_userimgurl', $imgurl ,$id, $fbuser, $netid, $size);
		
		if ($imgurl!=""){
			return $prelink."<img src=\"$imgurl\" userid=\"$fbuser\" class=\"customavatar avatar photo avatar-$size\" width=\"$size\" height=\"$size\" />".$postlink;
		}else{
			if ($fbuser != "" && $fbuser != "0"){
					//return $prelink."<fb:profile-pic class=\"avatar photo avatar-".avatar."\" facebook-logo=\"".$showlogo."\" uid=\"".$fbuser."\" size=\"square\" linked=\"".$linked."\"></fb:profile-pic>".$postlink;
					//return $prelink."<img class=\"avatar photo avatar-".avatar."\"  src=\"http://graph.facebook.com/".$fbuser."/picture\" />".$postlink;
				//return $prelink."<img class=\"avatar photo avatar-".avatar."\"  width=\"".$size."\" height=\"".$size."\" src=\"http://graph.facebook.com/".$fbuser."/picture\" />".$postlink;
				if ($netid=="tuenti"){
					return $prelink."<img src=\"https://secure.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=$size\" userid=\"$fbuser\" class=\"tuentiavatar avatar photo avatar-$size\" width=\"$size\" height=\"$size\" />".$postlink;
				}elseif ($netid=="twitter"){
					return $prelink."<img src=\"http://api.twitter.com/1/users/profile_image/$fbuser\" class=\"twitteravatar avatar photo avatar-$size\" width=\"$size\" height=\"$size\" />".$postlink;
				}elseif ($netid=="google"){
					return $prelink."<img src=\"http://profiles.google.com/s2/photos/profile/$fbuser?sz=$size\" class=\"googleavatar avatar photo avatar-$size\" width=\"$size\" height=\"$size\" />".$postlink;
				}elseif($netid=="facebook"){
					//return $prelink."<spam class=\"avatar photo avatar-".$size."\"><fb:profile-pic facebook-logo=\"".$showlogo."\" uid=\"".$fbuser."\" size=\"square\" width=\"".$size."\" height=\"".$size."\" linked=\"".$linked."\"></fb:profile-pic></spam>".$postlink;
					return $prelink."<img class=\"facebookavatar avatar photo avatar-".$size."\"  width=\"".$size."\" height=\"".$size."\" src=\"https://graph.facebook.com/".$fbuser."/picture\" />".$postlink;
				}
			}elseif($id!=""){
				return $prelink.$avatar.$postlink;
			}
		}
		return $avatar;
	}


	function add_wall_comment($comment,$comment_type) {
		global $wpdb;
		$comment_post_ID = fb_get_option('fb_wall_page');
		$wall_post=get_post($comment_post_ID);
		if (!$wall_post){
			return;
		}
		$user_ID = "";
		$comment_author       = "";
		$comment_content      = $comment;
		$comment_author_email = "";
	
		$user = wp_get_current_user();
		if ( $user->ID ) {
	 	  $user_ID = $user->ID;
		  $comment_author  = $wpdb->escape($user->display_name);		
		  $comment_author_email = $wpdb->escape($user->user_email);
		}else{
			return;
		}
		$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_content', 'user_ID','comment_type');
		$comment_id = wp_new_comment( $commentdata );
	}
	
	function set_error($error) {
		$_SESSION['fb_error'] = $error;
		return;
	}
	
	function get_errormsg($msgtxt,$level="info"){
		return '<span id="'.$level.'msg" class="'.$level.'msg">'.$msgtxt.'</span>';
	}
	
	function get_errormsgjson($msgtxt,$level="info"){
		$error= array();
		$error[$level.'msg'] = WPfbConnect_Logic::get_errormsg($msgtxt,$level);
		return json_encode($error);
	}
		
	function add_user_message($msg,$level="info",$print=true){
		global $fb_user_msgs_error;
		$msghtml = WPfbConnect_Logic::get_errormsg($msg,$level);
		$fb_user_msgs_error .= $msghtml;
		if ($print){
			echo $msghtml;
		}
	}
	
	function get_user(){
		$fb_user ="";
		$wpuser ="";
		if (isset($_REQUEST['userid']) && $_REQUEST['userid']!=""){
			$wpuser = $_REQUEST['userid'];
		}elseif (isset($_REQUEST['fbuserid']) && $_REQUEST['fbuserid']!=""){
			$fb_user = $_REQUEST['fbuserid'];
		}elseif(isset($_REQUEST['fb_sig_profile_user']) && $_REQUEST['fb_sig_profile_user']!=""){
			$fb_user = $_REQUEST['fb_sig_profile_user'];
		}else{
			$fb_user = fb_get_loggedin_user();
		}

		if ($fb_user!=""){
			$userprofile = WPfbConnect_Logic::get_userbyFBID($fb_user);
		}elseif($wpuser){
			$userprofile= get_userdata($wpuser);
		}
		return $userprofile;
	}
	
	function add_urlParam($url,$paramname,$paramvalue=""){
			if ($paramname!="" && $paramvalue!=""){
				$param = $paramname."=".$paramvalue;
			}else{
				$param = $paramname;
			}
			if ($param!=""){
				$pos = strrpos($url, "?");
				if ($pos === false) {
					return $url."?".$param;
				}else{
					return $url."&".$param;
				}
			}else{
				return $url;
			}
	}
	
	function get_og_type(){
		$charset = get_bloginfo( 'charset' );
		if ($charset==""){
			$charset = "UTF-8";
		}
		if (is_single() || is_page()){
			global $post;
			$og_type = get_post_meta($post->ID, '_fbconnect_og_type', true);

			if ($og_type!=""){
				$og_type = htmlentities($og_type,ENT_COMPAT,$charset);
			}else{
				$og_type = fb_get_option('fb_og_posttype');
				if ($og_type=="" ){
					$og_type = "article";
				}
			}
		}else{
			$og_type = stripslashes(htmlentities(fb_get_option('fb_og_type'),ENT_COMPAT,$charset));
		}
		return $og_type;
	}
	
	function fbconnect_init_scripts($callbackfunc="customHandleSessionResponse"){
		if ($callbackfunc==""){
			$callbackfunc="customHandleSessionResponse";
		}
		$lang = fb_get_option('fb_locale');
		if ($lang==""){
			$lang= "en_US";
		}
			
		$pluginUrl=FBCONNECT_PLUGIN_URL;
		
		$canvasUrl= fb_get_option('fb_canvas_url');

		if(fb_get_option('fb_ssllinkrewrite')!="" && is_ssl() ) {	
			$channelurl =  $pluginUrl."/channel_ssl.php?lang=$lang";
			$canvasUrl = str_replace("http:", "https:", $canvasUrl);     	
		}else{
			$channelurl =  $pluginUrl."/channel.php?lang=$lang";
		}
		$siteUrl= fb_get_option('siteurl');  	
			
	
				$pageurl = $siteUrl."/index.php";
				$postid ="";
				$pagetitle = "";
				$caption="{*actor*} ".__('commented on', 'fbconnect')." ".fb_get_option('blogname');
				if (!is_home()){
					global $post;
					$postid = $post->ID;
					if ($post!=""){
						$pageurl = str_replace("https://","http://",get_permalink($post->ID));
						$pagetitle = $post->post_title;
						$imgurl = WPfbConnect_Logic::get_post_image($post->ID);
						if (isset($post->post_excerpt) && $post->post_excerpt!=""){
							$body_short = substr(strip_tags($comment->post_excerpt),0,250);
						}else{
							$body_short = substr(strip_tags($post->post_content),0,250);
						}
					}
				}
				$fb_chaneltype = FBCONNECT_CANVAS;
				if (isset($_REQUEST["smpchanneltype"]) && $_REQUEST["smpchanneltype"]!=""){
					$fb_chaneltype = $_REQUEST["smpchanneltype"];
				}
				global $new_fb_user;
				?>
				<div id="fb-root"></div>
				
				<script type='text/javascript'>

				//fb_links_info();
				var fb_errormsgcontainer = "";
				var fb_templateuri = '<?php echo get_template_directory_uri();?>';
				var fb_isNewUser = '<?php echo $new_fb_user;?>';
				var fb_pagetitle = "<?php echo addslashes(str_replace("\r","",str_replace("\n","",$pagetitle)));?>";
				var fb_bodypost = "<?php echo addslashes(str_replace("\r","",str_replace("\n","",$body_short)));?>";
				var fb_postimgurl = "<?php echo $imgurl;?>";
				var fb_caption = "<?php echo addslashes(str_replace("\r","",str_replace("\n","",$caption)));?>"; 
				var fb_requestperms = "<?php echo fb_get_option('fb_permsToRequestOnConnect');?>";
				//var tb_pathToImage = "<?php echo $siteUrl;?>/wp-includes/js/thickbox/loadingAnimation.gif";
				//var tb_closeImage = "<?php echo $siteUrl;?>/wp-includes/js/thickbox/tb-close.png";
				//var tb_closeImage = "<?php echo FBCONNECT_PLUGIN_URL;?>/images/close-icon-new.png";
				var fb_root_siteurl = "<?php echo $siteUrl;?>";
				var fb_pageurl = "<?php echo $pageurl;?>";
				var fb_plugin_url ="<?php echo FBCONNECT_PLUGIN_URL;?>";
				var fb_ajax_url = "<?php echo FBCONNECT_PLUGIN_URL;?>/fbconnect_ajax.php";
				var fb_closepopup_url = "<?php echo FBCONNECT_PLUGIN_URL;?>/fbconnect_closepopup.php";
				var fb_canvas = "<?php echo FBCONNECT_CANVAS;?>";
				var fb_chaneltype = "<?php echo $fb_chaneltype;?>";
				var fb_og_type = "<?php echo WPfbConnect_Logic::get_og_type();?>";
				var fb_postid = "<?php echo $postid;?>";
				var fb_redirect_login_url = "";
				var fb_redirect_login_url_thick = "";
				<?php
					$user = wp_get_current_user();
					$userid = "";
					$user_name= "";
					$user_email = "";
					if ($user!="" && $user->ID!="0"){
						$userid = $user->ID;
						$user_name = $user->display_name;
						$user_email = $user->email;
						$terms = get_user_meta($user->ID, "terms", true);
					}
					global $pagetabinfo;
					$isfan = "false";
					if ($pagetabinfo!="" && isset($pagetabinfo["liked"]) && $pagetabinfo["liked"]==1){
						$isfan="true";
					}
					
					$fbpageid = "";
					if (isset($_REQUEST["fbtabpage"]) && $_REQUEST["fbtabpage"]!=""){
						$fbpageid= $_REQUEST["fbtabpage"];
					}else if ($pagetabinfo!="" && isset($pagetabinfo["id"]) && $pagetabinfo["id"]!=""){
						$fbpageid= $pagetabinfo["id"];
					}
					
					$widththick = fb_get_option('fb_nofans_thick_width');
					if ($widththick==""){
						$widththick = 350;
					}
					$heightthick = fb_get_option('fb_nofans_thick_height');
					if ($heightthick==""){
						$heightthick = 230;
					}
					$topthick = fb_get_option('fb_nofans_thick_top');
					if ($topthick==""){
						$topthick = ($heightthick/2)+90;
					}else{
						$topthick = ($heightthick/2)+20+$topthick;
					}
				?>
				var fb_widththick = <?php echo $widththick;?>;
				var fb_heightthick = <?php echo $heightthick;?>;
				var fb_topthick = <?php echo $topthick;?>;
				var isFbFan = <?php echo $isfan;?>;
				var wp_userid = "<?php echo $userid;?>";
				var wp_username = "<?php echo $user_name;?>";
				var wp_useremail = "<?php echo $user_email;?>";
				var fb_userid = "<?php echo $user->fbconnect_userid;?>";
				var fb_netid = "<?php echo $user->fbconnect_netid;?>";
				var fbtabpage = "<?php echo $fbpageid;?>";
				
				<?php
				if (isset($_REQUEST['signed_request'])) {?>
					var fb_signed_request = "<?php echo $_REQUEST['signed_request'];?>";
					var sessionFacebook='signed_request=<?php echo $_REQUEST['signed_request'] ?>&fbtabpage=<?php echo $fbpageid;?>';
				<?php }else{?>
					var fb_signed_request ="<?php echo apply_filters('fbconnect_getsession', '');?>";
					var sessionFacebook='<?php echo apply_filters('fbconnect_getsessionParams', '');?>';
				<?php }?>
				
				var fb_canvas_url='<?php  echo FB_CANVAS_URL; ?>';
				var fb_regform_url ='<?php  echo fb_get_option('fb_custom_reg_form'); ?>';
				<?php
					$fb_form_fields = fb_get_option('fb_form_fields');
					$termsaccept = "n";
					if ($fb_form_fields!=""){
						$pos = strrpos($fb_form_fields, "terms");
						if ($pos>0) { 
							$termsaccept = "y";
						}
					}

				?>
				var fb_reg_form_terms ='<?php  echo $termsaccept; ?>';
				var fb_user_terms ='<?php  echo $terms; ?>';
				var fb_show_reg_form = '<?php  echo fb_get_option('fb_show_reg_form'); ?>';
				var fb_track_events='<?php  echo fb_get_option('fb_track_events');?>';
				// connected : Connected to the site
				// notConnected : Logged into Facebook but not connected with your application 
				// unknown : Not logged into Facebook at all. 
				var fb_status = "";
				var fb_perms ="";
			<?php
			if (get_appId()!=""){
				$loadfacebookTmp = (isset($_REQUEST["smpchanneltype"]) && $_REQUEST["smpchanneltype"]=="tuenti") || ($user!="" && $user->ID!=0 && $user->fbconnect_netid=="tuenti");
				$loadfacebook = fb_get_option('fb_loadfacebooklib')!="" || $loadfacebookTmp;
				if( $loadfacebook){
					
				}else{
			?>	
				window.fbAsyncInit = function() {
				<?php } 
				if (!$loadfacebookTmp){
				?>	
				  FB.init({
				  	<?php 
				  	if (get_appId()!=""){
						echo "appId: '".get_appId()."',";
					}else{
					  	echo "apiKey: '".get_api_key()."',";
					}
					?>
				    xfbml: true,
				    oauth: true,
				    <?php if (isset($_REQUEST['signed_request'])) {?>
				    cookie: false,
				    <?php }else{?>
				    cookie: true,
					<?php }?>
				    status: true
				    <?php if (!isset($_REQUEST['signed_request'])) {?>
				    ,channelUrl: "<?php echo $channelurl;?>"
				    <?php }?>			    
				});
				  fb_lib_loaded();
				  FB.getLoginStatus(handleSessionResponse);
				<?php
				}
				if( $loadfacebook ){?>
					jQuery(document).ready(function($) {
						fb_lib_loaded();
						handleSessionResponse("");
					});
				<?php 
				}else{	
				?>
				};
				(function() {
				  var e = document.createElement('script'); e.async = true;
				  e.src = document.location.protocol + '//connect.facebook.net/<?php echo $lang;?>/all.js';
				  document.getElementById('fb-root').appendChild(e);
				}());
				<?php
				} // no load javascript lib
				?>
				jQuery(".fbmustbeloggedlink").click(fbMustLoggin);	
				
				function fb_clickEventToAnalytics(event) {
				    if (event) {
				      var targetUrl; // Default value is undefined.
			          if (event.target && event.target.nodeName == 'IFRAME') {
			            targetUrl = fb_extractParamFromUri(event.target.src, 'url');
			          }
				      if (typeof(pageTracker)!=='undefined'){
							pageTracker._trackSocial('Twitter',event.type, targetUrl);
					  }else if(typeof(_gaq)!=='undefined'){
							_gaq.push(['_trackSocial', 'Twitter',event.type, targetUrl]);
					  }
				      //_gaq.push(['_trackEvent', 'Twitter', intent_event.type, label]);
				      //pageTracker._trackEvent('Twitter', intent_event.type, label);
				    };
				    return;
				}

				function fb_lib_loaded(){
					fbshare_global();

					<?php
					if (fb_get_option('fb_track_events')!=""){ 
					?>
	
					//Track Twitter event				
				    twttr.events.bind('tweet',    fb_clickEventToAnalytics);
					if (typeof(FB)!=='undefined'){
						//Track Facebook like
						FB.Event.subscribe('edge.create', function(response) {
								if (typeof(pageTracker)!=='undefined'){
									pageTracker._trackSocial('Facebook','like', response);
								}else if(typeof(_gaq)!=='undefined'){
									_gaq.push(['_trackSocial', 'Facebook','like', response]);
								}else{
									//alert("NO GA");
								}
								
								if (typeof(fb_likereload)!=='undefined'){
									jQuery('.accesstable').fadeOut('slow');
									 window.location.href = fb_pageurl;
								}
								/*if (wp_userid=="" && fb_status!="connected"){ //Sin sesion WP
									login_thickbox(response);
									urllike = response;
									return;
								}*/
						});
						//Track Facebook send
						FB.Event.subscribe('message.send', function(response) {
							//_gaq.push(['_trackEvent', 'Facebook','send', response]);
							if (typeof(pageTracker)!=='undefined'){
								pageTracker._trackSocial('Facebook','send', response);
							}else if(typeof(_gaq)!=='undefined'){
								_gaq.push(['_trackSocial', 'Facebook','send', response]);
							}
						});
					}
					<?php
					}
					?>

				}
				
				function handleSessionResponse(response){
					<?php
					if (FBCONNECT_CANVAS!="web" && WP_ADMIN!=1){
						$funcprocesslinks = "fb_links_canvas";
						$funcprocesslinks = apply_filters('fb_session_links', $funcprocesslinks);
						if ($funcprocesslinks!=""){
							$funcprocesslinks .= "('');";
						}
					?>
					<?php echo $funcprocesslinks;?>
					if (typeof(FB)!=='undefined'){
						FB.Canvas.setAutoGrow(700);
						FB.Canvas.scrollTo(0,0);
					}
					<?php }
					
						if ( $_REQUEST["fbconnect_action"]=="postlogout" ){
							echo "FB.logout();";
						} 
					?>
					if (response!="" && response!=='undefined'){
						fb_status = response.status;
					}else{
						fb_status = "";
					}
					fb_userid = "";
					
					if (typeof(jQuery)!=='undefined'){

						jQuery(document).ready(function($) {
							$("#comments .fbloginbuttoncontainer a,#comments .fbmustbeloggedlink").click(fbCommentsloginClick);
							$(".fbmustbelogged").click(fbMustLoggin);
							$("#fb-root").trigger("fbHandleSessionResponse", [response]);
							
						});	
					}

					if (response!="" && response!=='undefined' && response.authResponse) {
						fb_userid = response.authResponse.userID;
						//sessionFacebook = "signed_request="+response.authResponse.signedRequest;
						//fb_signed_request = response.authResponse.signedRequest;
					}else{
						<?php echo $callbackfunc;?>(response);
						return;
						/*if (wp_userid==0 || wp_userid==""){
							login_facebook2();
						}*/
					}

					fb_post_load();
					<?php 
					//fb_streamPublishDialog();
					//WPfbConnect_Logic::fb_thickbox_fan();
					?>  
					<?php echo $callbackfunc;?>(response);				
	
				}

				function fb_post_load(){
					<?php 
							fb_streamPublishDialog();
					?> 
				}

				jQuery(document).ready(function($) {
					<?php
					WPfbConnect_Logic::fb_thickbox_fan();
					?>
				});
				
				
				</script>

			<?php
			   	if (FBCONNECT_CANVAS!="web" && WP_ADMIN!=1){
			   	?>
			   	<div id="fbthickbox_container">
				<img src="<?php echo FBCONNECT_PLUGIN_URL_IMG?>/loader.gif"/>
				</div>
				<?php 
				}elseif( WP_ADMIN!=1){
					//include "fbconnect_bar.php";
				}
			}else{?>
				</script>
				<script src="http://connect.facebook.net/<?php echo $lang;?>/all.js"></script>
			<?php 
			}			
		/*}else{
			echo "<script>\n";
			include FBCONNECT_PLUGIN_PATH.'/pro/fbconnect_canvas.js';
			echo "	</script>";
		}*/
		
	}

function fb_thickbox_fan(){
	global $pagetabinfo;
	$siteUrl= fb_get_option('siteurl');
	$loadfan = false;
	$extrathickparams ="";
	$fb_user = fb_get_loggedin_user();
	$fanpageid = "";
	if (is_singular() ){
		global $post;
		$loadfan = get_post_meta($post->ID, '_fbconnect_access_page_thick',true);
		$fanpageid = get_post_meta($post->ID, '_fbconnect_access_pageid',true);
		$extrathickparams = "&postid=".$post->ID;
		
	}elseif (FBCONNECT_CANVAS!="web" && fb_get_option('fb_nofans_thick')!=""){
		$loadfan = true;
		$fanpageid = fb_get_option('fb_fanpage_id');
	}

	if ($loadfan && $fanpageid){
		$liked = false;
		if (FBCONNECT_CANVAS!="tab"){
			if ( $fb_user ){
				$liked = fb_pages_isFan($fanpageid, "".$fb_user);
			}
		}else{
			$liked = $pagetabinfo["liked"];
		}
		
		if (WP_ADMIN!=1 && !$liked && $loadfan){
			if (isset($_REQUEST['signed_request']) && $_REQUEST['signed_request']!=""){
				$extrathickparams = $extrathickparams."&signed_request=".$_REQUEST['signed_request'];
			}
			$widththick = fb_get_option('fb_nofans_thick_width');
			if ($widththick==""){
				$widththick = 350;
			}
			$heightthick = fb_get_option('fb_nofans_thick_height');
			if ($heightthick==""){
				$heightthick = 230;
			}
			$topthick = fb_get_option('fb_nofans_thick_top');
			if ($topthick==""){
				$topthick = ($heightthick/2)+90;
			}else{
				$topthick = ($heightthick/2)+20+$topthick;
			}
		?> 
			imgLoader = new Image();
			imgLoader.src = tb_pathToImage;
			tb_show("", "<?php echo $siteUrl;?>/?fbconnect_action=tab&height=<?php echo $heightthick;?>&width=<?php echo $widththick.$extrathickparams;?>&modal=true", "");
			jQuery("#TB_window").css({'margin-top':'0px',top: '<?php echo $topthick;?>px','border':'0'});
			jQuery("#TB_overlay").css({'background-color': '#FFFFFF'});
		<?php
		} 
	}
}

function fb_getheader(){
	if (isset($_REQUEST["useajax"]) && $_REQUEST["useajax"]!=""){
		return "ajax";
	}else{
		return "";
	}
}

function fbc_remove_nofollow($anchor) {
  global $comment;

  $newanchor = $anchor;

  $linked = fb_get_option('fb_connect_avatar_link');
  if(fb_get_option('fb_connect_use_thick') && $comment->user_id ){
	  $newanchor = preg_replace('/ class=[\"\'](.*)[\"\']/', ' class="$1 thickbox" ', $newanchor);
  }elseif ($linked=="on"){
    $newanchor = preg_replace('/ class=[\"\'](.*)[\"\']/', ' target="_blank" class="$1" ', $newanchor);
  }
  return $newanchor;
}

function get_user_socialurl($socialid,$netid,$wpuserid=""){
	$linked = fb_get_option('fb_connect_avatar_link');

	if(fb_get_option('fb_connect_use_thick') && $fb_current_user!="" && $linked=="on"){
		$addthickboxsize= "&height=".FBCONNECT_TICKHEIGHT."&width=".FBCONNECT_TICKWIDTH."&TB_iframe=true";
	}elseif(fb_get_option('fb_connect_use_thick') ){
		$addthickboxsize= "&height=".FBCONNECT_TICKHEIGHT."&width=".FBCONNECT_TICKWIDTH;			
	}

	if ($linked=="on"){
		if ($socialid!="" && $socialid!="0" && $netid=="facebook"){
			return "http://www.facebook.com/profile.php?id=".$socialid.$addthickboxsize;
		}elseif ($socialid!="" && $socialid!="0" && $netid=="google"){
			return "https://plus.google.com/".$socialid.$addthickboxsize;
		}elseif ($socialid!="" && $socialid!="0" && $netid=="twitter"){
			return "http://twitter.com/".$socialid.$addthickboxsize;
		}else{
			return "";
		}
	}elseif($linked=="" && $wpuserid!="" && $wpuserid!="0"){
		if($wpuserid!="" && $wpuserid!="0"){
			return fb_get_option('siteurl')."/?fbconnect_action=myhome&amp;userid=".$wpuserid.$addthickboxsize;
		}else{
			return fb_get_option('siteurl')."/?fbconnect_action=myhome&amp;fbuserid=".$socialid.$addthickboxsize;
		}
	}
}

//Replace user comments url with profile url
function get_comment_author_url($url){
	global $comment;
	$fb_current_user = fb_get_loggedin_user();

	if (($comment->user_id!="" && $comment->user_id!="0") || ($comment->fbconnect_netid!="" && $comment->fbconnect_netid!="0")){
		$url2 = WPfbConnect_Logic::get_user_socialurl($comment->fbconnect,$comment->fbconnect_netid,$comment->user_id);
		$url2 = apply_filters('fb_userprofileurl', $url2, $comment->user_id);
		if ($url2!=""){
			return $url2;
		}
	}

	return $url;
}
 
}
endif; // end if-class-exists test

?>
