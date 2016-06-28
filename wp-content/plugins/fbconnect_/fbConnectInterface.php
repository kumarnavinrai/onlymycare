<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 05/10/2008
 * @license: GPLv2
 */

include_once FBCONNECT_PLUGIN_PATH.'/fbConfig.php';

if (!class_exists('WPfbConnect_Interface')):
class WPfbConnect_Interface {

	/**
	 * Enqueue required javascript libraries.
	 *
	 * @action: init
	 **/
	function js_setup() {
	}

	// Remove the filter excerpts
	function remove_share($content) {
	    remove_action('the_content', array( 'WPfbConnect_Interface', 'add_fbshare' ));
	    //echo "elimina ".$content;
	    return $content;
	}
	
	function add_shareLinks($title,$body,$caption,$url,$imgurl){
		if ($url==""){
			global $post;
			if ($post!=""){
				$url = get_permalink($post->ID);
			}
		}
		?>
		<div class="fbsharelinks">
		<div class="textoCompartir">Compártelo con tus amigos:</div>
		<a class="fb_login_button fb_login_button_medium" onclick="fbshare_facebook('<?php echo $title?>','<?php echo $caption?>','<?php echo $body?>','<?php echo $imgurl?>','<?php echo $url?>')">
		<span class="fb_login_button_text"><?php _e('Share', 'fbconnect'); ?></span>
		</a>
		<a class="tw_login_button tw_login_button_medium" onclick="fbshare_twitter('<?php echo urlencode($title)." ".$url;?>')">
		<span class="tw_login_button_text"><?php _e('Share', 'fbconnect'); ?></span>
		</a>
		<a class="tu_login_button tu_login_button_medium" onclick="fbshare_tuenti('<?php echo $url?>')">
		<span class="tu_login_button_text"><?php _e('Share', 'fbconnect'); ?></span>
		</a>
		</div>
		<?php
	}
	
	/**
	 * Add Facebook Share
	 *get_post_meta($id, 'fbconnect_short_url', true);
	 * @action: the_content
	 **/
	function add_fbshare($contentOrig) {
		global $post;
		 //echo "add";
		//$content = '<div class="fbconnect_head_share"><fb:share-button class="url" type="box_count" href="'.get_permalink($post->ID).'" /></div>'.$content;
		$lang= "en";
		
		if (WPLANG!=""){
				$lang = substr(WPLANG,0,2);	
		}

		$content = '';
		$imgurl = WPfbConnect_Logic::get_post_image($post->ID);
		$title = $post->post_title;
		$permalink = get_permalink($post->ID);

		if (isset($post->post_excerpt) && $post->post_excerpt!=""){
			$body_short = substr(strip_tags($post->post_excerpt),0,250);
		}else{
			$body_short = substr(strip_tags($post->post_content),0,250);
		}
		if ( (!is_home() || fb_get_option('fb_hide_home_head_share')=="") && ($post->post_type != "page" || fb_get_option('fb_add_page_head_share')!="")){
			$content = "";
			if( fb_get_option('fb_add_post_head_like') ) {
				if (fb_get_option('fb_add_post_head_send')){
					$send = 'send="true"';
				}else{
					$send ="";
				}
				//$content = '<fb:like layout="button_count" href="'.get_permalink($post->ID).'"></fb:like><br/><br/>';
				$content .= '<fb:like layout="box_count" font="arial" href="'.get_permalink($post->ID).'"></fb:like><br/><br/>';
				//$content = '<div class="fbconnect_head_share"><fb:like layout="box_count" href="'.get_permalink($post->ID).'"></fb:like></div>';
			}
			if( fb_get_option('fb_add_post_head_share') ) {
				$content .= '<a onclick="fbshare_facebook_login(\''.$title.'\',\'\',\''.$body_short.'\',\''.$imgurl.'\',\''.$permalink.'\',\'dialog\',\''.$permalink.'\'); return false;" class="fb_login_button fb_login_button_small">';
				$content .= '<span class="fb_login_button_text">'.__("Share","fbconnect").'</span>';
				$content .= '</a><br/><br/>';
				//$content .= '<fb:share-button display="popup" class="url" type="box_count" href="'.get_permalink($post->ID).'" />';
			}
			if ( fb_get_option('fb_add_post_head_google1')) {
				$content .= '<g:plusone href="'.get_permalink($post->ID).'" size="tall"></g:plusone><br/><br/>';				
			}
			if (fb_get_option('fb_add_post_head_send') ){
				$content .= '<fb:send href="'.get_permalink($post->ID).'"></fb:send><br/><br/>';
			}
			if( fb_get_option('tw_add_post_head_share') ) {
				$content .= '<a href="http://twitter.com/share" '.$onclick.' class="twitter-share-button" data-url="'.get_permalink($post->ID).'" data-text="'.$post->post_title.'" data-count="vertical" data-via="'.fb_get_option('tw_userid').'" data-lang="'.$lang.'"></a><br/><br/>';
				//$content .= '<iframe id="tweet_frame_'.$post->ID.'" name="tweet_frame_'.$post->ID.'" allowtransparency="true" frameborder="0" role="presentation" scrolling="no" src="http://platform.twitter.com/widgets/tweet_button.html?url='.urlencode(get_permalink($post->ID)).'&amp;via='.fb_get_option('tw_userid').'&amp;text='.urlencode($post->post_title).'&amp;count=vertical" width="55" height="63"></iframe><br/><br/>';
			}
			if( fb_get_option('li_add_post_head_share') ) {
				$content .= '<script type="IN/Share" data-url="'.get_permalink($post->ID).'" data-counter="top"></script> <br/><br/>';
				//$content .= '<iframe id="tweet_frame_'.$post->ID.'" name="tweet_frame_'.$post->ID.'" allowtransparency="true" frameborder="0" role="presentation" scrolling="no" src="http://platform.twitter.com/widgets/tweet_button.html?url='.urlencode(get_permalink($post->ID)).'&amp;via='.fb_get_option('tw_userid').'&amp;text='.urlencode($post->post_title).'&amp;count=vertical" width="55" height="63"></iframe><br/><br/>';
			}
		
			if( fb_get_option('fb_add_post_head_pinterest') ) {
				$content .= '<a href="http://pinterest.com/pin/create/button/?url='.urlencode(get_permalink($post->ID)).'&media='.urlencode($imgurl).'&description='.urlencode($post->post_title).'" class="pin-it-button" count-layout="vertical"><img border="0" src="http://assets.pinterest.com/images/PinExt.png" title="Pin It" /></a><br/><br/>';
				//$content .= '<fb:share-button display="popup" class="url" type="box_count" href="'.get_permalink($post->ID).'" />';
			}

			if ($content!=""){
				$content = '<div class="fbconnect_head_share" style="'.fb_get_option('fb_share_head_style').'">'.$content.'</div>'.$contentOrig;
			}else{
				$content = $contentOrig;
			}
		}else{
			$content = $contentOrig;
		}
		
		if ( (!is_home() || fb_get_option('fb_hide_home_share')=="") && ($post->post_type != "page" || fb_get_option('fb_add_page_share')!="")){
			$contentFooter = "";
			if( fb_get_option('fb_add_post_share') ) {
				$contentFooter .= '<div id="fbsharefooter" class="fbfootersharebutton"><a onclick="fbshare_facebook_login(\''.$title.'\',\'\',\''.$body_short.'\',\''.$imgurl.'\',\''.$permalink.'\',\'dialog\',\''.$permalink.'\'); return false;" class="fb_login_button fb_login_button_small">';
				$contentFooter .= '<span class="fb_login_button_text">'.__("Share","fbconnect").'</span>';
				$contentFooter .= '</a></div>';
				//$contentFooter .= '<div id="fbsharefooter" class="fbfootersharebutton"><fb:share-button class="url" type="button_count" href="'.get_permalink($post->ID).'" /></div>';
			}
			
			if ( fb_get_option('fb_add_post_google1')!="") {
				$contentFooter .= '<div id="googlesharefooter" class="fbfootersharebutton"><g:plusone href="'.get_permalink($post->ID).'" size="medium"></g:plusone></div>';				
			}
		
			if( fb_get_option('tw_add_post_share')) {
					$contentFooter .= '<div id="twittersharefooter" class="fbfootersharebutton"><a href="http://twitter.com/share" class="twitter-share-button" data-url="'.get_permalink($post->ID).'" data-text="'.$post->post_title.'" data-count="horizontal" data-via="'.fb_get_option('tw_userid').'" data-lang="'.$lang.'"></a></div>';
					//$contentFooter .= '<div style="float:left"><iframe id="tweet_frame_'.$post->ID.'" name="tweet_frame_'.$post->ID.'" allowtransparency="true" frameborder="0" role="presentation" scrolling="no" src="http://platform.twitter.com/widgets/tweet_button.html?url='.urlencode(get_permalink($post->ID)).'&amp;via='.fb_get_option('tw_userid').'&amp;text='.urlencode($post->post_title).'&amp;count=horizontal" width="100" height="20"></iframe></div>';
			}
			if( fb_get_option('li_add_post_share')) {
					$contentFooter .= '<div id="linkedinsharefooter" class="fbfootersharebutton"><script type="IN/Share" data-url="'.get_permalink($post->ID).'" data-counter="right"></script></div>';
					//$contentFooter .= '<div style="float:left"><iframe id="tweet_frame_'.$post->ID.'" name="tweet_frame_'.$post->ID.'" allowtransparency="true" frameborder="0" role="presentation" scrolling="no" src="http://platform.twitter.com/widgets/tweet_button.html?url='.urlencode(get_permalink($post->ID)).'&amp;via='.fb_get_option('tw_userid').'&amp;text='.urlencode($post->post_title).'&amp;count=horizontal" width="100" height="20"></iframe></div>';
			}
			if( fb_get_option('fb_add_post_pinterest')) {
					$contentFooter .= '<div id="pinterestsharefooter" class="fbfootersharebutton"><a href="http://pinterest.com/pin/create/button/?url='.urlencode(get_permalink($post->ID)).'&media='.urlencode($imgurl).'&description='.urlencode($post->post_title).'" class="pin-it-button" count-layout="horizontal"><img border="0" src="http://assets.pinterest.com/images/PinExt.png" title="Pin It" /></a></div>';
					//$contentFooter .= '<div style="float:left"><iframe id="tweet_frame_'.$post->ID.'" name="tweet_frame_'.$post->ID.'" allowtransparency="true" frameborder="0" role="presentation" scrolling="no" src="http://platform.twitter.com/widgets/tweet_button.html?url='.urlencode(get_permalink($post->ID)).'&amp;via='.fb_get_option('tw_userid').'&amp;text='.urlencode($post->post_title).'&amp;count=horizontal" width="100" height="20"></iframe></div>';
			}
			/*if (fb_get_option('fb_add_post_send') ){
				$contentFooter .= '<div id="fbsendfooter" class="fbfootersharebutton"><fb:send href="'.get_permalink($post->ID).'"></fb:send></div>';
			}*/
				//$content .= '<div style="float:right;margin-left:10px;"><a rel="nofollow" target="_blank" href="http://www.google.com/reader/link?url='.urlencode(get_permalink($post->ID)).'&amp;title='.urlencode($post->post_title).'&amp;srcURL='.urlencode(fb_get_option('siteurl')).'" class="google_buzz"><img alt="Google Buzz" src="'.FBCONNECT_PLUGIN_URL_IMG.'/buzzp.jpg"></a></div>';
				/*if( FBCONNECT_CANVAS=="web") {
					$content .= '<div style="float:right;"><a name="fb_share" type="button_count" share_url="'.get_permalink($post->ID).'" href="http://www.facebook.com/sharer.php">'.__('Share', 'fbconnect').'</a></div>';
					//$content .= '<p class="fbconnect_share"><fb:share-button class="url" type="button_count" href="'.get_permalink($post->ID).'" /></p>';
				}else{
					$content .= '<div style="float:right;"><fb:share-button class="url" href="'.get_permalink($post->ID).'" /></div>';			
				}*/
			if( fb_get_option('fb_add_post_like') ) {
				if (fb_get_option('fb_add_post_send')){
					$send = 'send="true"';
				}else{
					$send ="";
				}
				$hidefaces = "";
				if (fb_get_option('fb_like_show_faces')){
					$hidefaces = 'show_faces="false" layout="button_count"';				
				}
				$contentFooter .= '<div id="fblikefooter" class="fbfootersharebutton"><fb:like '.$send.' href="'.get_permalink($post->ID).'" '.$hidefaces.'></fb:like></div>';
			}
			if(  $contentFooter!="") {
				$content .= '<div class="fbconnect_share" style="'.fb_get_option('fb_share_style').'">'.$contentFooter.'</div>';
			}
			/*if( FBCONNECT_CANVAS=="web" && (fb_get_option('fb_add_post_share') || fb_get_option('fb_add_post_head_share')) ) {
				$content .= '<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>';
			}*/
			//$content .= '<div class="fbconnect_share"><fb:share-button class="url" type="button_count" href="'.get_permalink($post->ID).'" /></div>';
		}			
		if ( is_home() && fb_get_option('fb_fbcomments_home')!=""){
			if (FBCONNECT_CANVAS=="web"){
				$width = "550";
				if (fb_get_option('fb_fbcomments_width') != ""){
					$width = fb_get_option('fb_fbcomments_width');
				}
			}else{
				$width = "490";
				if (fb_get_option('fb_fbcomments_canvas_width') != ""){
					$width = fb_get_option('fb_fbcomments_canvas_width');
				}
			}
			
			$content .= '<fb:comments migrated=1 numposts="'.fb_get_option('fb_fbcomments_home_numposts').'" width='.$width.' xid="'.$post->ID.'"> </fb:comments>';
			 
		}
		return $content;
	}
	
	
	/**
	 * Include internal stylesheet.
	 *
	 * @action: wp_head, login_head
	 **/
	function style() {
		if ( file_exists( TEMPLATEPATH . '/fbconnect.css') ){
			$css_path =  get_template_directory_uri() . '/fbconnect.css?ver='.FBCONNECT_PLUGIN_REVISION;
		}else{
			$css_path = FBCONNECT_PLUGIN_URL . '/fbconnect.css?ver='.FBCONNECT_PLUGIN_REVISION;
		}

		//echo '<link rel="stylesheet" type="text/css" href="http://www.escire.com/wp-content/plugins/fbconnect/fbconnect.css" />';

		echo '<link rel="stylesheet" type="text/css" href="'.$css_path.'" />';
		//echo '<link rel="stylesheet" href="'.fb_get_option('siteurl').'/'.WPINC.'/js/thickbox/thickbox.css" type="text/css" media="screen" />';
		
		$charset = get_bloginfo( 'charset' );
		if ($charset==""){
			$charset = "UTF-8";
		}
		
		$disablemetas = fb_get_option('fb_disable_ogmetas');
		if(!$disablemetas){
			if (is_single() || is_page()){
				global $post;
				$postID="";
				if (isset($post))
					$postID=$post->ID;
	
				$imgurl = WPfbConnect_Logic::get_post_image($postID);
				$pos = strrpos($imgurl, "default_logo.gif");
				if ($pos === false) {
					echo "<link rel=\"image_src\" href=\"".$imgurl."\" />\n";
					echo "<meta property=\"og:image\" content=\"".$imgurl."\"/>\n";
					echo "<meta itemprop=\"image\" content=\"".$imgurl."\">\n";
	
				}
	
				if(function_exists('fbconnect_getvideourl_post')):	
					$video_src = fbconnect_getvideourl_post($postID);
					if ($video_src!=""){
						echo '<link rel="video_src" href="'.$video_src.'" />';
					}
				endif;
				echo "<meta property=\"og:site_name\" content=\"".htmlentities(get_bloginfo('name'),ENT_COMPAT,$charset)."\"/>\n";
				echo "<meta property=\"og:title\" content=\"".htmlentities(wp_title( '|', false,'right').get_bloginfo('name'),ENT_COMPAT,$charset)."\"/>\n";
				echo "<meta property=\"og:url\" content=\"".get_permalink($post->ID)."\" />\n";
				
				//remove_all_filters("excerpt_more");
				$excerpt = str_replace( array( "\r\n", "\r", "\n" ), ' ', wp_strip_all_tags( strip_shortcodes( apply_filters( 'the_excerpt', get_the_excerpt() ) ) ) );

				
				if ($excerpt==""){
					$excerpt = $post->post_content;
					$excerpt = htmlentities(substr(strip_tags($excerpt),0,250),ENT_COMPAT,$charset);
				}
				echo "<meta property=\"og:description\" content=\"".$excerpt."\" />\n";
				
				$og_type = get_post_meta($post->ID, '_fbconnect_og_type', true);
				$namespace = "";
				if (fb_get_option('fb_og_namespace')!=""){
					$namespace = fb_get_option('fb_og_namespace').":";
				}
		
				if ($og_type!=""){
					echo "<meta property=\"og:type\" content=\"".$namespace.htmlentities($og_type,ENT_COMPAT,$charset)."\"/>\n";
					echo "<meta property=\"object_type\" content=\"".$namespace.htmlentities($og_type,ENT_COMPAT,$charset)."\" />\n";
				}else{
					$defaultpostype = fb_get_option('fb_og_posttype');
					if ($defaultpostype=="" ){
						$defaultpostype = "article";
					}else{
						$defaultpostype = $namespace.$defaultpostype;
					}
					echo "<meta property=\"og:type\" content=\"".$defaultpostype."\"/>\n";
					echo "<meta property=\"object_type\" content=\"".$defaultpostype."\" />\n";
				}
				$fb_admins = get_post_meta($post->ID, '_fbconnect_fb_admins', true);
				if ($fb_admins!=""){
						echo "<meta property=\"fb:admins\" content=\"".htmlentities($fb_admins,ENT_COMPAT,$charset)."\"/>\n";
						echo "<meta property=\"fb_admins\" content=\"".htmlentities($fb_admins,ENT_COMPAT,$charset)."\" />\n";
				}elseif (fb_get_option('fb_admins')!=""){
						echo "<meta property=\"fb:admins\" content=\"".stripslashes(htmlentities(fb_get_option('fb_admins'),ENT_COMPAT,$charset))."\"/>\n";
						echo "<meta property=\"fb_admins\" content=\"".stripslashes(htmlentities(fb_get_option('fb_admins'),ENT_COMPAT,$charset))."\" />\n";
				}
				$appId = fb_get_option('fb_appId');
				if ($appId!=""){
						echo "<meta property=\"fb_app_id\" content=\"".$appId."\" />\n";
						echo "<meta property=\"fb:app_id\" content=\"".$appId."\"/>\n";
				}
	
				
			}else{
				if (fb_get_option('fb_comments_logo')!=""){
					echo "<link rel=\"image_src\" href=\"".fb_get_option('fb_comments_logo')."\" />\n";
					echo "<meta property=\"og:image\" content=\"".fb_get_option('fb_comments_logo')."\"/>\n";
				}
	
				echo "<meta property=\"og:site_name\" content=\"".stripslashes(htmlentities(get_bloginfo('name'),ENT_COMPAT,$charset))."\"/>\n";
				echo "<meta property=\"og:description\" content=\"".stripslashes(htmlentities(get_bloginfo('description'),ENT_COMPAT,$charset))."\"/>\n";
				
				if (fb_get_option('fb_og_type')!=""){
					echo "<meta property=\"og:type\" content=\"".stripslashes(htmlentities(fb_get_option('fb_og_type'),ENT_COMPAT,$charset))."\"/>\n";
					echo "<meta property=\"object_type\" content=\"".stripslashes(htmlentities(fb_get_option('fb_og_type'),ENT_COMPAT,$charset))."\" />\n";
				}
				
				if (fb_get_option('fb_admins')!=""){
					echo "<meta property=\"fb:admins\" content=\"".stripslashes(htmlentities(fb_get_option('fb_admins'),ENT_COMPAT,$charset))."\"/>\n";
					echo "<meta property=\"fb_admins\" content=\"".stripslashes(htmlentities(fb_get_option('fb_admins'),ENT_COMPAT,$charset))."\" />\n";
				}
				
				$appId = fb_get_option('fb_appId');
				$pageid = fb_get_option('fb_fanpage_id');
				
				if ($appId!=""){
						echo "<meta property=\"fb_app_id\" content=\"".$appId."\" />\n";
						echo "<meta property=\"fb:app_id\" content=\"".$appId."\"/>\n";
				}
				
				if($pageid!=""){
						echo "<meta property=\"fb:page_id\" content=\"".$pageid."\"/>\n";
				}
				
				echo "<meta property=\"og:url\" content=\"".get_bloginfo('url')."\" />\n";
				
				if (fb_get_option('fb_og_latitude')!=""){
					echo "<meta property=\"og:latitude\" content=\"".stripslashes(htmlentities(fb_get_option('fb_og_latitude'),ENT_COMPAT,$charset))."\"/>\n";
				}
				if (fb_get_option('fb_og_longitude')!=""){
					echo "<meta property=\"og:longitude\" content=\"".stripslashes(htmlentities(fb_get_option('fb_og_longitude'),ENT_COMPAT,$charset))."\"/>\n";
				}
				if (fb_get_option('fb_og_street_address')!=""){
					echo "<meta property=\"og:street-address\" content=\"".stripslashes(htmlentities(fb_get_option('fb_og_street_address'),ENT_COMPAT,$charset))."\"/>\n";
				}
				if (fb_get_option('fb_og_locality')!=""){
					echo "<meta property=\"og:locality\" content=\"".stripslashes(htmlentities(fb_get_option('fb_og_locality'),ENT_COMPAT,$charset))."\"/>\n";
				}
				if (fb_get_option('fb_og_region')!=""){
					echo "<meta property=\"og:region\" content=\"".stripslashes(htmlentities(fb_get_option('fb_og_region'),ENT_COMPAT,$charset))."\"/>\n";
				}
				if (fb_get_option('fb_og_postal_code')!=""){
					echo "<meta property=\"og:postal-code\" content=\"".stripslashes(htmlentities(fb_get_option('fb_og_postal_code'),ENT_COMPAT,$charset))."\"/>\n";
				}																		
				if (fb_get_option('fb_og_country_name')!=""){
					echo "<meta property=\"og:country-name\" content=\"".stripslashes(htmlentities(fb_get_option('fb_og_country_name'),ENT_COMPAT,$charset))."\"/>\n";
				}												
			}
		}
	}


	/**
	 *  Modify comment form.
	 *
	 * @action: comment_form
	 **/
	function comment_form() {
		$fb_user = fb_get_loggedin_user();
		$user = wp_get_current_user();
		if (is_user_logged_in() && $user->fbconnect_userid!=0) {
			$style = "";
		}else{
			$style = "display:none;";
		}

		echo '<span id="fbconnectcheckpublish" class="fbconnect_sharewith_'.$user->fbconnect_netid.'" style="'.$style.'">'; 
		//echo '<img class="icon-text-middle" src="'.FBCONNECT_PLUGIN_URL .'/images/facebook_24.png"/>';
		echo '<input style="width:20px;" type="checkbox" name="sendToFacebook" id="sendToFacebook" checked="checked" />'.__('Share with', 'fbconnect').' '.$user->fbconnect_netid;
		echo '</span>';
	
		if(fb_get_option('fb_connect_comments_login')){
			echo '<div id="fbconnect_commentslogin" class="fbconnect_commentsloginclass fbconnect_widget_divclass">';
			include(FBCONNECT_PLUGIN_PATH."/fbconnect_widget_login.php");

			if (!isset($_GET['fbajaxlogin']) && !isset($_GET['fbajaxlogout'])){ ?>
			<div class="fbcreditos"><?php _e('Powered by', 'fbconnect'); ?> <a href="http://www.sociable.es">Sociable!</a></div>
			<?php
			}
			echo '</div>';
			echo "<script type='text/javascript'>\n";
			echo 'showCommentsLogin();';
			echo "</script>\n";
		}
	}

	function comment_form_before() {
		$fb_user = fb_get_loggedin_user();
		$user = wp_get_current_user();

		if(fb_get_option('fb_connect_comments_login')){
			echo '<div id="fbconnect_commentslogin" class="fbconnect_commentsloginclass fbconnect_widget_divclass">';
			include(FBCONNECT_PLUGIN_PATH."/fbconnect_widget_login.php");

			if (!isset($_GET['fbajaxlogin']) && !isset($_GET['fbajaxlogout'])){ ?>
			<div class="fbcreditos"><?php _e('Powered by', 'fbconnect'); ?> <a href="http://www.sociable.es">Sociable!</a></div>
			<?php
			}
			echo '</div>';
		}
	}
	function fbconnect_add_main_img_box(){
		if( function_exists( 'add_meta_box' )) {
			if(fb_get_option('fb_add_main_image')){
				add_meta_box( 'fbconnect_main_img', __( 'Main post image', 'fbconnect' ), 
		                array( 'WPfbConnect_Interface','fbconnect_main_img_box'), 'post', 'side','high' );						
				add_meta_box( 'fbconnect_main_img', __( 'Main post image', 'fbconnect' ), 
		                array( 'WPfbConnect_Interface','fbconnect_main_img_box'), 'page', 'side','high' );
			}						
			add_meta_box( 'fbconnect_opengraph', __( 'Facebook OpenGraph', 'fbconnect' ), 
		                array( 'WPfbConnect_Interface','fbconnect_opengraph_box'), 'page', 'advanced' );
		    add_meta_box( 'fbconnect_opengraph', __( 'Facebook OpenGraph', 'fbconnect' ), 
		                array( 'WPfbConnect_Interface','fbconnect_opengraph_box'), 'post', 'advanced' );
		    if (fb_get_option('fb_use_fbtheme')!=""){						
		    	add_meta_box( 'fbconnect_page_template', __( 'Facebook Theme template', 'fbconnect' ), 
		                array( 'WPfbConnect_Interface','fb_page_attributes_meta_box'), 'page', 'side','low' );
		    }
		    add_meta_box( 'fbconnect_stream', __( 'Facebook Stream', 'fbconnect' ), 
		                 array( 'WPfbConnect_Interface','fbconnect_stream_box'), 'post', 'side','high' );
			add_meta_box( 'fbconnect_stream', __( 'Facebook Stream', 'fbconnect' ), 
		                 array( 'WPfbConnect_Interface','fbconnect_stream_box'), 'page', 'side','high' );						
	   } 
	}
		
	function fbconnect_stream_box(){
		global $post;
	
		$post_id = $post;
		if (is_object($post_id)){
			$post_id = $post_id->ID;
		}
		
		echo "
			<script>
				function permissionsF8()
				{
					FB.login(function(response) {
					  if (response.authResponse) {
						if (response.perms) {
						  //222
						  // user is logged in and granted some permissions.
						  // perms is a comma separated list of granted permissions
						  if(response.perms.indexOf('manage_pages')>=0 && response.perms.indexOf('read_stream')>=0 && response.perms.indexOf('publish_stream')>=0)
							alert('Ya ha concedido los permisos de Lectura/Escritura en el Stream de la Fan Page');
						  
						} else {
						  // user is logged in, but did not grant any permissions
						}
					  } else {
						// user is not logged in
					  }
					}, {scope:'read_stream,publish_stream,manage_pages'});
				}		
			</script>
		";
		echo '<input type="button" value="Permitir publicar en muros »" name="selectPage" onclick="permissionsF8();" class="button-primary"/><br/><br/>';
		echo '<input type="checkbox" name="fb_publish_stream" value="yes" /> '; 
		echo '<label for="fb_publish_stream">' . __("Publish to facebook", 'fbconnect' ) . '</label> <br/>';
		echo '<input type="checkbox" name="fb_publish_linkcanvas" value="yes" /> ';		
  		echo '<label for="fb_publish_linkcanvas">' . __("Link to Canvas app", 'fbconnect' ) . '</label> <br/>';
		echo '<input type="checkbox" name="fb_publish_asflash" value="yes" /> ';		
  		echo '<label for="fb_publish_asflash">' . __("Publish as online flash", 'fbconnect' ) . '</label> <br/><br/>';

		echo '<label for="fb_publish_stream_comment">' . __("Comment", 'fbconnect' ) . '</label><br/> ';
		echo '<textarea name="fb_publish_stream_comment" rows="2" cols="30"></textarea>';
	}


	function fbconnect_main_img_box(){
		global $post;
		$post_id = $post;
		if (is_object($post_id)){
			$post_id = $post_id->ID;
		}
		echo "<script type='text/javascript'>\n";
		echo "function fbconnect_changeimg(url,imgid){\n";
		echo "jQuery(document).ready(function($) {\n";
		echo "$('#fb_mainimg').attr('src', url);\n";
		echo "$('#fb_mainimg_url').attr('value', url);\n";
		echo "$('#fb_mainimg_id').attr('value', imgid);});\n";
		echo "tb_remove();\n";
		echo "\n}\n";
		echo "function fbconnect_imgselect(){\n";
		echo '	tb_show("Main post image", "'.fb_get_option('siteurl').'?fbconnect_action=mainimage&modal=true&postid='.$post_id.'", "");';
		echo "\n}";
	   	echo "</script>\n";
		echo '<div style="text-align:center;width:250px;height:150px;margin-top:5px;">';
		echo '<a href="#" onclick="fbconnect_imgselect()"><b>Change main post image</b></a>';
		echo '<div style="margin:5px;width:250px;height:150px;margin-top:5px;">';
		
		$imgurl = WPfbConnect_Logic::get_post_image($post_id);
		$imgid = get_post_meta($post_id , 'fb_mainimg_id', true);
		$currentimgurl = get_post_meta($post_id , 'fb_mainimg_url', true);
		//$thumb="http://www.sociable.es/wp-content/themes/sociable/images/sociable_logo.gif";
       	echo '<img src="'.$imgurl.'" id="fb_mainimg" width=100>';
		echo '<input type="hidden" id="fb_mainimg_url" name="fb_mainimg_url" value="'.$currentimgurl.'"/>';
		echo '<input type="hidden" id="fb_mainimg_id" name="fb_mainimg_id" value="'.$imgid.'"/>';
		echo '</div>';
		echo '</div>';
	}
	
	function fbconnect_img_selector($post_id=""){
		if ($post_id==""){
			echo "Error: Select a post";
			return;
		}	
		

		echo '<div style="width:100%;margin:10px;">';
		echo '<h2>Select an image</h2>';
		$files = get_children("post_parent=$post_id&post_type=attachment&post_mime_type=image");
		//$files = get_children("post_parent=$post_id&post_type=attachment");
		if ($files!="" && count($files)>0){
			foreach($files as $num=>$value){
				echo '<div style="text-align:center;margin:5px;width:50px;height:70px;float:left;">';
				echo '<div style="width:50px;height:50px;float:left;">';
		        $thumb=wp_get_attachment_thumb_url($num);
				//$thumb="http://www.sociable.es/wp-content/themes/sociable/images/sociable_logo.gif";
		       	$img = "<img src='$thumb' width=50 align=right/>";
				//$thumb = wp_get_attachment_image( $post->ID);
				echo $img;
				echo '</div>';
				echo '<input type="radio" name="fb_publish_imgid" onclick="fbconnect_changeimg(\''.$thumb.'\',\''.$num.'\')" value="'.$num.'"/>';
				echo '</div>';
			}
		}
		echo '</div>';
		echo '<div style="width:100%;margin:10px;clear:both;text-align:center;">';
		echo '<label for="fb_current_url">Main image URL:</label>';
		echo '<input type="text" size=75 id="fb_current_url" name="fb_current_url" value="'.WPfbConnect_Logic::get_post_image($post_id).'"/><br/><br/>';
		echo '<input type="button" name="fb_save" id="fb_save" onclick="fbconnect_changeimg(document.getElementById(\'fb_current_url\').value,\'0\')" value="'.__('Save', 'fbconnect').'">'; 
		echo '<input type="button" name="fb_cancel" id="fb_cancel" onclick="tb_remove();" value="'.__('Cancel', 'fbconnect').'">'; 
		echo '</div>';
		//echo '<input type="button" name="fb_select" id="fb_cancel" onclick="fb_changeimg();" value="'.__('Select', 'fbconnect').'">'; 
	}
	
	function fbconnect_save_post($post_id ){ 
		if ( 'page' == $_POST['post_type'] ) {
		    if ( !current_user_can( 'edit_page', $post_id ))
		      return $post_id;
		} else {
		    if ( !current_user_can( 'edit_post', $post_id ))
		      return $post_id;
		}

		if ( isset($_POST['fb_mainimg_url']) && $_POST['fb_mainimg_url']!="" ) {
			update_post_meta($post_id , 'fb_mainimg_url', $_POST['fb_mainimg_url']);
		}
		if ( isset($_POST['fb_mainimg_id']) && $_POST['fb_mainimg_id']!="" ) {
			update_post_meta($post_id , 'fb_mainimg_id', $_POST['fb_mainimg_id']);
		}
		
		update_post_meta($post_id , '_fbconnect_og_type', $_POST['_fbconnect_og_type']);
		update_post_meta($post_id , '_fbconnect_fb_admins', $_POST['_fbconnect_fb_admins']);
		update_post_meta($post_id ,'_fbpage_template',$_POST['_fbpage_template']);


		if (isset($_POST['_fb_custom_theme_name'])){
			$themes = get_themes();
			$theme = $themes[$_POST['_fb_custom_theme_name']];
			update_post_meta($post_id ,'_fb_custom_theme_name', $_POST['_fb_custom_theme_name']);
			update_post_meta($post_id , '_fb_custom_theme',$theme["Template"]);
			update_post_meta($post_id, '_fb_custom_style', $theme["Stylesheet"]);
		}
			
		global $fbpublished;
		WPfbConnect::log("[fbConnectCorePro::fbconnect_publish_post] Post: ".$post_id,FBCONNECT_LOG_DEBUG);	
		if (isset($_POST["post_ID"]) && $_POST["post_ID"]!=""){
			$post_id = $_POST["post_ID"];
		}
		//print_r($_POST);
		if (isset($_POST['_fbconnect_access_pageid'])){

			$fbconnect_pageid = $_POST['_fbconnect_access_pageid'];
			//$fbconnect_userid = $_POST['_fbconnect_access_userid'];
			$fbconnect_page_name = $_POST['_fbconnect_access_page_name'];
			$fbconnect_page_url = $_POST['_fbconnect_access_page_url'];
			$fbconnect_access_page_thick = $_POST['_fbconnect_access_page_thick'];
			$fbconnect_access_page_check = $_POST['_fbconnect_access_page_check'];
			$fbconnect_access_page_text = $_POST['_fbconnect_access_page_text'];
			$fbconnect_access_page_text_footer = $_POST['_fbconnect_access_page_text_footer'];
		    update_post_meta($post_id , '_fbconnect_access_pageid', $fbconnect_pageid);
		    update_post_meta($post_id , '_fbconnect_access_page_name', $fbconnect_page_name);
		    update_post_meta($post_id , '_fbconnect_access_page_url', $fbconnect_page_url);
		    //update_post_meta($post_id , '_fbconnect_access_userid', $fbconnect_userid);
		    update_post_meta($post_id , '_fbconnect_access_page_thick', $fbconnect_access_page_thick);
		    update_post_meta($post_id , '_fbconnect_access_page_check', $fbconnect_access_page_check);
		    update_post_meta($post_id , '_fbconnect_access_page_text', $fbconnect_access_page_text);
		    update_post_meta($post_id , '_fbconnect_access_page_text_footer', $fbconnect_access_page_text_footer);
		}
		if (!$fbpublished && !$_POST['autosave']){
			if ( 'page' == $_POST['post_type'] ) {
			    if ( !current_user_can( 'edit_page', $post_id ))
			      return $post_id;
			} else {
			    if ( !current_user_can( 'edit_post', $post_id ))
			      return $post_id;
			}
		
			if ( isset($_POST['fb_publish_stream']) ) {
				$thumb = WPfbConnect_Logic::get_post_image($post_id);
				
				//echo $thumb;
				//print_r($_POST);
				//exit();
				if (isset($_POST["fb_publish_linkcanvas"]) && $_POST["fb_publish_linkcanvas"]=="yes"){
					$url =  fb_get_option('fb_canvas_url').'?p='. $post_id;
				}else{
					$url = get_permalink($post_id);
				}
				if (isset($_POST["fb_publish_asflash"]) && $_POST["fb_publish_asflash"]=="yes"){
					$files = get_children("post_parent=$post_id&post_type=attachment&post_mime_type=application/x-shockwave-flash");
					if ($files!="" && count($files)>0){
						foreach($files as $num=>$file){
							$videourl = $file->guid;
							break;
						}
					}
					$attachment = WPfbConnect_Logic::create_attachment($_POST["post_title"],"{*actor*} has published a new post",$_POST["excerpt"],$url,$videourl,$post_id,'flash',$thumb);
					//print_r($attachment);
				}else{
					//$properties = array('category' => array( 'text' => 'humor', 'href' => 'http://www.icanhascheezburger.com/category/humor'), 'ratings' => '5 stars');
					//$attachment = WPfbConnect_Logic::create_attachment($_POST["post_title"],"{*actor*} has published a new post",$_POST["excerpt"],$url,$thumb,$post_id,'image',"",$properties);
					$attachment = WPfbConnect_Logic::create_attachment($_POST["post_title"],"{*actor*} has published a new post",$_POST["excerpt"],$url,$thumb,$post_id,'image',"");
					//print_r($attachment);
				}
				
	
				
				//$attachment = WPfbConnect_Logic::create_attachment($_POST["post_title"],"{*actor*} has published a new post",$_POST["excerpt"],$url,"http://www.sociable.es/test2.flv",$post_id,'video','http://www.sociable.es/wp-content/themes/sociable/images/sociable_logo.gif');
				//$attachment = WPfbConnect_Logic::create_attachment($_POST["post_title"],"{*actor*} has published a new post",$_POST["excerpt"],$url,'http://www.youtube.com/v/fzzjgBAaWZw&hl=en&fs=1',$post_id,'video','http://www.sociable.es/wp-content/themes/sociable/images/sociable_logo.gif');
				//$attachment = WPfbConnect_Logic::create_attachment($_POST["post_title"],"{*actor*} has published a new post",$_POST["excerpt"],$url,'http://static.slidesharecdn.com/swf/ssplayer2.swf?doc=blogssales2-091011192135-phpapp01&stripped_title=blogs-making-sales',$post_id,'video','http://www.sociable.es/wp-content/themes/sociable/images/sociable_logo.gif');
				//$attachment = WPfbConnect_Logic::create_attachment($_POST["post_title"],"{*actor*} has published a new post",$_POST["excerpt"],$url,'http://www.visualxtreme.com/paragon/paramain_index.swf',$post_id,'video','http://www.sociable.es/wp-content/themes/sociable/images/sociable_logo.gif');
				//$attachment = WPfbConnect_Logic::create_attachment($_POST["post_title"],"{*actor*} has published a new post",$_POST["excerpt"],$url,'http://www.listentoyourlips.es/fb/home.swf',$post_id,'video','http://www.sociable.es/wp-content/themes/sociable/images/sociable_logo.gif');
				//$attachment = WPfbConnect_Logic::create_attachment($_POST["post_title"],"{*actor*} has published a new post",$_POST["excerpt"],$url,'http://www2.simyomultimedia.com/simyo/web/web/jsp/flash/mobiPlayer.swf?son=/simyo/web/snd/canciones/audio/web/mp3/093624972969_033_128.mp3&autoplay=0',$post_id,'video','http://www2.simyomultimedia.com/simyo/web/web/jsp/img/imagenes_album/web/100x100/20753/093624972945.200.jpg');
	//$attachment = WPfbConnect_Logic::create_attachment($_POST["post_title"],"{*actor*} has published a new post",$_POST["excerpt"],$url,'http://blip.tv/play/AZOCJgI',$post_id,'video','http://www2.simyomultimedia.com/simyo/web/web/jsp/img/imagenes_album/web/100x100/20753/093624972945.200.jpg');
	
	
				
				//Publicar en muro de página con o sin sesion
				//Videotutoriales "21134181944"
				$fbpostid = fb_stream_publish($_POST["fb_publish_stream_comment"], fb_json_encode($attachment), null,'',fb_get_option('fb_fanpage_id'),"" );
				global $fbpublished;
				$fbpublished = true;
				//Publicar en muro usuario sin sesión
				//$fbpostid = fb_stream_publish($_POST["fb_publish_stream_comment"], fb_json_encode($attachment), null, "800993445","800993445");
				//Publicar en muro del usuario conectado (con sesion)
				//$fbpostid = fb_stream_publish($_POST["fb_publish_stream_comment"], fb_json_encode($attachment), null);
	
				update_post_meta($post_id , 'fbconnect_stream_postid', $fbpostid);
				//fb_stream_addLike($fbpostid);
			}
			
		}

		return;

	}

	
	function print_ogtypes_select($fbconnect_og_type=""){
			echo '<select name="_fbconnect_og_type" id="_fbconnect_og_type" class="widefat">';

				$ogTypes = array("","article","activity","sport","bar","company","cafe","hotel","restaurant","cause","sports_league","sports_team","band","government","non_profit","school","university","actor","athlete","author","director","musician","politician","public_figure","city","country","landmark","state_province","album","book","drink","food","game","product","song","movie","tv_show","blog","website");
				foreach($ogTypes as $ogType)	{
					echo '<option value="'.$ogType.'" '.selected($fbconnect_og_type, $ogType).' >'.$ogType.'</option>';
				}

			echo '</select>';	
	}
	
	
	function fb_get_page_templates($theme) {
		$themes = get_themes();
		$templates = $themes[$theme]['Template Files'];
		$page_templates = array();
	
		if ( is_array( $templates ) ) {
			//$base = array( trailingslashit(get_template_directory()), trailingslashit(get_stylesheet_directory()) );
	
			foreach ( $templates as $template ) {
				//$basename = str_replace($base, '', $template);

				$template_data = implode( '', file( $template ));
	
				$name = '';
				if ( preg_match( '|Template Name:(.*)$|mi', $template_data, $name ) ){
					$name = _cleanup_header_comment($name[1]);
				}
				if ( !empty( $name ) ) {
					$page_templates[trim( $name )] = $template;
				}
			}
		}
		return $page_templates;
	}

	function fb_page_template_dropdown( $themename, $default = '' ) {

		$templates = WPfbConnect_Interface::fb_get_page_templates($themename);
		ksort( $templates );
		foreach (array_keys( $templates ) as $template )
			: if ( $default == $templates[$template] )
				$selected = " selected='selected'";
			else
				$selected = '';
		echo "\n\t<option value='".$templates[$template]."' $selected>$template</option>";
		endforeach;
	}

	function fb_page_attributes_meta_box() {
		global $post;
		if (fb_get_option('fb_use_fbtheme')!=""){
			$wp_themes = get_themes();
			$themeorig = get_current_theme();
	
			$themefb = "";
			foreach($wp_themes as $themename=>$themecache){
				//echo " ".$themecache["Template"]."=".fb_get_option('fb_use_fbtheme');
				/*if ($themecache["Template"]==$stylesheet){
					$themeorig = $themename;
				}*/
				if ($themecache["Template"]==fb_get_option('fb_use_fbtheme')){
					$themefb = $themename;
				}
				
			}
			$post_type_object = get_post_type_object($post->post_type);
			$template =  get_post_meta($post->ID, '_fbpage_template', true);
			$themes = get_themes();
			$postheme = get_post_meta($post->ID, '_fb_custom_theme', true);
			$posthemename = get_post_meta($post->ID, '_fb_custom_theme_name', true);
			$posstyle = get_post_meta($post->ID, '_fb_custom_style', true);
			$themes = get_themes();
		?>
		<label class="fblabelform" for="_fb_custom_theme_name"><?php _e('Page Theme') ?>:</label>
		<SELECT id="_fb_custom_theme_name" name="_fb_custom_theme_name">
		<OPTION <?php if ($postheme=="") echo "SELECTED";?> VALUE=""></OPTION>
		<?php 
		foreach($themes as $name => $theme){
			if ($name==$posthemename){
				$selected = "SELECTED";
			}else{
				$selected = "";
			} 	
		?>
		<OPTION <?php echo $selected;?> VALUE="<?php echo $name;?>"><?php echo $name;?></OPTION>
		<?php 
		}
		?>
		</SELECT>
		
		
		<label class="fblabelform" for="page_template"><?php _e('Page Template') ?>:</label>
		<select name="_fbpage_template" id="_fbpage_template">
		<option value='default'><?php _e('Default Template'); ?></option>
		<?php
	
		WPfbConnect_Interface::fb_page_template_dropdown($themefb,$template);
		
		?>
		</select>
	<?php 
		}
	if ($post->post_type!="page"){
	?>
	<p><strong><?php _e('Order') ?></strong></p>
	<p><label class="screen-reader-text" for="menu_order"><?php _e('Order') ?></label><input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo esc_attr($post->menu_order) ?>" /></p>
	<p><?php if ( 'page' == $post->post_type ) _e( 'Need help? Use the Help tab in the upper right of your screen.' ); ?></p>
	
	<?php
	}
	}

	function fbconnect_single_template($template){
		global $post;
		if ($post!="" && fb_get_option('fb_use_fbtheme')!="" && FBCONNECT_CANVAS!="web"){
			$newtemplate =  get_post_meta($post->ID, '_fbpage_template', true);
			if ($newtemplate!="" && $newtemplate!="default"){
				return $newtemplate;
			}
		}
		return $template;
	}
	
			


	function fbconnect_opengraph_box($showType = true,$showHeaderFooterFanText = true){
		global $post;
	
		$post_id = $post;
		if (is_object($post_id)){
			$post_id = $post_id->ID;
		}
	
		echo '<input type="hidden" name="fbconnect_noncename" id="fbconnect_noncename" value="' . 
	    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
		$fbconnect_og_type = htmlspecialchars(stripcslashes(get_post_meta($post_id, '_fbconnect_og_type', true)));


		$fbconnect_fb_admins = htmlspecialchars(stripcslashes(get_post_meta($post_id, '_fbconnect_fb_admins', true)));
		$fb_user = fb_get_loggedin_user();
		$fbconnect_access_pageid = htmlspecialchars(stripcslashes(get_post_meta($post_id, '_fbconnect_access_pageid', true)));
		$fbconnect_access_page_name = htmlspecialchars(stripcslashes(get_post_meta($post_id, '_fbconnect_access_page_name', true)));
		$fbconnect_access_page_url = htmlspecialchars(stripcslashes(get_post_meta($post_id, '_fbconnect_access_page_url', true)));
		$fbconnect_access_page_text = get_post_meta($post_id , '_fbconnect_access_page_text', true);
		$fbconnect_access_page_text_footer = get_post_meta($post_id , '_fbconnect_access_page_text_footer', true);
		//$fbconnect_access_userid = htmlspecialchars(stripcslashes(get_post_meta($post_id, '_fbconnect_access_userid', true)));
		?>
		<script type='text/javascript'>
		function callbackSelectPageBoxEditor(pageid,pagename,pageurl){
			jQuery("#_fbconnect_access_pageid").attr("value",pageid);
			jQuery("#_fbconnect_access_page_name").attr("value",pagename);
			jQuery("#_fbconnect_access_page_url").attr("value",pageurl);
			tb_remove();
			jQuery("#pagepic").html('<fb:profile-pic size="square" uid="'+pageid+'" linked="true" />');
			FB.XFBML.parse();
		}
		
		function selectFBPageBoxEditor(){
				tb_show("Select Page", "<?php echo fb_get_option('siteurl'); ?>?fbconnect_action=userpages&height=450&width=630&callback=callbackSelectPageBoxEditor", "");
		}
		</script>
		
		<?php if ($showType){?>
	  		<label for="_fbconnect_og_type" class="fblabelform">
	  		<?php _e("OpenGraph Type:", 'fbconnect' );?>
	  		</label>
	  		<?php //WPfbConnect_Interface::print_ogtypes_select($fbconnect_og_type);
	  		?>
			<input type="text" name="_fbconnect_og_type" id="_fbconnect_og_type" value="<?php echo $fbconnect_og_type;?>" size="25"/>		
	  		
	  		<label for="_fbconnect_fb_admins" class="fblabelform">
	  		<?php  _e("Page Admins:", 'fbconnect' );?>
	  		</label>
			<input type="text" name="_fbconnect_fb_admins" id="_fbconnect_fb_admins" value="<?php echo $fbconnect_fb_admins;?>" size="25" /> (Your ID: <?php $fb_user;?>)
		<?php }?>
  	<!-- 		
  		<label for="_fbconnect_access_userid" class="fblabelform">
  		<?php _e("Facebook user ID:", 'fbconnect' );?>
  		</label>
 		<input type="text" name="_fbconnect_access_userid" value="<?php echo $fbconnect_access_userid;?>" size="25" />
 	 -->
 	 <?php 
 	 if(file_exists (FBCONNECT_PLUGIN_PATH.'/pro/fbConnectCanvas.php')){
 	 ?>
 	 <div style="display:block; overflow:hidden;">
 	  	<div class="fbConfigurationCol">
		<label for="_fbconnect_access_page_check" class="fblabelform">
		<input type="checkbox" name="_fbconnect_access_page_check" id="_fbconnect_access_page_check" <?php
								if( get_post_meta($post_id, '_fbconnect_access_page_check',true) ) echo 'checked="checked"'
								?> />
  		&nbsp;<?php _e("Check fan page user.", 'fbconnect' );?>
  		</label>
  		
		<label for="_fbconnect_access_page_thick" class="fblabelform">
		<input type="checkbox" name="_fbconnect_access_page_thick" id="_fbconnect_access_page_thick" <?php
								if( get_post_meta($post_id, '_fbconnect_access_page_thick',true) ) echo 'checked="checked"'
								?> />
  		&nbsp;<?php _e("Use thick box.", 'fbconnect' );?>
  		</label>	
  		
  		<div style="display:block;overflow:hidden;">
		<div style="float:left;width:155px;">
		<label for="_fbconnect_access_pageid" class="fblabelform"><?php _e('Page ID:', 'fbconnect') ?></label>
		<input type="text" size="20" name="_fbconnect_access_pageid" id="_fbconnect_access_pageid" value="<?php echo $fbconnect_access_pageid; ?>"/><br/>
		</div>
		<div id="pagepic" style="float:left;width:80px;">
		<fb:profile-pic size="square" uid="<?php echo $fbconnect_access_pageid; ?>" linked="true" />
		</div>
		</div>
		<label for="_fbconnect_access_page_name" class="fblabelform"><?php _e('Page name:', 'fbconnect') ?></label>
		<input type="text" size="40" name="_fbconnect_access_page_name" id="_fbconnect_access_page_name" value="<?php echo $fbconnect_access_page_name; ?>"/><br/>
		
		<label for="_fbconnect_access_page_url" class="fblabelform"><?php _e('Page URL:', 'fbconnect') ?></label>
		<input type="text" size="40" name="_fbconnect_access_page_url" id="_fbconnect_access_page_url" value="<?php echo $fbconnect_access_page_url; ?>"/>
		<br/>
		<span class="submit">
		<input class="button" type="button" onclick="selectFBPageBoxEditor();" name="selectPage" value="<?php _e('Select page', 'fbconnect') ?> &raquo;" /> 
		</span>
		</div>
		<?php 
		if($showHeaderFooterFanText){?>
		<div class="fbConfigurationCol">
		<label for="_fbconnect_access_page_text" class="fblabelform"><?php _e('Header fan text:', 'fbconnect') ?></label>
		<textarea cols=30 rows=5 name="_fbconnect_access_page_text" id="_fbconnect_access_page_text"><?php echo $fbconnect_access_page_text; ?></textarea>
		<label for="_fbconnect_access_page_text_footer" class="fblabelform"><?php _e('Footer fan text:', 'fbconnect') ?></label>
		<textarea cols=30 rows=5 name="_fbconnect_access_page_text_footer" id="_fbconnect_access_page_text_footer"><?php echo $fbconnect_access_page_text_footer; ?></textarea>
		</div>
		<?php 
		}?>
	</div>
  		<?php 
 	 }
	}


	/**
	 * Setup admin menus for fbconnect options and ID management.
	 *
	 * @action: admin_menu
	 **/
	function add_admin_panels() {
		if (function_exists('add_menu_page')) {
			add_menu_page("Facebook Connector", "Facebook", 8, 'fbconnect/fbConnectInterface.php', array( 'WPfbConnect_Interface', 'options_page'), plugins_url('fbconnect/images/facebook-16.png'));
			add_menu_page("Google Connector", "Google", 8, 'fbconnect/gConnectInterface.php', array( 'WPgConnect_Interface', 'options_page'), plugins_url('fbconnect/images/gplus-16.png'));
			add_menu_page("Twitter Connector", "Twitter", 8, 'fbconnect/tConnectInterface.php', array( 'WPtConnect_Interface', 'options_page'), plugins_url('fbconnect/images/twitter-16.png'));
			if(file_exists (FBCONNECT_PLUGIN_PATH.'/pro/tuentiConnectLogic.php')){
				add_menu_page("Tuenti Connector", "Tuenti", 8, 'fbconnect/tuentiConnectInterface.php', array( 'WPtuentiConnect_Interface', 'options_page'), plugins_url('fbconnect/images/tuenti-16.png'));
			}
		}
		if (function_exists('add_submenu_page')) {
			add_submenu_page('fbconnect/fbConnectInterface.php', __('Main options', 'fbconnect'), __('Main options', 'fbconnect'), 8, 'fbconnect/fbConnectInterface.php',array( 'WPfbConnect_Interface', 'options_page'));
			add_submenu_page('fbconnect/fbConnectInterface.php', __('Comments', 'fbconnect'), __('Comments', 'fbconnect'), 8, 'fbconnect/fbConnectTemplates.php');		
			add_submenu_page('fbconnect/fbConnectInterface.php', __('Open Graph', 'fbconnect'), __('Open Graph', 'fbconnect'), 8, 'fbconnect/fbConnectOpenGraph.php');		
			add_submenu_page('fbconnect/fbConnectInterface.php', __('Share', 'fbconnect'), __('Share', 'fbconnect'), 8, 'fbconnect/fbConnectShare.php');		
			add_submenu_page('fbconnect/fbConnectInterface.php', __('Registration form', 'fbconnect'), __('Registration form', 'fbconnect'), 8, 'fbconnect/fbConnectRegister.php');		
			
			if(file_exists (FBCONNECT_PLUGIN_PATH.'/pro/fbConnectCanvas.php')){
			    add_submenu_page('fbconnect/fbConnectInterface.php', __('Facebook tags', 'fbconnect'), __('Facebook tags', 'fbconnect'), 8, 'fbconnect/pro/fbConnectTags.php');
				add_submenu_page('fbconnect/fbConnectInterface.php', __('Page tab & canvas', 'fbconnect'), __('Page tab & canvas', 'fbconnect'), 8, 'fbconnect/pro/fbConnectCanvas.php');
				add_submenu_page('fbconnect/fbConnectInterface.php', __('Offline options', 'fbconnect'), __('Offline options', 'fbconnect'), 8, 'fbconnect/pro/fbConnectOfflineOptions.php');		
			}
			
			add_submenu_page('fbconnect/gConnectInterface.php', __('Main options', 'fbconnect'), __('Main options', 'fbconnect'), 8, 'fbconnect/gConnectInterface.php',array( 'WPgConnect_Interface', 'options_page'));
			//add_submenu_page('fbconnect/gConnectInterface.php', __('Share', 'fbconnect'), __('Share', 'fbconnect'), 8, 'fbconnect/gConnectShare.php');		
		
			//if(file_exists (FBCONNECT_PLUGIN_PATH.'fbConnectInfo.php')){
				add_submenu_page('fbconnect/fbConnectInterface.php', __('Sociable!', 'fbconnect'), __('Sociable!', 'fbconnect'), 8, 'fbconnect/fbConnectInfo.php');		
			//}
		}

	}


	function register_feed_forms($fb_online_stories,$fb_short_stories_title,$fb_short_stories_body,$fb_full_stories_title,$fb_full_stories_body) {
	  $one_line_stories = $short_stories = $full_stories = array();
	
	  $one_line_stories[] = $fb_online_stories;
	  $short_stories[] = array('template_title' => $fb_short_stories_title,
	                         'template_body' => $fb_short_stories_body);
	  $full_stories = array('template_title' => $fb_full_stories_title,
	                         'template_body' => $fb_full_stories_body);
	  $form_id = fb_feed_registerTemplateBundle($one_line_stories,$short_stories,$full_stories);
		
	  return $form_id;
	}

	/*
	 * Display and handle updates from the Admin screen options page.
	 *
	 * @options_page
	 */
	function options_page() {
		global $wp_version, $fbconnect,$fb_reg_formfields;

			// if we're posted back an update, let's set the values here
			if ( isset($_POST['clean_log']) ) {
				unlink(FBCONNECT_PLUGIN_PATH_LOG);
			}elseif ( isset($_POST['migrate_data']) ) {
				$store =& WPfbConnect_Logic::getStore();
				$migrationresponse = $store->migration_adamplugin();
				echo '<div class="updated"><p><strong>'.__('Migration done...', 'fbconnect').'</strong></p>';
				echo $migrationresponse;
				echo '</div>';
			}elseif ( isset($_POST['info_update']) ) {
				check_admin_referer('wp-fbconnect-info_update');

				$error = '';
				update_option( 'fb_facebooklogin_enabled', $_POST['fb_facebooklogin_enabled'] );
				update_option( 'fb_api_key', $_POST['fb_api_key'] );
				update_option( 'fb_appId', $_POST['fb_appId'] );
				update_option( 'fb_api_secret', $_POST['fb_api_secret'] );
				update_option('fb_connect_use_thick',$_POST['fb_connect_use_thick']);
				update_option('fb_connect_avatar_logo',$_POST['fb_connect_avatar_logo']);
				update_option('fb_communitynumimgs',$_POST['fb_communitynumimgs']);
				$loglevel = $_POST['fb_connect_log_level'];
				update_option('fb_connect_log_level',$loglevel);				
				update_option('fb_connect_avatar_link',$_POST['fb_connect_avatar_link']);
				update_option('fb_removeadminbar',$_POST['fb_removeadminbar']);
				update_option('fb_ssllinkrewrite',$_POST['fb_ssllinkrewrite']);
				update_option('fb_friendsstorage',$_POST['fb_friendsstorage']);
				update_option('fb_loadfacebooklib',$_POST['fb_loadfacebooklib']);
				update_option('fb_locale',$_POST['fb_locale']);
				if ($error !== '') {
					echo '<div class="error"><p><strong>'.__('At least one of Facebook Connector options was NOT updated', 'fbconnect').'</strong>'.$error.'</p></div>';
				} else {
					echo '<div class="updated"><p><strong>'.__('Facebook Connector options updated', 'fbconnect').'</strong></p></div>';
				}

			
			}
			
			// Display the options page form
			$siteurl = fb_get_option('home');
			if( substr( $siteurl, -1, 1 ) !== '/' ) $siteurl .= '/';
			?>
			<div class="wrap">
				<h2><img src="<?php echo FBCONNECT_PLUGIN_URL;?>/images/facebook-20.png"/>
					<?php _e('Facebook Configuration', 'fbconnect') ?></h2>

				<form method="post">


					<h3><?php _e('Facebook Application Configuration', 'fbconnect') ?></h3>
     				<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Facebook App. Config.', 'fbconnect') ?></th>
							<td>
							<a href="http://www.facebook.com/developers/" target="_blank"><?php _e('Create a new Facebook Application', 'fbconnect') ?></a> 
							<b><a href="http://www.sociable.es/facebook-opengraph-wordpress-plugin-configuration/" target="_blank">[<?php _e('View tutorial', 'fbconnect') ?>]</a></b>

							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Enable Facebook login:', 'fbconnect') ?></th>
							<td>
								<p><input type="checkbox" name="fb_facebooklogin_enabled" id="fb_facebooklogin_enabled" <?php
								if( fb_get_option('fb_facebooklogin_enabled')) echo 'checked="checked"'
								?> />
							</td>
						</tr>
						<?php if( fb_get_option('fbc_app_key_option')!="") : ?>
     					<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Migrate user data', 'fbconnect') ?></th>
							<td>
							<input class="button-primary" type="submit" name="migrate_data" value="<?php _e('Migrate from wp-facebookconnect', 'fbconnect') ?>"/> <?php _e('(It is strongly recommended to do a backup before)', 'fbconnect'); ?>
							</td>
						</tr>
						<?php endif; ?>
						<tr valign="top">
							<th style="width: 33%" scope="row"><label for="fb_appId"><?php _e('Facebook App ID:', 'fbconnect') ?></label></th>
							<td>
							<input type="text" name="fb_appId" id="fb_appId" size="50" value="<?php echo fb_get_option('fb_appId');?>"/>
							</td>
						</tr>
						<tr valign="top" style="display:none;">
							<th style="width: 33%" scope="row"><label for="fb_api_key"><?php _e('Facebook API Key:', 'fbconnect') ?></label></th>
							<td>
							<input type="text" name="fb_api_key" id="fb_api_key" size="50" value="<?php echo fb_get_option('fb_api_key');?>"/>
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><label for="fb_api_secret"><?php _e('Facebook API Secret:', 'fbconnect') ?></label></th>
							<td>
							<input type="text" name="fb_api_secret" size="50" id="fb_api_secret" value="<?php echo fb_get_option('fb_api_secret');?>"/>
							</td>
						</tr>							
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Language:', 'fbconnect') ?></th>
							<td>
								<select name="fb_locale" id="fb_locale">
								<option value="" <?php if( fb_get_option('fb_locale')=="") echo 'selected="selected"'; ?>></option>
								<?php 
								$xml = file_get_contents ( FBCONNECT_PLUGIN_PATH."/FacebookLocales.xml" );
		
								$locales = new SimpleXMLElement ( $xml );
								$locales = $locales->locale;
								foreach($locales as $locale){
									$code = $locale->codes->code->standard->representation;
									$desc = $locale->englishName;
								?>
								<option value="<?php echo $code;?>" <?php if( fb_get_option('fb_locale')==$code) echo 'selected="selected"'; ?>><?php echo $desc;?></option>
								<?php } ?>
								</select>
								
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Avatar link:', 'fbconnect') ?></th>
							<td>
								<select name="fb_connect_avatar_link" id="fb_connect_avatar_link">
								<option value="" <?php if( fb_get_option('fb_connect_avatar_link')=="") echo 'selected="selected"'; ?>>Link user avatars to a local profile page</option>
								<option value="on" <?php if( fb_get_option('fb_connect_avatar_link')=='on') echo 'selected="selected"'; ?>>Link user avatars to Facebook profiles</option>
								<option value="off" <?php if( fb_get_option('fb_connect_avatar_link')=='off' ) echo 'selected="selected"'; ?>>Don't link user avatars</option>
								</select>
								
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Use ThickBox:', 'fbconnect') ?></th>
							<td>
								<p><input type="checkbox" name="fb_connect_use_thick" id="fb_connect_use_thick" <?php
								if( fb_get_option('fb_connect_use_thick') ) echo 'checked="checked"'
								?> />
									<label for="fb_connect_use_thick"><?php _e('Load user info on a ThickBox.', 'fbconnect') ?></label></p>

							</td>
						</tr>
						
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Avatar FB logo:', 'fbconnect') ?></th>
							<td>
								<p><input type="checkbox" name="fb_connect_avatar_logo" id="fb_connect_avatar_logo" <?php
								if( fb_get_option('fb_connect_avatar_logo') ) echo 'checked="checked"'
								?> />
									<label for="fb_connect_avatar_logo"><?php _e('Show Facebook logo in user avatars.', 'fbconnect') ?></label></p>

							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><label for="fb_communitynumimgs"><?php _e('Community page user images:', 'fbconnect') ?></label></th>
							<td>
							<?php 
							$fb_communitynumimgs = fb_get_option('fb_communitynumimgs');
							if ($fb_communitynumimgs==""){
								$fb_communitynumimgs="50";
							}
							?>
							<input type="text" name="fb_communitynumimgs" id="fb_communitynumimgs" size="10" value="<?php echo $fb_communitynumimgs;?>"/>
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Remove Wordpress admin bar:', 'fbconnect') ?></th>
							<td>
								<p><input type="checkbox" name="fb_removeadminbar" id="fb_removeadminbar" <?php
								if( fb_get_option('fb_removeadminbar') ) echo 'checked="checked"'
								?> />
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Enable SSL link rewrite:', 'fbconnect') ?></th>
							<td>
								<p><input type="checkbox" name="fb_ssllinkrewrite" id="fb_ssllinkrewrite" <?php
								if( fb_get_option('fb_ssllinkrewrite') ) echo 'checked="checked"'
								?> />
							</td>
						</tr>
						<tr valign="top">
								<th style="width: 33%" scope="row"><?php _e('Disable friends storage:', 'fbconnect') ?></th>
								<td>
									<p><input type="checkbox" name="fb_friendsstorage" id="fb_friendsstorage" <?php
									if( fb_get_option('fb_friendsstorage')) echo 'checked="checked"'
									?> />
								</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Don\'t load Facebook lib:', 'fbconnect') ?></th>
							<td>
								<p><input type="checkbox" name="fb_loadfacebooklib" id="fb_loadfacebooklib" <?php
								if( fb_get_option('fb_loadfacebooklib') ) echo 'checked="checked"'
								?> />
								<label for="fb_loadfacebooklib"><?php _e('Check this options if other plugin is loading de Facebook Javascript lib.', 'fbconnect') ?></label></p>

							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Log level:', 'fbconnect') ?></th>
							<td>
								<select name="fb_connect_log_level" id="fb_connect_log_level">
								<option value="-1" <?php if( fb_get_option('fb_connect_log_level')=="") echo 'selected="selected"'; ?>></option>
								<option value="1" <?php if( fb_get_option('fb_connect_log_level')=='1' ) echo 'selected="selected"'; ?>>Emergency</option>
								<option value="2" <?php if( fb_get_option('fb_connect_log_level')=='2' ) echo 'selected="selected"'; ?>>Error</option>
								<option value="3" <?php if( fb_get_option('fb_connect_log_level')=='3' ) echo 'selected="selected"'; ?>>Warning</option>
								<option value="4" <?php if( fb_get_option('fb_connect_log_level')=='4' ) echo 'selected="selected"'; ?>>Info</option>
								<option value="5" <?php if( fb_get_option('fb_connect_log_level')=='5' ) echo 'selected="selected"'; ?>>Debug</option>
								</select>

								<a class="button" target="_blank" href="<?php echo FBCONNECT_PLUGIN_URL_LOG; ?>"><?php _e('View log', 'fbconnect') ?></a> <input class="button" type="submit" name="clean_log" value="<?php _e('Clean log', 'fbconnect') ?> &raquo;" />

							</td>
						</tr>

     				</table>

					
					<?php wp_nonce_field('wp-fbconnect-info_update'); ?>
					
     				<p class="submit"><input class="button-primary" type="submit" name="info_update" value="<?php _e('Update Configuration', 'fbconnect') ?> &raquo;" /></p>
     			</form>
				
			</div>
    			<?php
	} // end function options_page


}
endif;

?>
