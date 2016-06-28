<?php
 class Widget_FacebookConnector extends WP_Widget {
 	function Widget_FacebookConnector() 
 	{
		$widget_ops = array('description' => __('Facebook Connector Main Widget', 'fbconnect'));
		$this->WP_Widget('FacebookConnector', __('Facebook Connector'), $widget_ops);
 		
 	}

 	function widget($args, $options) {

		$options = Widget_FacebookConnector::init_options($options);
		
		$title = $options['title'];
		$welcometext = $options['welcometext'];
		$loginbutton = $options['loginbutton'];
		$maxlastusers = $options['maxlastusers'];
		$avatarsize = $options['avatarsize'];
		$themecolor = $options['themecolor'];

		extract($args);
		
		echo $before_widget.$before_title.$title.$after_title;
?>
		<script>
		var maxlastusers = <?php echo $maxlastusers;?>;
		var avatarsize = <?php echo $avatarsize;?>;
		</script>
<?php
		$uri = "";
		if (isset($_SERVER["REQUEST_URI"])){
			$uri = $_SERVER["REQUEST_URI"];			
		}
		
		//set_include_path( TEMPLATEPATH . PATH_SEPARATOR . dirname(__FILE__) .PATH_SEPARATOR. WP_PLUGIN_DIR.'/'.FBCONNECT_PLUGIN_BASENAME. PATH_SEPARATOR . get_include_path() );   

		include( FBCONNECT_PLUGIN_PATH.'/fbconnect_widget.php');
		
		//restore_include_path();
		
		echo $after_widget;
 		
 	}
 	
 	function init_options($options){
 		if ($options==""){
 			$options = array();
 		}
		if (!isset($options['title'])){
			$options['title'] = "Community";
		}
		if (!isset($options['welcometext'])){
			$options['welcometext'] = "Welcome to ".fb_get_option('blogname')."!";
		}
		if (!isset($options['loginbutton'])){
			$options['loginbutton'] = "medium";
		}
		if (!isset($options['maxlastusers'])){
			$options['maxlastusers'] = "9";
		}
		if (!isset($options['avatarsize'])){
			$options['avatarsize'] = "50";
		}
 		if (!isset($options['themecolor'])){
			$options['themecolor'] = "fbthemelight";
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
		$instance['loginbutton'] = stripslashes($new_instance['loginbutton']);
		$instance['maxlastusers'] = (int)$new_instance['maxlastusers'];
		$instance['avatarsize'] = (int)$new_instance['avatarsize'];
		$instance['themecolor'] = stripslashes($new_instance['themecolor']);
		
		return $instance;
	}
 	
 	// DIsplay Widget Control Form
	function form($instance) {
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('title' => __('Community', 'fbconnect'), 'welcometext' => "Welcome to ".fb_get_option('blogname')."!", 'maxlastusers' => 10, 'avatarsize'=>35));
		
		$title = htmlspecialchars($instance['title'], ENT_QUOTES);
		$welcometext = htmlspecialchars($instance['welcometext'], ENT_QUOTES);
		$loginbutton = htmlspecialchars($instance['loginbutton'], ENT_QUOTES);
		$maxlastusers = htmlspecialchars($instance['maxlastusers'], ENT_QUOTES);
		$avatarsize = htmlspecialchars($instance['avatarsize'], ENT_QUOTES);
		$themecolor = htmlspecialchars($instance['themecolor'], ENT_QUOTES);
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
		
		<label for="<?php echo $this->get_field_id('maxlastusers'); ?>" class="fblabelform">
		<?php _e('Max users photos:', 'fbconnect');?>
		<input class="widefat" id="<?php echo $this->get_field_id('maxlastusers'); ?>" name="<?php echo $this->get_field_name('maxlastusers'); ?>" type="text" value="<?php echo $maxlastusers;?>" />
		</label>
		
		<label for="<?php echo $this->get_field_id('avatarsize'); ?>" class="fblabelform">
		<?php _e('Users photos size:', 'fbconnect');?>
		<input class="widefat" id="<?php echo $this->get_field_id('avatarsize'); ?>" name="<?php echo $this->get_field_name('avatarsize'); ?>" type="text" value="<?php echo $avatarsize;?>" />
		</label>
		<label for="<?php echo $this->get_field_id('loginbutton');?>" class="fblabelform">
		<?php _e('Login button:', 'fbconnect');?>
		<SELECT style="width: 180px;" id="<?php echo $this->get_field_id('loginbutton');?>" name="<?php echo $this->get_field_name('loginbutton');?>">
		<?php 
		echo '<OPTION ';
		if ($loginbutton=="small") echo "SELECTED";
		echo ' VALUE="small">Small</OPTION>';
		echo ' <OPTION ';
		echo '<OPTION ';
		if ($loginbutton=="medium") echo "SELECTED";
		echo ' VALUE="medium">Medium</OPTION>';
		echo ' <OPTION ';
		if ($loginbutton=="large") echo "SELECTED";
		echo ' VALUE="large">Large</OPTION>';
		echo ' <OPTION ';
		if ($loginbutton=="xlarge") echo "SELECTED";
		echo ' VALUE="xlarge">xLarge</OPTION>';
		echo '</SELECT>';
		?>
		</label>
		<label for="<?php echo $this->get_field_id('themecolor');?>" class="fblabelform">
		<?php _e('Your theme color:', 'fbconnect');?>
		<SELECT style="width: 180px;" id="<?php echo $this->get_field_id('themecolor');?>" name="<?php echo $this->get_field_name('themecolor');?>">
		<?php 
		echo '<OPTION ';
		if ($themecolor=="fbthemelight") echo "SELECTED";
		echo ' VALUE="fbthemelight">light</OPTION>';
		echo ' <OPTION ';
		echo '<OPTION ';
		if ($themecolor=="fbthemedark") echo "SELECTED";
		echo ' VALUE="fbthemedark">Dark</OPTION>';
		echo '</SELECT>';
		?>
		</label>
		<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
		
	<?php
	}	
	
 }

add_action('widgets_init', 'widget_FacebookConnector_init');
function widget_FacebookConnector_init() {
	register_widget('Widget_FacebookConnector');
}
 
?>