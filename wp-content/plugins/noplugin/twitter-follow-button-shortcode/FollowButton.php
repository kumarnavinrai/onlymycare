<?php 
/*
Plugin Name: Twitter Follow Button Shortcode and Widget
Plugin URI: http://muneeb.me/
Description: Shortcode and Widget for creating new Twitter Follow Button.
Version: 1.3
Author: Muneeb ur Rehman
License: GPL2
	Copyright 2011  Muneeb ur Rehman

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

/*
 * Shortcode Syntax
 * [followbutton username='yourusername' count='true' lang='en' theme='light'] 
 * Attributes
 * username - Your twitter username or twitter account you want others to follow - please don't include @ with username
 * count - if true Show follower count, if false does not show follower count
 * lang - default is english(en) use ISO standard two character language codes such as for french(fr)
 * theme= default is light - If you want to use dark theme use [followbutton username='yourusername' theme='dark']
 * ------------
 * default values
 * username=twitter
 * count=true
 * lang=en
 * theme=light
 */

if ( !class_exists('FollowButton') )
{
	class FollowButton
	{
		function __construct(){
			add_shortcode('followbutton',array(&$this,'shortcode'));
		
		}
		function shortcode($atts)
		{
			extract( shortcode_atts( array(
					'username' => 'twitter',
					'count' => 'true',
					'lang' => 'en',
					'theme' => 'light'
				), $atts ) );

			if ( $theme == 'light' )
			return sprintf('<div class="wpfollowbutton"><a href="http://twitter.com/%s" class="twitter-follow-button" data-show-count="%s" data-lang="%s">Follow @twitter</a><script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script></div>',$username,$count,$lang);

			return sprintf('<div class="class="wpfollowbutton"><a href="http://twitter.com/%s" class="twitter-follow-button" data-show-count="%s" data-button="grey" data-text-color="#FFFFFF" data-link-color="#00AEFF" data-lang="%s">Follow @twitter</a>
<script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script></div>',$username,$count,$lang);
				
			
		}
		
		
	}

}	
if ( !class_exists('FollowButtonWidget') )
{

	
	class FollowButtonWidget {
		function __construct()
		{
			$this->data = array('username' => 'twitter',
				'count' => 'true',
				'theme' => 'light',
				'lang' => 'en'
			);
			if ( !get_option('FollowButtonWidget') )
				add_option('FollowButtonWidget',&$this->data);
			
		}
		function control()
		{
			$data = get_option('FollowButtonWidget');
			extract($data);
			echo '<div>';
			
			printf('<label> Username: <input required type="text" placeholder="Twitter Username" name="FollowButtonWidgetUsername" value="%s" /></label>',$username);
			//count section
			if ( $count == 'true' )
			{
				printf('<br /><br /><label>Count: <input type="radio" name="FollowButtonWidgetCount" checked value="%s" />Yes</label>','true');
			printf('<label><input type="radio" name="FollowButtonWidgetCount" value="%s" />No</label>','false');
			}else
			{
				printf('<br /><br /><label>Count: <input type="radio" name="FollowButtonWidgetCount" value="%s" />Yes</label>','true');
			printf('<label><input type="radio" name="FollowButtonWidgetCount" value="%s" checked />No</label>','false');
			}//count section ends

			//theme section start
			if ( $theme == 'light' )
			{
				printf('<br /><br /><label>Theme: <input type="radio" name="FollowButtonWidgetTheme" checked value="%s" />Light</label>','light');
			printf('<label><input type="radio" name="FollowButtonWidgetTheme" value="%s" />Dark</label>','dark');
			}else
			{
				printf('<br /><br /><label>Theme: <input type="radio" name="FollowButtonWidgetTheme" value="%s" />Light</label>','light');
			printf('<label><input type="radio" name="FollowButtonWidgetTheme" value="%s" checked />Dark</label>','dark');
			}//theme section ends
			
			echo '</div>';

			if ( !empty($_POST['FollowButtonWidgetUsername']) )
				$data['username'] = trim($_POST['FollowButtonWidgetUsername']);
		        if ( !empty($_POST['FollowButtonWidgetCount'] ) )
				$data['count'] = $_POST['FollowButtonWidgetCount'];
			if ( !empty($_POST['FollowButtonWidgetTheme'] ) )
				$data['theme'] = $_POST['FollowButtonWidgetTheme'];
			$data['lang'] = 'en';
			update_option('FollowButtonWidget',$data);
  		}
		function widget($args)
		{
			extract(get_option('FollowButtonWidget'));
    			echo $args['before_widget'];
    			echo $args['before_title'] . _('<b>Follow us on Twitter</b>') . $args['after_title'];
    			echo do_shortcode(sprintf('[followbutton username=%s count=%s lang=%s theme=%s]',$username,$count,$lang,$theme));
    			echo $args['after_widget'];
  		}
		function register()
		{
		
wp_register_sidebar_widget(
    'follow_button_1',        // your unique widget id
    'Twitter Follow Button',          // widget name
    array('FollowButtonWidget', 'widget'),  // callback function
    array(                  // options
        'description' => 'Create Twitter Follow Button'
    )
);
		wp_register_widget_control(
    'follow_button_1',        // your unique widget id
    'Twitter Follow Button',          // widget name
    array('FollowButtonWidget', 'control'),  // callback function
    array(                  // options
        'description' => 'Create Twitter Follow Button'
    )
	);	
    		
  		}
}
	
}
new FollowButton();
new FollowButtonWidget();
add_action("widgets_init", array('FollowButtonWidget', 'register'));