<?php
/*********************************************************************************************

Adding Translation Option

*********************************************************************************************/
load_theme_textdomain( 'site5framework', get_template_directory().'/languages' );
$locale = get_locale();
$locale_file = get_template_directory()."/languages/$locale.php";
if ( is_readable($locale_file) ) require_once($locale_file);


/*********************************************************************************************

Load site5framework Theme Options

*********************************************************************************************/
require('theme-options.php');


/*********************************************************************************************
/*********************************************************************************************

Add Thumbnail Support

*********************************************************************************************/
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 100, 100, true ); // Normal post thumbnails
add_image_size( 'single-post-thumbnail', 170, 170, TRUE );
add_image_size( 'portfolio-item-small', 300, 250, TRUE );


/*********************************************************************************************

Add Custom Background Support

*********************************************************************************************/
$defaults = array(
	'default-color'          => '000000',
	'wp-head-callback'       => '_custom_background_cb',
	'admin-head-callback'    => '',
	'admin-preview-callback' => ''
);
add_theme_support( 'custom-background', $defaults );


function is_post_type($type){
    global $wp_query;
    if($type == get_post_type($wp_query->post->ID))  { return true; }
    return false;
}

/*********************************************************************************************

Print objects

*********************************************************************************************/

function s5pr($obj) {
	echo "<pre style='clear:both'>";
	print_r($obj);
	echo "</pre>";
}

/*********************************************************************************************

Fix rel validation on category links

*********************************************************************************************/
add_filter( 'the_category', 'add_nofollow_cat' );  
function add_nofollow_cat( $text ) { $text = str_replace('rel="category tag"', "", $text); return $text; }