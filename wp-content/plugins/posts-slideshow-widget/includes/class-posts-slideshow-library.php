<?php

/**
 * Posts Slideshow Widget WordPress plugin library - common code
 * @Authors: Vladimir Garagulya (shinephp)
 * @package: posts-slideshow-widget
 * 
 */

class Posts_Slideshow_Library {

// general WP plugin related stuff

	private  $options_id = '';
  private  $options = array();
  private  $cache = null;  // cache Object for caching images
  private  $admin_notices = array();

  
  /**
   * public initialazer (constructor) for Posts_Slideshow Library class
   * 
   * @param type $option_name - options ID for this plugin
   * @param type $cache   - object for caching images
   */
  public function __construct($option_name) {

    $this->init_options($option_name);        
    
  }
  // end of __construct()
  
// get current options for this plugin
	private function init_options($options_id) {
    
		$this->options_id = $options_id;
		$this->options = get_option($options_id);    
    
	}
	
	
  public function add_message_for_show($message, $error_style=false) {
  
    if ($message) {
      if ($error_style) {
        $message = '<div id="message" class="error" >'. $message;
      } else {
        $message = '<div id="message" class="updated fade">'. $message;
      }
      $message .= '</div>';
      $this->messages[] = $message;
    }

  }
  // end of add_message_for_show()
  
  
  /**
   * Ouput debug message on screen
   * 
   * @param string $message - debug message
   * 
   */
  public function show_debug_message( $message ) {
    
    echo '<div style="display: block; margin: 5px; padding: 5px; border: 1px solid red; background: #fdfbbc; color: black;">'.$message.'</div>';
    
  }
  // end of show_debug_message()
  
  /**
   * Show messsages, if any, as admin notices
   * 
   */
  public function show_messages($message = '') {
    if ( !empty($message) ) {
      $this->add_message_for_show($message);
    }
    
    if (count($this->messages)) {
      foreach ($this->messages as $message) {
        echo $message;
      }
      $this->messages = array();
    }
    
  }
  // end of show_messages()
  
  
  public function get_request_var($var_name, $request_type='request', $var_type='string') {

    $result = 0;
    if ($request_type == 'get') {
      if (isset($_GET[$var_name])) {
        $result = $_GET[$var_name];
      }
    } else if ($request_type == 'post') {
      if (isset($_POST[$var_name])) {
        if ($var_type!='checkbox') {
          $result = $_POST[$var_name];
        } else {
          $result = 1;
        }
      }
    } else {
      if (isset($_REQUEST[$var_name])) {
          $result = $_REQUEST[$var_name];
      }
    }

    if ($result) {
      if ($var_type == 'int' && !is_numeric($result)) {
        $result = 0;
      }
      if ($var_type!='int') {
        $result = esc_attr($result);
      }
    }
    

    return $result;
  }
  // end of get_request_var
 

  /**
   * read option value by name
   * 
   * @param type $option_name
   * @param type $default
   * @return string returns option value for option with name in $option_name
   * 
   */
  public function get_option($option_name, $default = false) {
    
    if (!empty($this->options[$option_name])) {
      return $this->options[$option_name];
    } else {
      return $default;
    }
    
  }
  // end of get_option()

  
  /**
   * Save option value by name
   * 
   * @param string $option_name
   * @param string $option_value
   * @param boolean $flush_options puts option value according to $option_name option name into options array property
   * 
   */
  public function put_option($option_name, $option_value, $flush_options=false) {
    
    $this->options[$option_name] = $option_value;
    if ($flush_options) {
      $this->flush_options();
    }
    
  }
  // end of put_option()

  
  /** 
   * saves options array into WordPress database wp_options table
   * 
   */
  public function flush_options() {
    
    update_option($this->options_id, $this->options);
    
  }
  // end of flush_options()

  
  /**
   * 
   * @param Class_Posts_Slideshow_Cache_Id $cache initialize image cache object
   * 
   */
  public function set_cache(Posts_Slideshow_Cache_It $cache) {
    
    $this->cache = $cache;
    
  }
  // end of set_cache()
  
    
  /**
   * Returns image URL for the post && stores image URL extracted from post content at the post meta field
   * @global type $blog_id
   * @param type $post  - post object to work with
   * @param type $post_update  - if true, then function is called from post publish action and image URL from the post content should be updated in any case
   * @return string - post image URL
   */
  public function get_post_image( $post=NULL, $post_update=true, $width=0, $height=0 ) {

    if ($post===NULL) {
      return;
    }
    
    if (is_int($post)) {
      $post = get_post($post);
    }
    
    $image_url = '';
    if (function_exists('has_post_thumbnail') && has_post_thumbnail( $post->ID ) ) {
      $images = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );  // available variants: 'thumbnail', 'medium', 'large'
      $image_url = $images[0];
    } else {
      $images_list = get_post_custom_values(POSTS_SLIDESHOW_WIDGET_IMAGE_META_KEY, $post->ID);
      $image_url = $images_list['0'];
      if (empty($image_url) || $post_update) {  // get 1st image from the post content
        $result = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
        if ($result && is_array($matches) && count($matches) && isset($matches[1])) {
          $image_url1 = $matches[1][0];
        } else {
          $image_url1 = '';
        }
        if (!empty($image_url1)) { 
          if ($image_url1!==$image_url) {
            update_post_meta($post->ID, POSTS_SLIDESHOW_WIDGET_IMAGE_META_KEY, $image_url1);
            $this->cache->remove_image($post->ID);
            $image_url = $image_url1;
          }
        } else if (!empty($image_url)) {
          delete_post_meta($post->ID, POSTS_SLIDESHOW_WIDGET_IMAGE_META_KEY);
          $this->cache->remove_image($post->ID);
        }
      }
    }

    if ($post_update) { // code called from the 'publsh_post' action
      return;
    }
    
    if ($image_url && is_multisite() && isset($blog_id) && $blog_id > 0) {
      global $blog_id;
      $image_parts = explode('/files/', $image_url);
      if (isset($image_parts[1])) {
        $image_url = '/blogs.dir/' . $blog_id . '/files/' . $image_parts[1];
      }
    }
  
    if (empty($image_url)) {
      $image_url = plugins_url( 'images'.DIRECTORY_SEPARATOR.'image-not-found-300x300.png', POSTS_SLIDESHOW_WIDGET_PLUGIN_FILE );
    } else {
      $image_url = $this->cache->get_image($post->ID, $image_url, $width, $height);
      if (empty($image_url)) {
        $image_url = plugins_url( 'images'.DIRECTORY_SEPARATOR.'image-not-found-300x300.png', POSTS_SLIDESHOW_WIDGET_PLUGIN_FILE );
      }
    }
            
    return $image_url;    
  }
  // end of get_post_image()
   
  

  
  // build Credits (for plugin producer) link
  public function credits() {
    
    $credits = '<a href="'. POSTS_SLIDESHOW_WIDGET_CREDITS_LINK .'" target="_new">'. POSTS_SLIDESHOW_WIDGET_CREDITS_TEXT .'</a>';
    
    return $credits;
  }
  // end of credits()
    
}
// end of class Posts_Slideshow_Library


?>
