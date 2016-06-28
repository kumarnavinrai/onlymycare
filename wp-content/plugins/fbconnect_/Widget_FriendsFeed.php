<?php
 class Widget_FriendsFeed extends WP_Widget {
 	function Widget_FriendsFeed() 
 	{
		$widget_ops = array('description' => __('Faceboook Friends Feed', 'fbconnect'));
		$this->WP_Widget('FriendsFeed', __('Facebook Friends Feed'), $widget_ops);
 		
 	}

 	function widget($args, $instance) {
 		if (!isset($instance) || count($instance)==0){
			
			$instance = Widget_FriendsFeed::init_options($instance);
		}
		
		$title = $instance['title'];
		$welcometext = $instance['welcometext'];
		$maxlastcomments = $instance['maxlastcomments'];
		$avatarsize = $instance['avatarsize'];
		$maxlastcomments = $instance['maxlastcomments'];
		
		extract($args);

		$fb_user = fb_get_loggedin_user();

		$user = wp_get_current_user();
		
		$siteurl = fb_get_option('siteurl');
		
		echo $before_widget.$before_title.$title.$after_title;
			
		$uri = "";
		if (isset($_SERVER["REQUEST_URI"])){
			$uri = $_SERVER["REQUEST_URI"];			
		}
		
		//set_include_path( TEMPLATEPATH . PATH_SEPARATOR . dirname(__FILE__) .PATH_SEPARATOR. WP_PLUGIN_DIR.'/'.FBCONNECT_PLUGIN_BASENAME. PATH_SEPARATOR . get_include_path() );   

		echo '<div  id="fbconnect_feed" class="fbconnect_LastComments" >';
		global $fbconnect_filter;
		$fbconnect_filter="fbFriendsComments";
		include( FBCONNECT_PLUGIN_PATH.'/fbconnect_feed.php');
		echo '</div>';		

		
				
		//restore_include_path();
		
		echo $after_widget;
 		
 	}
 	
 	function init_options($options){
 		if (!isset($options['title'])){
			$options['title'] = "Last Friends Comments";
		}
		if (!isset($options['welcometext'])){
			$options['welcometext'] = "Last friends comments ".fb_get_option('blogname')."!";
		}
		if (!isset($options['maxlastcomments'])){
			$options['maxlastcomments'] = "9";
		}
		
 		if (!isset($options['avatarsize'])){
			$options['avatarsize'] = "50";
		}

		return $options;
		
	}
	
	// When Widget Control Form Is Posted
	function update($new_instance, $old_instance) {
		if (!isset($new_instance['submit'])) {
			return false;
		}
		
		$instance = $old_instance;

		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['welcometext'] = stripslashes($new_instance['welcometext']);
		$instance['maxlastcomments'] = (int)$new_instance['maxlastcomments'];
		$instance['avatarsize'] = (int)$new_instance['avatarsize'];
	
			
		return $instance;
	}
 	
 	// DIsplay Widget Control Form
 function form($instance) {
		global $wpdb;

   		if (!isset($instance) || count($instance)==0){
			$instance = Widget_FriendsFeed::init_options($instance);
		}
		
		$title = htmlspecialchars($instance['title'], ENT_QUOTES);
		$welcometext = htmlspecialchars($instance['welcometext'], ENT_QUOTES);
		$maxlastcomments = htmlspecialchars($instance['maxlastcomments'], ENT_QUOTES);
		$avatarsize = htmlspecialchars($instance['avatarsize'], ENT_QUOTES);
		//fb_get_option('blogname')
?>

		<label for="<?php echo $this->get_field_id('title'); ?>" class="fblabelform" >
		<?php _e('Title:', 'fbconnect');?>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</label>
		
		
		<label for="<?php echo $this->get_field_id('welcometext'); ?>" class="fblabelform">
		<?php _e('Welcome msg:', 'fbconnect');?>
		<input class="widefat" id="<?php echo $this->get_field_id('welcometext'); ?>" name="<?php echo $this->get_field_name('welcometext'); ?>" type="text" value="<?php echo $welcometext;?>" />
		</label>
		
		<label for="<?php echo $this->get_field_id('maxlastcomments'); ?>" class="fblabelform">
		<?php _e('Max comments:', 'fbconnect');?>
		<input class="widefat" id="<?php echo $this->get_field_id('maxlastcomments'); ?>" name="<?php echo $this->get_field_name('maxlastcomments'); ?>" type="text" value="<?php echo $maxlastcomments;?>" />
		</label>
		
		<label for="<?php echo $this->get_field_id('avatarsize'); ?>" class="fblabelform">
		<?php _e('Users photos size:', 'fbconnect');?>
		<input class="widefat" id="<?php echo $this->get_field_id('avatarsize'); ?>" name="<?php echo $this->get_field_name('avatarsize'); ?>" type="text" value="<?php echo $avatarsize;?>" />
		</label>
		
		<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
		
	<?php
	}
	
 }

add_action('widgets_init', 'widget_FriendsFeed_init');
function widget_FriendsFeed_init() {
	register_widget('Widget_FriendsFeed');
}
 
?>