<?php

/*

Plugin Name: Status Press Widget

Plugin URI: http://www.briandgoad.com/blog/status-press-widget/

Description: Adds a Widget to display your Facebook/Twitter/Last.FM/Pownce status in your sidebar. 

Version: 1.14

Author: Brian D. Goad

Author URI: http://www.briandgoad.com/blog

*/

/*  

	Copyright 2008  Brian D. Goad  (email : bdgoad@gmail.com)
			& Adam Walker Cleaveland &  C. Scott Andreas, 
			Authors of the original Status Press Plugin


    This program is free software; you can redistribute it and/or modify

    it under the terms of the GNU General Public License as published by

    the Free Software Foundation; either version 2 of the License, or

    (at your option) any later version.



    This program is distributed in the hope that it will be useful,

    but WITHOUT ANY WARRANTY; without even the implied warranty of

    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

    GNU General Public License for more details.



    You should have received a copy of the GNU General Public License

    along with this program; if not, write to the Free Software

    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/
if (!function_exists("htmlspecialchars_decode")) {
	function htmlspecialchars_decode($string,$style=ENT_COMPAT){
        $translation = array_flip(get_html_translation_table(HTML_SPECIALCHARS,$style));
        if($style === ENT_QUOTES){ $translation['&#039;'] = '\''; }
        return strtr($string,$translation);
    }
}

class SPWidget {

	//Set Defaults
	var $default_options = array(
		'title' => "",
		'social' => "",
		'url' => "",
		'num' => "1",
		'status_mods' => "",
		'show_time' => 1,
		'time_mods' => "" 
	);

	var $o;
	
	function SPWidget() {
	
	}
	

	
	//Begin By Checking to See if Widget Can Exist
	function init() {
		if (!function_exists('register_sidebar_widget')) {
			return;
		}
		
		if (!$options = get_option('widget_status_press'))
            $options = array();
			
		$widget_ops = array('classname' => 'widget_status_press', 'description' => 'Add a Social Status to your sidebar');
        $control_ops = array('width' => 200, 'height' => 350, 'id_base' => 'status_press');
        $name = 'Status Press';
		
        $registered = false;
		
		//Register Widgets
        foreach (array_keys($options) as $o) {
            if (!isset($options[$o]['title']))
                continue;
				
            $id = "status_press-$o";
            $registered = true;
            wp_register_sidebar_widget($id, $name, array(&$this, 'widget'), $widget_ops, array( 'number' => $o ) );
            wp_register_widget_control($id, $name, array(&$this, 'control'), $control_ops, array( 'number' => $o ) );
        }
        if (!$registered) {
            wp_register_sidebar_widget('status_press-1', $name, array(&$this, 'widget'), $widget_ops, array( 'number' => -1 ) );
            wp_register_widget_control('status_press-1', $name, array(&$this, 'control'), $control_ops, array( 'number' => -1 ) );
        }
		
	}
	
	function widget($args, $widget_args = 1) {
		//Retrieve Any Arguments
        extract($args);

        if (is_numeric($widget_args))

            $widget_args = array('number' => $widget_args);

        $widget_args = wp_parse_args($widget_args, array( 'number' => -1 ));

        extract($widget_args, EXTR_SKIP);
		
		//Get Options Saved in Control
        $options_all = get_option('widget_status_press');

        if (!isset($options_all[$number]))

            return;

        $this->o = $options_all[$number];
	
		//Set Temp Values (in case they have changed)
		$title = htmlspecialchars($this->o['title'], ENT_QUOTES);
	
		$url = $this->o['url'];
		$num = htmlspecialchars($this->o['num'], ENT_NOQUOTES);
		$status_mods = htmlspecialchars_decode($this->o['status_mods'], ENT_QUOTES);
		$show_time = $this->o['show_time'];
		$time_mods = htmlspecialchars_decode($this->o['time_mods'], ENT_QUOTES);
	
		//Begin Status Press Functions
		
		require_once (ABSPATH . WPINC . '/rss.php');
		
		if($url == '' ){
			if ($title == '') {
				$title = 'Status Press';
			}
			$disp .= '<p>You Must Supply the URL to your Facebook Status RSS Feed </p>';
		} else {
			
			//Adjust cache setting
			if ( !defined('MAGPIE_CACHE_AGE') ) {
			
				define('MAGPIE_CACHE_AGE', 5*60); // five minutes

			}
			
			$rss = fetch_rss($url);
		
			if($rss) {
		
				if ($title == '') {
					$title = $rss->channel[title];
				}
						
				if ($num > 0) $rss->items = array_slice($rss->items, 0, $num);

					foreach($rss->items as $item) {
				
						//Get Status Text
						$status = $item[title];

						if ($status != '') {
					
							$disp .= "\t<p " . $status_mods . ">" . $status;
					
								if($show_time) {
					
									// Get the date + time of the last update from the RSS feed.
									$pubdate = $item[pubdate];

									// Convert this string to a time.
									$pubdate = strtotime($pubdate);

									// Calculate how long it's been since the status was updated.
									$today = time();
									$difference = $today - $pubdate;
						
									// Display how long it's been since the last update.
									$disp .= "</p><p ". $time_mods . ">(Updated ";

									// Show days if it's been more than a day.
									if(floor($difference / 86400) > 0) {
										$disp .= floor($difference / 86400);
										if(floor($difference / 86400) == 1) { $disp .= ' day, '; } else { $disp .= ' days, '; }
										$difference -= 86400 * floor($difference / 86400);
									}

									// Show hours if it's been more than an hour.
									if(floor($difference / 3600) > 0) {				
										$disp .= floor($difference / 3600);
										if(floor($difference / 3600) == 1) { $disp .= ' hour, '; } else { $disp .= ' hours, '; }
										$difference -= 3600 * floor($difference / 3600);
									}

									// Show minutes if it's been more than a minute.
									$disp .= floor($difference / 60);
									$difference -= 60 * floor($difference / 60);
									if(floor($difference / 60) == 1) { $disp .= ' minute, '; } else { $disp .= ' minutes ago)'; }

								}
								
								$disp .= "</p>\n";
						}
					}
			} else {
				if ($title == '') {
					$title = "Status Press";
				}
				$disp .= "\t<p>Status Press Error: Something bad happened! <br /> Here are the variables you entered: <ul><li> Title:".$title."</li><li>URL:".$url."</li><li>Number:".$num."</li><li>RSS: ".$rss."\t</li></ul></p>\n";
			}
		}
		
		//Call Widget
	?>
        <?php echo $before_widget;
		
		
		?>
            <?php echo $before_title
				//Get Title to Display
				. $title
                . $after_title; ?>
				
				<div>
					<?php 
					// Display Widget Content
					echo $disp;
					?>
				</div>
		
		<?php echo $after_widget; ?>
	
	<?php	
	}

	function control($widget_args = 1) {
		global $wp_registered_widgets;
        
		static $updated = false;
		
        if (is_numeric($widget_args))
            $widget_args = array('number' => $widget_args);
        $widget_args = wp_parse_args($widget_args, array('number' => -1));
        extract($widget_args, EXTR_SKIP);
        $options_all = get_option('widget_status_press');
        if (!is_array($options_all))
            $options_all = array();
	
		if (!$updated && !empty($_POST['sidebar'])) {
            $sidebar = (string)$_POST['sidebar'];
 
            $sidebars_widgets = wp_get_sidebars_widgets();
            if (isset($sidebars_widgets[$sidebar]))
                $this_sidebar =& $sidebars_widgets[$sidebar];
            else
                $this_sidebar = array();
 
            foreach ($this_sidebar as $_widget_id) {
                if ('widget_status_press' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number'])) {
                    $widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
                    if (!in_array("status_press-$widget_number", $_POST['widget-id']))
                        unset($options_all[$widget_number]);
                }
            }
            foreach ((array)$_POST['status_press'] as $widget_number => $posted) {
                if (!isset($posted['title']) && isset($options_all[$widget_number]))
                    continue;
 
                $options = array();
 
                $options['title'] = strip_tags(stripslashes($posted['title']));
				$options['social'] = $posted['social'];
				$options['url'] = sanitize_url(strip_tags($posted['url']));
				$options['num'] = intval($posted['num']);
				$options['status_mods'] = strip_tags(stripslashes($posted['status_mods']));
				$options['show_time'] = isset($posted['show_time']);
				$options['time_mods'] = strip_tags(stripslashes($posted['time_mods']));
				
                $options_all[$widget_number] = $options;
            }
            update_option('widget_status_press', $options_all);
            $updated = true;
        }
		
        if (-1 == $number) {
            $wpnm = '%i%';
            $values = $this->default_options;
        }
        else {
            update_option('widget_status_press', $options_all);
			$wpnm = $number;
            $values = $options_all[$number];
        }
		
		//Show Admin Screen
		//Title (REQUIRED)
		echo '<p style="text-align:left;"><label for="status_press-title">' . __('Title (Leave Blank to Pull Title From Feed):', 'status-press-widget') . ' </label><br /><input style="width: 200px;" id="status_press-title" name="status_press['.$wpnm.'][title]" type="text" value="'. htmlspecialchars($values['title'], ENT_QUOTES) .'" /></p>';
	
		//Textbox
		?>
		<script language="javascript">
			function rss(social) {
				actSocial = social.value;
				var txtUrl = document.getElementById("status_press-url[<? echo $wpnm; ?>]")
				function getUserName(actSocial) {
					do {
						var name = prompt("Please input your " + actSocial + " username here:", "");
					} while (name == "");
					if (name!=null && name!="") {
						return name;
					} else {
						social.selectedIndex = 0;
					}
				}
				switch (actSocial) {
					case "Facebook":
						okFB = confirm('Please ensure that you are already logged in to Facebook.');
						if (okFB) {
							alert('On the following page, please find the My Status RSS feed and copy it into the box below.');
							window.open("http://www.facebook.com/minifeed.php?filter=11");
						}
					break;
					case "Twitter":
						name = getUserName(actSocial);
						if (name != null) {
							txtUrl.value = "http://twitter.com/statuses/user_timeline/" + name + ".rss";
						}
					break;
					case "Last.FM":
						name = getUserName(actSocial);
						if (name != null) {
							txtUrl.value = "http://ws.audioscrobbler.com/1.0/user/" + name + "/recenttracks.rss";
						}
					break;
					/* Pownce is no more..
					case "Pownce":
						name = getUserName(actSocial);
						if (name != null) {
							txtUrl.value = "http://pownce.com/feeds/public/" + name + "/";
						}
					break;*/
					/* Jaiku not quite ready...
					case "Jaiku":
						name = getUserName(actSocial);
						if (name != null) {
							txtUrl.value = "http://"+name+".jaiku.com/feed/rss";
						}
					break;*/
				}
			}
			function showHide(that, element) {
				e = document.getElementById(element);
				if (that.checked) {
					e.style.visibility = "visible";
				} else {
					e.style.visibility = "hidden";
				}
			}
			//showHide(document.getElementById("status_press-show_time[<? echo $wpnm; ?>]"), "status_press-time_mods[<? echo $wpnm; ?>]");	
		</script>
		
		<?php
		echo '<p style="text-align:left;"><span style="vertical-align: bottom;">' . __('Social Status Network:  ', 'status-press-widget') . '</span> ';
		echo '<select name="status_press['.$wpnm.'][social]" id="status_press-social" onchange="rss(this)">';
		echo '<option value="" ';
			if($values['social'] == "") {
				echo ' selected="selected"';
			}
		echo '>';
		echo '<option value="Facebook"';
			if($values['social'] == "Facebook") {
				echo ' selected="selected"';
			}
		echo '>' . __('Facebook', 'status-press-widget') . '</option>'; 
		echo '<option value="Twitter"';
			if($values['social'] == "Twitter"){
				echo ' selected="selected"';
			}
		echo '>' . __('Twitter', 'status-press-widget') . '</option>'; 
		echo '<option value="Last.FM"';
			if($values['social'] == "Last.FM"){ 
				echo ' selected="selected"';
			}
		echo '>' . __('Last.FM', 'status-press-widget') . '</option>'; 
		/*echo '<option value="Pownce"';
			if($values['social'] == "Pownce"){
				echo ' selected="selected"';
			}
		echo '>' . __('Pownce', 'status-press-widget') . '</option>'; */
		
		/*echo '<option value="Jaiku"';
			if($values['social'] == "Jaiku"){
				echo ' selected="selected"';
			}
		echo '>' . __('Jaiku', 'status-press-widget') . '</option>'; */
		echo '</select></p>';
		echo '<p><label for="status_press-url['.$wpnm.']">' . __('Status URL Feed:', 'status-press-widget') . ' </label><br /><input style="width: 230px;" id="status_press-url['.$wpnm.']" name="status_press['.$wpnm.'][url]" type="text" value="'. $values['url'] .'" /></p>';
		echo '<p style="text-align:left;"><label for="status_press-num">' . __('Number of Status Feeds to Display <br/>(Use "0" to display All):', 'status-press-widget') . ' </label><br /><input style="width: 20px;" id="status_press-num" name="status_press['.$wpnm.'][num]" type="text" value="'. htmlspecialchars($values['num'], ENT_NOQUOTES) .'" /></p>';
		echo '<p style="text-align:left;"><label for="status_press-status_mods">' . __('Stylizing Modifications for Status Tags <br/>(i.e. id="status_press", etc):', 'status-press-widget') . ' </label> <br /><input style="width: 230px;" id="status_press-status_mods" name="status_press['.$wpnm.'][status_mods]" type="text" value="'.	htmlspecialchars($values['status_mods'], ENT_QUOTES) .'" /></p>';
		
		//Checkbox
		echo '<p>';
		if($values['show_time']) {
			$values['show_time'] = 'checked="checked"';
		}

		echo '<label for="status_press-show_time">' . __( 'Show Time Since Status Update: ', 'status-press-widget') . '</label><input type="checkbox" class="checkbox" id="status_press-show_time['.$wpnm.']" name="status_press['.$wpnm.'][show_time]" onClick="showHide(this, \'status_press-time_mods['.$wpnm.']\')" ' . $values['show_time'] . ' />';
		echo '</p>';
	
		//Textbox
		echo '<p style="text-align:left;" id="status_press-time_mods['.$wpnm.']"><label for="status_press-time_mods['.$wpnm.']">' . __('Stylizing Modifications for Time Since Tags <br/>(i.e. id="status-time", etc):', 'status-press-widget') . '  </label><br /><input style="width: 230px;" id="status_press-time_mods['.$wpnm.']" name="status_press['.$wpnm.'][time_mods]" type="text" value="'.	htmlspecialchars($values['time_mods'], ENT_QUOTES) .'" /></p>';
	
		//Hidden element (REQUIRED)
		echo '<input type="hidden" id="status_press-submit" name="status_press['.$wpnm.'][submit]" value="1" />';

	}
	
}

$spw = new SPWidget();
//Initialize Widget on Run
add_action('widgets_init', array($spw, 'init'));
	
?>