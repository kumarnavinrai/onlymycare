<?php
/*
Plugin Name: Answer My Question
Plugin URI: http://netcandy.co
Description: Allow users to ask the site administrator a question. The flow of conversation could either be publicly displayed on the website, or private via direct email communication. 
Version: 1.3
Author: Matt Kaye
Author URI: http://netcandy.co
License: GPL2

Copyright 2012  Matt Kaye  (URL : http://netcandy.co)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once('functions.inc.php');
register_activation_hook(__FILE__, 'amq_install');
register_setting('amq_options', 'amq_option_item', 'amq_options_validate');
add_action('plugins_loaded', 'amq_update_db_check');
add_action('init', 'loadjQuery');
add_action('admin_menu', 'register_amq_menu_page');
add_action('wp_enqueue_scripts', 'loadClientAssets');
add_action('wp_footer', 'insertModal');
add_shortcode('list_amq', 'listAmq');
add_shortcode('amq_modal', 'amqModal');
?>
