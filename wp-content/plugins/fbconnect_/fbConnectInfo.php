<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 05/10/2008
 * @license: GPLv2
 */

include_once FBCONNECT_PLUGIN_PATH.'/fbConfig.php';

	global $wp_version, $fbconnect,$fb_reg_formfields;

			
			// Display the options page form
			$siteurl = fb_get_option('home');
			if( substr( $siteurl, -1, 1 ) !== '/' ) $siteurl .= '/';
			?>
			<div class="wrap">
				<h2><?php _e('Sociable! - Facebook Connect Wordpress Plugin', 'fbconnect') ?></h2>

<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fsociables&amp;width=600&amp;height=590&amp;colorscheme=light&amp;show_faces=true&amp;border_color&amp;stream=true&amp;header=true&amp;appId=62885075047" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:600px; height:590px;" allowTransparency="true"></iframe>

<div style="font-size:12px; padding-left:10px"><a href="http://www.sociable.es/">Sociable - The Social Media Blog</a> </div>


			</div>
