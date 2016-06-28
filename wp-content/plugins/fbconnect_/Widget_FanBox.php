<?php
 class Widget_FanBox extends WP_Widget {
 	function Widget_FanBox() 
 	{
		$widget_ops = array('description' => __('Faceboook Fan Box', 'fbconnect'));
		$this->WP_Widget('FanBox', __('Facebook Fan Box'), $widget_ops);
 		
 	}

 	function widget($args, $instance) {
 		if (!isset($instance) || $instance==""){
			
			$instance = Widget_FanBox::init_options($instance);
		}
		
		$title = $instance['title'];
		$profile_id = $instance['profile_id'];
		$profile_url = $instance['profile_url'];
		$page_stream = $instance['page_stream'];
		$connections = $instance['connections'];
		$width = $instance['width'];
		$height = $instance['height'];
		
	
		extract($args);
		$header="true";
		if (!$showHeader){
			$header="false";
		}
		$recommTxt="false";
	
		if ($recommendations){
			$recommTxt="true";
		}
		echo $before_widget.$before_title.$title.$after_title;
		
		
		echo '<fb:like-box href="'.$profile_url.'" stream="'.$page_stream.'" show_faces="true" connections="'.$connections.'" width="'.$width.'" height="'.$height.'" ></fb:like-box>';
		
		echo $after_widget;
 		
 	}
 	
 	function init_options($options){
		if (!isset($options['title'])){
			$options['title'] = "Facebook Fans";
		}
		if (!isset($options['profile_id'])){
			$options['profile_id'] = "";
		}
		if (!isset($options['profile_url'])){
			$options['profile_url'] = "";
		}
		if (!isset($options['connections'])){
			$options['connections'] = "10";
		}
		if (!isset($options['page_stream'])){
			$options['page_stream'] = "1";
		}
		if (!isset($options['width'])){
			$options['width'] = "300";
		}
		if (!isset($options['height'])){
			$options['height'] = "300";
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
		$instance['profile_id'] = stripslashes($new_instance['profile_id']);
		$instance['profile_url'] = stripslashes($new_instance['profile_url']);
		$instance['connections'] = stripslashes($new_instance['connections']);
		if (isset($new_instance['page_stream']))
			$instance['page_stream'] = (int)$new_instance['page_stream'];
		else
			$instance['page_stream'] = 0;
		$instance['width'] = (int)$new_instance['width'];
		$instance['height'] = (int)$new_instance['height'];
			
		return $instance;
	}
 	
 	// DIsplay Widget Control Form
	function form($instance) {
		$title = htmlspecialchars($instance['title'], ENT_QUOTES);
		$profile_id = htmlspecialchars($instance['profile_id'], ENT_QUOTES);
		$profile_url = htmlspecialchars($instance['profile_url'], ENT_QUOTES);
		$connections = htmlspecialchars($instance['connections'], ENT_QUOTES);
		$page_stream = htmlspecialchars($instance['page_stream'], ENT_QUOTES);
		$width = htmlspecialchars($instance['width'] , ENT_QUOTES);
		$height = htmlspecialchars($instance['height'], ENT_QUOTES);
		//fb_get_option('blogname')
		$checked_stream = "";
		if (isset($page_stream) && $page_stream=="1")
			$checked_stream = "checked";
	?>		
			<script type='text/javascript'>
				function callbackSelectPage(pageid,pagename,pageurl){
					jQuery(".FBConnector_FanBox_text").attr("value",pageurl);
					jQuery(".FBConnector_FanBox_pageid").attr("value",pageid);
					tb_remove();
					jQuery(".FBConnector_FanBox_pagepic").html('<fb:profile-pic uid="'+pageid+'" linked="true" />');
					FB.XFBML.parse();
				}
				
				function selectFBPage(){
						tb_show("Select Page", "<?php echo fb_get_option('siteurl'); ?>?fbconnect_action=userpages&height=450&width=630&callback=callbackSelectPage", "");
				}
			</script>	
	
		<label for="<?php echo $this->get_field_id('title'); ?>" class="fblabelform">
		<?php _e('Title:', 'fbconnect');?>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title;?>" />
		</label>
		
		
		<label for="<?php echo $this->get_field_id('profile_id');?>"  class="fblabelform">
		<?php _e('Page URL:', 'fbconnect');?>
		<div style="float:right;height:70px;width:228px;">
		<div style="float:left;width:125px;">
			<input type="hidden" class="FBConnector_FanBox_pageid" name="<?php echo $this->get_field_name('profile_id'); ?>" id="<?php echo $this->get_field_id('profile_id'); ?>" value="<?php echo $profile_id;?>"/>
		<input type="text" size="15" class="FBConnector_FanBox_text" name="<?php echo $this->get_field_name('profile_url'); ?>" id="<?php echo $this->get_field_id('profile_url'); ?>" value="<?php echo $profile_url;?>"/>
		<br/>
		<span class="submit"><input class="button-primary" type="button" onclick="selectFBPage();" name="selectPage" value="<?php _e('Select page', 'fbconnect');?>&raquo" /></span>
		</div>
		<div class="FBConnector_FanBox_pagepic" id="FBConnector_FanBox-profile_id-pagepic" style="float:left;width:80px;">
		<fb:profile-pic uid="<?php echo $profile_id;?>" linked="true" />
		</div>
		</div>
		</label>		
		<label for="<?php echo $this->get_field_id('connections'); ?>" class="fblabelform">
		<?php _e('Connections:', 'fbconnect'); ?><br/>
		<input size="15" id="<?php echo $this->get_field_id('connections'); ?>" name="<?php echo $this->get_field_name('connections'); ?>" type="text" value="<?php echo $connections;?>" />
		</label>
		
		<label for="<?php echo $this->get_field_id('page_stream'); ?>" class="fblabelform">
		<?php _e('Show stream:', 'fbconnect')?>
		<input id="<?php echo $this->get_field_id('page_stream'); ?>" name="<?php echo $this->get_field_name('page_stream'); ?>" type="checkbox" value="1" <?php echo $checked_stream;?> />
		</label>
		
		<label for="<?php echo $this->get_field_id('width'); ?>" class="fblabelform">
		<?php _e('Width:', 'fbconnect');?><br/>
		<input size="15" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width;?>" />
		</label>
		
		<label for="<?php echo $this->get_field_id('height'); ?>" class="fblabelform">
		<?php _e('Height:', 'fbconnect');?><br/>
		<input size="15" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height;?>" />
		</label>
		
		
		<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
	<?php
	}	
	
 }

add_action('widgets_init', 'widget_FanBox_init');
function widget_FanBox_init() {
	register_widget('Widget_FanBox');
}
 
?>