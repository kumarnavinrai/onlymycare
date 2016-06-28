<?php

/*
Plugin Name: Posts Slideshow Widget (Post Image Slider Widget)
Plugin URI: http://wordpress.org/extend/plugins/posts-slideshow-widget
Description: Side bar widget with slider (slideshow) of featured images from posts from a selected category
Version: 1.0
Author: APgossips
Author URI: http://apgossips.com
License: GPLv2
*/


/* Copyright 2013
Anamika Mamidi 
(email :  anamikamamidi@gmail.com)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301
USA
*/

if ( !function_exists("get_option" ) ) {
  exit;  // Direct call is prohibited
}

if (version_compare(PHP_VERSION, '5.0.0', '<')) {
	if ( is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX) ) {
		require_once ABSPATH.'/wp-admin/includes/plugin.php';
		deactivate_plugins( __FILE__ );
		$exit_msg = __('Posts Slideshow Widget plugin requires PHP 5.0 or newer.', 'posts-slideshow-widget').' <a href="http://codex.wordpress.org/Upgrading_WordPress">'.__('Please update!', 'ure').'</a>';
    wp_die( $exit_msg );
	} else {
		return;
	}
}

if (!class_exists('posts_slideshow')) {
	define('POSTS_SLIDESHOW_WIDGET_PLUGIN_FILE', __FILE__ );
  define('POSTS_SLIDESHOW_WIDGET_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );	   
  define('POSTS_SLIDESHOW_WIDGET_IMAGE_META_KEY', 'posts_slideshow_widget_image');  
  define('POSTS_SLIDESHOW_WIDGET_IMAGE_CACHE_DIR', POSTS_SLIDESHOW_WIDGET_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'images'. DIRECTORY_SEPARATOR .'cache');
  define('POSTS_SLIDESHOW_WIDGET_CREDITS_LINK', 'http://apgossips.com');
  define('POSTS_SLIDESHOW_WIDGET_CREDITS_TEXT', 'by APgossips');
  
	require_once(POSTS_SLIDESHOW_WIDGET_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'includes'. DIRECTORY_SEPARATOR . 'class-posts-slideshow-library.php');
  require_once(POSTS_SLIDESHOW_WIDGET_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'includes'. DIRECTORY_SEPARATOR . 'class-posts-slideshow-cache-it.php');  
  require_once(POSTS_SLIDESHOW_WIDGET_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'includes'. DIRECTORY_SEPARATOR . 'class-posts-slideshow-widget.php');
	require_once(POSTS_SLIDESHOW_WIDGET_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'includes'. DIRECTORY_SEPARATOR . 'class-posts-slideshow.php');
  
  
  $psw_library = new Posts_Slideshow_Library('posts_slideshow_widget');
  $psw_cache = new Posts_Slideshow_Cache_It( POSTS_SLIDESHOW_WIDGET_IMAGE_CACHE_DIR, $psw_library, 'psw_cache_');
  $psw_library->set_cache($psw_cache);
  new Posts_Slideshow($psw_library);
}


?>
