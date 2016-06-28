<?php
/*
Plugin Name: Stop Spam Comments
Description: Dead simple and super lightweight anti-spambot plugin. No captcha, tricky questions or any other user interaction required at all.
Plugin URI: http://wordpress.org/plugins/stop-spam-comments/
Version: 0.2
Author: Pino Ceniccola
Author URI: http://pino.ceniccola.it
License: GPLv2 
*/

add_action('admin_init', 'p_ssc_admin_init');

function p_ssc_admin_init(){
 	add_settings_section('p_ssc_setting_section', 'Stop Spam Comments', 'p_ssc_setting_section_callback', 'discussion');
	add_settings_field('p_ssc_keepspam',  'Keep spam comments', 'p_ssc_setting_input', 'discussion', 'p_ssc_setting_section' );
	register_setting( 'discussion', 'p_ssc_keepspam' );
}

function p_ssc_setting_section_callback() {
 	echo '<p>Option for the Stop Spam Comments plugin</p>';
}

function p_ssc_setting_input() {
 	echo '<input name="p_ssc_keepspam" type="checkbox" value="1" ' . checked( 1, get_option( 'p_ssc_keepspam' ), false ) . ' /> Keep blocked comments in the Spam queue <em>(leave this unchecked if your site gets lots of spam)</em>';
}

add_action('init','p_ssc_init');

function p_ssc_init(){
	// activate only for not logged in users
	if (!is_user_logged_in()) {
		// config the comment form
		add_filter('comment_form_field_comment','p_ssc_config');
		// process the comment
		add_filter('preprocess_comment','p_ssc_process');
		// add a notice and a key for users with no js support
		add_action('comment_form','p_ssc_notice');
	}
}	

function p_ssc_process($commentdata) {
	
	// if this is a trackback or pingback return
	if ($commentdata['comment_type'] != '') return $commentdata;
		
	global $post;
	$key = p_ssc_generateKey($post->ID);	
	
	// if comment has key field return
	if ( isset($_POST['ssc_key_'.$key[0]]) && $_POST['ssc_key_'.$key[0]]==$key[1])  { return $commentdata; }
	
	// else if the key is in the comment content (accessibility, for users with no js support)
	elseif (strpos($commentdata['comment_content'], $key[0].$key[1]) !== false) {
		$commentdata['comment_content'] = str_replace($key[0].$key[1],'',$commentdata['comment_content']);
		return $commentdata;
	}
	
	// no key = comment is spam
	else {
		if (get_option( 'p_ssc_keepspam' )) {
			$commentdata['comment_approved'] = 'spam';
			wp_insert_comment($commentdata);
		}
		wp_die('Notice: It seems you have Javascript disabled in your Browser. In order to submit a comment to this post, please copy the code below the form and paste it along with your comment.');
	}
}

function p_ssc_config($field){
	global $post;
	$key = p_ssc_generateKey($post->ID);
	$field=str_replace('<textarea','<textarea onfocus="if(!this._s==true){var _i=document.createElement(\'input\');_i.setAttribute(\'type\',\'hidden\');_i.setAttribute(\'name\',\'ssc_key_'.$key[0].'\');_i.setAttribute(\'value\',\''.$key[1].'\');var _p=this.parentNode;_p.insertBefore(_i,this);this._s=true;}"',$field);
	return $field;
}

function p_ssc_notice($id) {
	$key = p_ssc_generateKey($id);	
	echo '<noscript><p class="ssc_notice">Notice: It seems you have Javascript disabled in your Browser. In order to submit a comment to this post, please copy this code and paste it along with your comment: <strong>'.$key[0].$key[1].'</strong></p></noscript>';
}

function p_ssc_generateKey($id) {
	$key = md5 ( ABSPATH . $id . AUTH_KEY );
	$key = str_split( $key , 16);
	return $key;
}