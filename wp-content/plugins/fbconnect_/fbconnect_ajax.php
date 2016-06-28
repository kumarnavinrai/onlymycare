<?php
error_reporting(E_ALL & ~E_DEPRECATED);
require_once("../../../wp-config.php");

nocache_headers();

require_once("../../../wp-includes/comment-template.php");

//print_r($_REQUEST);
global $new_fb_user;
global $fb_ajaxcall;
$fb_ajaxcall = true;

$wall_page = fb_get_option('fb_wall_page');
$fb_show_reg_form = fb_get_option('fb_show_reg_form');
$fb_connect_use_thick = fb_get_option('fb_connect_use_thick');
$user = wp_get_current_user();
//print_r($user);
$fb_user = fb_get_loggedin_user();
$show_refresh = true;

if (isset($_REQUEST["fbpostid"])){
	$fbpostid = $_REQUEST["fbpostid"];
	global $post;
	$post = get_post($fbpostid);
}

if (isset($_REQUEST['fbajaxregister'])){
	if($user!="" && $user->ID!=0){
		$userid = WPfbConnect_Logic::update_wpuser($user->ID);
	}else{
		$userid = WPfbConnect_Logic::register_update();
	}
	if ( !is_wp_error($userid) ) {
		//echo "OK";
		if ($new_fb_user){
			$userdata = get_userdata($userid);
			$_SESSION["fb_registerednewuser"] = $userdata;
		}
		
		if (isset($_REQUEST['returnuserid'])){
			echo $userid;
			exit;
		}
		
		if ($new_fb_user){
		?>
		<span id="fbwelcometitle" class="dialogtitle"> <?php _e("Thanks! You're almost done.", "fbconnect");?></span>
		<span id="fbwelcomebody" class="dialogtext"> <?php
		_e("We've sent you an email from which to confirm your suscription.", "fbconnect");
			?></span>
		<?php }else{?>
		<span id="fbprofiledatamodified" class="dialogtext"> <?php _e("Your profile data has been updated.", "fbconnect");?></span>
		<?php
		}
	}else{
		echo WPfbConnect_Logic::get_errormsg($userid->get_error_message(),"error");
		exit;
	}
	
	exit;
}

if (isset($_REQUEST['fbajaxlogin'])){
	if ($_REQUEST['fbajaxlogin']=="wordpress"){
		if ($_REQUEST["log"]=="" || $_REQUEST["pwd"]==""){
			echo WPfbConnect_Logic::get_errormsg(__("Please, enter your user name and password","fbconnect"),"error");
			exit;
		}
		$user = get_user_by("login", $_REQUEST["log"]);
		if ($user!="" && $user->ID!="" && $user->user_status==2){
			echo WPfbConnect_Logic::get_errormsg("ERROR: User account not activated, please review your email to activate","error");
			exit;
		}
		$secure_cookie = true;
		if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
			$secure_cookie = false;
		
		$user = wp_signon('', $secure_cookie);
		
		if ( !is_wp_error($user) ) {
		//echo "OK";
		}else{
			echo WPfbConnect_Logic::get_errormsg($user->get_error_message(),"error");
			exit;
		}
	}

	$fb_form_fields = fb_get_option('fb_form_fields');
	$termsaccept = "n";
	if ($fb_form_fields!=""){
		$pos = strrpos($fb_form_fields, "terms");
		if ($pos>0) { 
			$termsaccept = "y";
		}
	}
	
	if (isset($user) && $user->ID!=0){
		$terms = get_user_meta($user->ID, "terms", true);
	}			
//$userprofile = WPfbConnect_Logic::get_userbyFBID($fb_user);
if (isset($user) && $user->ID!=0 && ($new_fb_user || ($termsaccept=="y" && $terms!="y")) && $fb_show_reg_form!="" && $fb_connect_use_thick==""){
	$profileurl = fb_get_option('siteurl')."/?fbconnect_action=myhome&amp;userid=%USERID%";
	$fb_custom_reg_form = fb_get_option('siteurl').fb_get_option('fb_custom_reg_form');
	if (isset($fb_custom_reg_form) && $fb_custom_reg_form!=""){
		$profileurl = fb_get_option('siteurl').$fb_custom_reg_form;
	}
	$profileurl = str_replace('%USERID%',$user->ID,$profileurl);
	//echo "<script type='text/javascript'>\n";
	//echo "window.location.href= '".$fb_custom_reg_form."';\n";
	//echo "</script>";
}else if(isset($user) && $user->ID!=0 && ($new_fb_user || ($termsaccept=="y" && $terms!="y")) && $fb_connect_use_thick!=""){
	if (isset($_REQUEST["showcommentform"]) && 	$_REQUEST["showcommentform"]=="true"){
		comment_form("",$fbpostid);
	}else{
		include("fbconnect_widget.php");
	}
?>
<script type='text/javascript'>
	tb_show("Registration", "<?php echo fb_get_option('siteurl') . "?fbconnect_action=register&height=390&width=435";?>", "");</script>
<?php
}else{
	if (isset($_REQUEST["showcommentform"]) && 	$_REQUEST["showcommentform"]=="true"){
		comment_form("",$fbpostid);
	}else{
		include("fbconnect_widget.php");
	}
}

}elseif (isset($_GET['fbajaxlogout'])){
	wp_logout();
	include("fbconnect_widget.php");
}elseif (isset($_GET['refresh'])){
/*nocache_headers();
global $comment_post_ID;
$comment_post_ID=$wall_page;
if (isset($_REQUEST['fbstatus_postid']) && $_REQUEST['fbstatus_postid']!=""){
$comment_post_ID= $_REQUEST['fbstatus_postid'];
}
global $fbconnect_page;
global $fbconnect_filter;
$fbconnect_page = $_GET['refresh'];
$fbconnect_filter = $_GET['filter'];
echo '<ol class="commentlist">';
wp_list_comments();
echo '</ol>';
*/
}else if ($_POST['submit_status']){

	if($_REQUEST['submit_comment_parent'] && $_REQUEST['submit_comment_parent']!=""){ //Asociamos al mismo post del comentario padre
		$comentariopadre = get_comment($_REQUEST['submit_comment_parent']);
		if ($comentariopadre->post_ref_ID!="" && $comentariopadre->post_ref_ID!=0 ){
			$comment_post_ID = $comentariopadre->post_ref_ID;
		}else{
			$comment_post_ID = $comentariopadre->comment_post_ID;
		}
	}elseif (isset($_POST['submit_fbstatus_postid']) && $_POST['submit_fbstatus_postid']!=""){
		$comment_post_ID= $_POST['submit_fbstatus_postid'];
	}else{
		$comment_post_ID=$wall_page;
	}

	$actual_post=get_post($comment_post_ID);
	
	if (!$comment_post_ID || !$actual_post || ($comment_post_ID!=$actual_post->ID) ){
		echo 'Sorry, there was a problem. Please try again.';
		exit;
	}
	
	$comment_author       = "";
	$comment_content      = substr(strip_tags(trim($_POST['submit_fbstatus_comment'])),0,5000);
	$comment_author_email = "";
	
	if ( $user->ID ) {
		$comment_author  = $wpdb->escape($user->display_name);
		$comment_author_email = $wpdb->escape($user->user_email);
	}else{
		echo 'Sorry, you must be logged in to post a comment.';
		exit;
	}
	
	/*if ( '' == $comment_content ){
	echo 'Please type a comment.';
	exit;
	}*/
	
	// insert the comment
	$comment_type = "";
	if (isset($_REQUEST['workspace_type'])){
		$comment_type = $_REQUEST['workspace_type'];
	}
	$comment_parent = isset($_REQUEST['submit_comment_parent']) ? absint($_REQUEST['submit_comment_parent']) : 0;
	
	$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_content','comment_parent', 'user_ID','comment_type');
	//$_REQUEST["sendToFacebook"] = "on";
	$comment_id = wp_new_comment( $commentdata );
	
	if (isset($_REQUEST["sendToFacebookAjax"]) && $_REQUEST["sendToFacebookAjax"]!=""){
		$template_data = $_SESSION["template_data"];
		$fb_user = fb_get_loggedin_user();
		if ($fb_user!=""){
			$result = fb_stream_publish($comment_content,fb_json_encode($template_data["attachment"]),fb_json_encode($template_data["action_links"]));
			if ($result=="ERROR"){
				fb_users_setStatus($comment_content,$fb_user);
			}
		}
		$_SESSION["template_data"] = "";
	}
}else{
$comment_post_ID = $_REQUEST["postid"];
}
/*if(file_exists (TEMPLATEPATH.'/fbconnect_feed.php')){
include( TEMPLATEPATH.'/fbconnect_feed.php');
}else{
include( FBCONNECT_PLUGIN_PATH.'/fbconnect_feed.php');
}*/
//$comments = WPfbConnect_Logic::get_post_comments(1000,$comment_post_ID,0);
?>

<?php if ($show_refresh) :
?>
<script type='text/javascript'>
	wp_userid = '<?php echo $user->ID;?>';
	fb_isNewUser = '<?php echo $new_fb_user;?>';
	fb_user_terms = '<?php echo $terms;?>';
</script>
<?php endif;?>

