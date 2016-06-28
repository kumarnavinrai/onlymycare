<?php

/**
 * Posts Slideshow Widget plugin uninstallation script
 * 
 * @author Vladimir Garagulya
 * @package posts-slideshow-widget 
 * 
 */

if( defined( 'ABSPATH') && defined('WP_UNINSTALL_PLUGIN') ) {

	// Remove plugin's settings 
	delete_option('posts_slideshow_widget');
  // Remove all post meta field value for this plugin key
  delete_post_meta_by_key(POSTS_SLIDESHOW_WIDGET_IMAGE_META_KEY);

}

?>