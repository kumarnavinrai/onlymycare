<?php
    /* 
    Plugin Name: WordPress Font Resizer by InoPlugs
    Plugin URI: http://inoplugs.com
    Description: Font Resizer with jQuery and Cookies
    Author: <a href="http://inoplugs.com">InoPlugs</a> | Peter Sch&ouml;nmann
    Version: 1.0.0
    Author URI: http://inoplugs.com
    */
			
	$textdomainname = 'inofontresizer';	
	load_plugin_textdomain( $textdomainname, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    # Add the options/actions to WordPress (if they doesn't exist)
	
	global $ino_fr_options;
	$ino_fr_options = get_option('ino_font_resizer_options');
	
	$ino_fr_options['element'] = empty($ino_fr_options['element']) ? "body" : $ino_fr_options['element'];
	$ino_fr_options['resizeSteps'] = empty($ino_fr_options['resizeSteps']) ? "2" : $ino_fr_options['resizeSteps'];
	$ino_fr_options['cookieTime'] = empty($ino_fr_options['cookieTime']) ? "31" : $ino_fr_options['cookieTime'];
	$ino_fr_options['instancename'] = empty($ino_fr_options['instancename']) ? "inofontresizer" : $ino_fr_options['instancename'];
	$ino_fr_options['symbol1'] = empty($ino_fr_options['symbol1']) ? "A-" : $ino_fr_options['symbol1'];
	$ino_fr_options['symbol2'] = empty($ino_fr_options['symbol2']) ? "A" : $ino_fr_options['symbol2'];
	$ino_fr_options['symbol3'] = empty($ino_fr_options['symbol3']) ? "A+" : $ino_fr_options['symbol3'];
	$ino_fr_options['symbol_1_font_size'] = empty($ino_fr_options['symbol_1_font_size']) ? "20" : $ino_fr_options['symbol_1_font_size'];
	$ino_fr_options['symbol_2_font_size'] = empty($ino_fr_options['symbol_2_font_size']) ? "30" : $ino_fr_options['symbol_2_font_size'];
	$ino_fr_options['symbol_3_font_size'] = empty($ino_fr_options['symbol_3_font_size']) ? "40" : $ino_fr_options['symbol_3_font_size'];
		
		
    # Register an administration page

	add_action('admin_menu', 'inofontResizer_addAdminPage');
	 
    function inofontResizer_addAdminPage() {
        add_options_page(__('Font-Resizer Options',$textdomainname), 'Font-Resizer', 8, 'Font-Resizer', 'inofontResizer_adminmenu');
    }

    # Generates the administration menu
	

	add_action('wp_ajax_inoptions_font_resizer_ajax_save','inoplugs_font_resizer_save_ajax');
	
	function inoplugs_font_resizer_save_ajax() {

		check_ajax_referer('test-inooptions_font_resizer', 'security');

		$data = $_POST['ino_font_resizer_options'];
		unset($data['security'], $data['action']);

		if(update_option('ino_font_resizer_options', $data)) 
		{
			die('1');
		} else {
			die('0');
		}
	}

    function inofontResizer_adminmenu() {
	?>
	<div class="wrap">
		<div id="message"></div>
		
		<script type="text/javascript">
		jQuery(document).ready(function($) {

		  jQuery('form#ino_font_resizer_option_form').submit(function() {
			  var data = jQuery(this).serialize();
			  jQuery.post(ajaxurl, data, function(response) {
				  if(response == 1) {
					  show_message(1);
					  t = setTimeout('fade_message()', 2000);
				  } else {
					  show_message(2);
					  t = setTimeout('fade_message()', 2000);
				  }
			  });
			  return false;
		  });

		});

		function show_message(n) {
			if(n == 1) {
				jQuery('#message').html('<div id="notice" class="updated fade"><p><strong><?php _e('Font Resizer options saved.',$textdomainname); ?></strong></p></div>').show();
			} else {
				jQuery('#message').html('<div id="notice" class="updated fade"><p><strong><?php _e('Font Resizer options saved.',$textdomainname); ?></strong></p></div>').show();
			}
		}

		function fade_message() {
			jQuery('#message').fadeOut(1000);
			clearTimeout(t);
		}
		</script>
		
		<div class="logo"><a href="http://inoplugs.com"><img style="float:left;" src="<?php $ino_urlpath = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),'',plugin_basename(__FILE__)); echo $ino_urlpath; ?>images/logo.png" height="100" alt="logo" /></a>
			<h2 style="float:left; margin-top: 64px;"><?php _e('Font Resizer Option Page',$textdomainname); ?></h2>
		</div>
		<div style="clear: both;"></div>
		
		<form method="post" action="options.php" name="ino_font_resizer_option_form" id="ino_font_resizer_option_form">
		<?php global $ino_fr_options; wp_nonce_field('update-options'); ?>
		
			<div id="ino-options-cont">

				<div id="ino-element" class="tab-option">
					<h3 class="option-title"><strong><?php _e('Element',$textdomainname); ?></strong></h3>
					<div class="option-content">
						<label for="fr_div">
							<input type="text" name="ino_font_resizer_options[element]" value="<?php if( !empty($ino_fr_options['element']) ) { echo $ino_fr_options['element']; }else{ echo ""; } ?>" /><br />
							<?php _e('Enter the element you want to apply the resize effect to (for example: for a span with class "bla" (&lt;span class="bla"&gt;Resizable text&lt;/span&gt;), enter the css definition, "span.bla" (without quotes)). Default element is "body" but you can enter use other element - just enter the id or class of the element you want to apply the resize effect to.',$textdomainname); ?>
						</label><br />

						<div class="clear"></div>
					</div>
				</div>
				
				
				<div id="ino-resize" class="tab-option">
					<h3 class="option-title"><strong><?php _e('Resize Steps',$textdomainname); ?></strong></h3>
					<div class="option-content">
					
					<label for="resizeSteps">
						<input type="text" name="ino_font_resizer_options[resizeSteps]" value="<?php if( !empty($ino_fr_options['resizeSteps']) ) { echo $ino_fr_options['resizeSteps']; }else{ echo "1.6"; } ?>" style="width: 3em"><span style="text-weight:bold"><?php _e('px',$textdomainname); ?></span> 
						<br /><?php _e('Set the resize steps in pixel (default: 2px)',$textdomainname); ?>
					</label>

						<div class="clear"></div>
					</div>
				</div>
				
				<div id="ino-cookie" class="tab-option">
					<h3 class="option-title"><strong><?php _e('Cookie Time',$textdomainname); ?></strong></h3>
					<div class="option-content">
					
					<label for="cookieTime">
						<input type="text" name="ino_font_resizer_options[cookieTime]" value="<?php if( !empty($ino_fr_options['cookieTime']) ) { echo $ino_fr_options['cookieTime']; }else{ echo "31"; } ?>" style="width: 3em"> <span style="text-weight:bold"><?php _e('days',$textdomainname); ?></span>
						<br /><?php _e('Set the cookie store time (default: 31 days)',$textdomainname); ?>
					</label>

						<div class="clear"></div>
					</div>
				</div>
				
				
				<div id="ino-symbol" class="tab-option">
					<h3 class="option-title"><strong><?php _e('Resize Symbols',$textdomainname); ?></strong></h3>
					<div class="option-content">
					
					<label for="symbol1">
						<input type="text" name="ino_font_resizer_options[symbol1]" value="<?php if( !empty($ino_fr_options['symbol1']) ) { echo $ino_fr_options['symbol1']; }else{ echo "A-"; } ?>" style="width: 3em">
						<br /><?php _e('Set the decrease font size symbol',$textdomainname); ?>
					</label>
					<br /><br />
					<label for="symbol2">
						<input type="text" name="ino_font_resizer_options[symbol2]" value="<?php if( !empty($ino_fr_options['symbol2']) ) { echo $ino_fr_options['symbol1']; }else{ echo "A"; } ?>" style="width: 3em">
						<br /><?php _e('Set the default font size symbol',$textdomainname); ?>
					</label>
					<br /><br />
					<label for="symbol3">
						<input type="text" name="ino_font_resizer_options[symbol3]" value="<?php if( !empty($ino_fr_options['symbol3']) ) { echo $ino_fr_options['symbol3']; }else{ echo "A+"; } ?>" style="width: 3em">
						<br /><?php _e('Set the increase font size symbol',$textdomainname); ?>
					</label>

						<div class="clear"></div>
					</div>
				</div>
				
				<div id="ino-symbol-size" class="tab-option">
					<h3 class="option-title"><strong><?php _e('Symbol Sizes',$textdomainname); ?></strong></h3>
					<div class="option-content">
					
					<label for="symbol_1_font_size">
						<input type="text" name="ino_font_resizer_options[symbol_1_font_size]" value="<?php if( !empty($ino_fr_options['symbol_1_font_size']) ) { echo $ino_fr_options['symbol_1_font_size']; }else{ echo "20"; } ?>" style="width: 3em"><span style="text-weight:bold"><?php _e('px',$textdomainname); ?></span>
						<br /><?php _e('Set the decrease font size symbol font size',$textdomainname); ?>
					</label>
					<br /><br />
					<label for="symbol_2_font_size">
						<input type="text" name="ino_font_resizer_options[symbol_2_font_size]" value="<?php if( !empty($ino_fr_options['symbol_2_font_size']) ) { echo $ino_fr_options['symbol_2_font_size']; }else{ echo "30"; } ?>" style="width: 3em"><span style="text-weight:bold"><?php _e('px',$textdomainname); ?></span>
						<br /><?php _e('Set the default font size symbol font size',$textdomainname); ?>
					</label>
					<br /><br />
					<label for="symbol_3_font_size">
						<input type="text" name="ino_font_resizer_options[symbol_3_font_size]" value="<?php if( !empty($ino_fr_options['symbol_3_font_size']) ) { echo $ino_fr_options['symbol_3_font_size']; }else{ echo "40"; } ?>" style="width: 3em"><span style="text-weight:bold"><?php _e('px',$textdomainname); ?></span>
						<br /><?php _e('Set the increase font size symbol font size',$textdomainname); ?>
					</label>

						<div class="clear"></div>
					</div>
				</div>
				
				
				<div id="ino-instance" class="tab-option">
					<h3 class="option-title"><strong><?php _e('Instance',$textdomainname); ?></strong></h3>
					<div class="option-content">
					
					<label for="instance">
						<input type="text" name="ino_font_resizer_options[instance]" value="<?php if( !empty($ino_fr_options['instance']) ) { echo $ino_fr_options['instance']; }else{ echo "inofontresizer"; } ?>" style="width: 200px">
						<br /><?php _e('Set the default instance name',$textdomainname); ?>
					</label>

						<div class="clear"></div>
					</div>
				</div>

		</div>
			<p class="submit">

				<input type="hidden" name="action" value="inoptions_font_resizer_ajax_save" />
				<input type="hidden" name="security" value="<?php echo wp_create_nonce('test-inooptions_font_resizer'); ?>" />
				<input type="submit" class="button-primary" value="<?php _e('Save Changes',$textdomainname) ?>" />
			</p>
		</form>	
	</div>
			
	<?php	
    }
    
    # Sort the dependencies

    function inofontResizer_sortDependencys(){
		if( ! is_admin() )
		{
			$ino_urlpath = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),'',plugin_basename(__FILE__));
			wp_register_script('inofontResizer', $ino_urlpath.'js/jquery.fontsize.js');
			wp_register_script('inofontResizerCookie', $ino_urlpath.'js/jquery.cookie.js');
			wp_enqueue_script('jquery');
			wp_enqueue_script('inofontResizerCookie');
			wp_enqueue_script('inofontResizer');
			
			wp_register_style( 'inofontresizer-style', $ino_urlpath.'css/inofontresizer.css' );
			wp_enqueue_style( 'inofontresizer-style' );
		}
    }
    
    # Generate the inofontresizer text
	

	class inofontresizer_widget extends WP_Widget {
		
		function inofontresizer_widget() {
			//Constructor
			$this->textdomainname = 'inofontresizer';
			global $ino_fr_options;
			$this->ino_options = $ino_fr_options;
			load_plugin_textdomain( $textdomainname, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			$widget_ops = array('classname' => 'inofontresizer_widget', 'description' => 'Font Resizer Widget for WordPress' );
			$this->WP_Widget( 'inofontresizer_widget', __('InoPlugs Font Resizer Widget',$this->textdomainname), $widget_ops );
		}

		function widget($args, $instance) 
		{
			extract($args, EXTR_SKIP);
			echo $before_widget;
			
			$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
			$element = empty($instance['element']) ? $this->ino_options['element'] : $instance['element'];
			$widgetinstance = empty($instance['instance']) ? $this->ino_options['instance'] : $instance['instance'];
			$cookieTime = empty($instance['cookieTime']) ? $this->ino_options['cookieTime'] : $instance['cookieTime'];
			$resizeSteps = empty($instance['resizeSteps']) ? $this->ino_options['resizeSteps'] : $instance['resizeSteps'];
			$symbol_1 = empty($instance['symbol_1']) ? $this->ino_options['symbol1'] : $instance['symbol_1'];
			$symbol_2 = empty($instance['symbol_2']) ? $this->ino_options['symbol2'] : $instance['symbol_2'];
			$symbol_3 = empty($instance['symbol_3']) ? $this->ino_options['symbol3'] : $instance['symbol_3'];
			$symbol_1_font_size = empty($instance['symbol_1_font_size']) ? $this->ino_options['symbol_1_font_size'] : $instance['symbol_1_font_size'];
			$symbol_2_font_size = empty($instance['symbol_2_font_size']) ? $this->ino_options['symbol_2_font_size'] : $instance['symbol_2_font_size'];
			$symbol_3_font_size = empty($instance['symbol_3_font_size']) ? $this->ino_options['symbol_3_font_size'] : $instance['symbol_3_font_size'];
			
			if ( !empty($symbol_1_font_size) ) { $symbol_1_font_size = 'style="font-size: '.$symbol_1_font_size.'px;"';}else{ $symbol_1_font_size = ''; }
			if ( !empty($symbol_2_font_size) ) { $symbol_2_font_size = 'style="font-size: '.$symbol_2_font_size.'px;"';}else{ $symbol_2_font_size = ''; }
			if ( !empty($symbol_3_font_size) ) { $symbol_3_font_size = 'style="font-size: '.$symbol_3_font_size.'px;"';}else{ $symbol_3_font_size = ''; }

			if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
			echo '<div class="nofontResizer_container"><span class="inofontResizer_wrap">';
			echo '<a class="inofontResizer_minus" title="'.__('Decrease font size',$this->textdomainname).'" '.$symbol_1_font_size.'>'.$symbol_1.'</a> ';
			echo '<a class="inofontResizer_reset" title="'.__('Reset font size',$this->textdomainname).'" '.$symbol_2_font_size.'>'.$symbol_2.'</a> ';
			echo '<a class="inofontResizer_add" title="'.__('Increase font size',$this->textdomainname).'" '.$symbol_3_font_size.'>'.$symbol_3.'</a> ';
			echo '<input type="hidden" id="inofontResizer_element" value="'.$element.'" />';
			echo '<input type="hidden" id="inofontResizer_instance" value="'.$widgetinstance.'" />';
			echo '<input type="hidden" id="inofontResizer_resizeSteps" value="'.$resizeSteps.'" />';
			echo '<input type="hidden" id="inofontResizer_cookieTime" value="'.$cookieTime.'" />';
			echo '</span></div>';
			echo $after_widget;
			
			if($title == '')
			{
				$firsttitle = 'no_top_margin';
			}
			
		}
	 
	 
		function update($new_instance, $old_instance) {
			//save the widget
			$instance = $old_instance;	
			foreach($new_instance as $key=>$value)
			{
				$instance[$key]	= strip_tags($new_instance[$key]);
			}
			return $instance;
		}

	 
	 
		function form($instance) 
		{
			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'symbol_1' => '', 'symbol_2' => '', 'symbol_3' => '', 'symbol_1_font_size' => '', 'symbol_2_font_size' => '', 'symbol_3_font_size' => '', 'instance' => '', 'element' => '', 'resizeSteps' => '', 'cookieTime' => '') );
			$title = strip_tags($instance['title']);
			$symbol_1 = empty($instance['symbol_1']) ? $this->ino_options['symbol1'] : $instance['symbol_1'];
			$symbol_2 = empty($instance['symbol_2']) ? $this->ino_options['symbol2'] : $instance['symbol_2'];
			$symbol_3 = empty($instance['symbol_3']) ? $this->ino_options['symbol3'] : $instance['symbol_3'];
			$symbol_1_font_size = empty($instance['symbol_1_font_size']) ? $this->ino_options['symbol_1_font_size'] : strip_tags($instance['symbol_1_font_size']);
			$symbol_2_font_size = empty($instance['symbol_2_font_size']) ? $this->ino_options['symbol_2_font_size'] : strip_tags($instance['symbol_2_font_size']);
			$symbol_3_font_size = empty($instance['symbol_3_font_size']) ? $this->ino_options['symbol_3_font_size'] : strip_tags($instance['symbol_3_font_size']);
			$widgetinstance = empty($instance['instance']) ? $this->ino_options['instance'] : strip_tags($instance['instance']);
			$element = empty($instance['element']) ? $this->ino_options['element'] : strip_tags($instance['element']);
			$resizeSteps = empty($instance['resizeSteps']) ? $this->ino_options['resizeSteps'] : strip_tags($instance['resizeSteps']);
			$cookieTime = empty($instance['cookieTime']) ? $this->ino_options['cookieTime'] : strip_tags($instance['cookieTime']);
			
	?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:',$this->textdomainname) ?> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
			
			<p><label for="<?php echo $this->get_field_id('instance'); ?>"><?php _e('Instance name:',$this->textdomainname) ?>
			<input class="widefat" id="<?php echo $this->get_field_id('instance'); ?>" name="<?php echo $this->get_field_name('instance'); ?>" type="text" value="<?php echo esc_attr($widgetinstance); ?>" /></label></p>
			
			<p><label for="<?php echo $this->get_field_id('element'); ?>"><?php _e('Element you want to apply the font resizer to:',$this->textdomainname) ?>
			<input class="widefat" id="<?php echo $this->get_field_id('element'); ?>" name="<?php echo $this->get_field_name('element'); ?>" type="text" value="<?php echo esc_attr($element); ?>" /></label></p>
			
			<p><label for="<?php echo $this->get_field_id('resizeSteps'); ?>"><?php _e('Resize Steps in px (insert it without "px"):',$this->textdomainname) ?>
			<input class="widefat" id="<?php echo $this->get_field_id('resizeSteps'); ?>" name="<?php echo $this->get_field_name('resizeSteps'); ?>" type="text" value="<?php echo esc_attr($resizeSteps); ?>" /></label></p>
			
			<p><label for="<?php echo $this->get_field_id('cookieTime'); ?>"><?php _e('Cookie Time in days (insert it without "days"):',$this->textdomainname) ?>
			<input class="widefat" id="<?php echo $this->get_field_id('cookieTime'); ?>" name="<?php echo $this->get_field_name('cookieTime'); ?>" type="text" value="<?php echo esc_attr($cookieTime); ?>" /></label></p>
			
			<p><label for="<?php echo $this->get_field_id('symbol_1'); ?>"><?php _e('Symbol for smaller font size:',$this->textdomainname) ?>
			<input class="widefat" id="<?php echo $this->get_field_id('symbol_1'); ?>" name="<?php echo $this->get_field_name('symbol_1'); ?>" type="text" value="<?php echo esc_attr($symbol_1); ?>" /></label></p>
			
			<p><label for="<?php echo $this->get_field_id('symbol_2'); ?>"><?php _e('Symbol for default font size:',$this->textdomainname) ?>
			<input class="widefat" id="<?php echo $this->get_field_id('symbol_2'); ?>" name="<?php echo $this->get_field_name('symbol_2'); ?>" type="text" value="<?php echo esc_attr($symbol_2); ?>" /></label></p>
			
			<p><label for="<?php echo $this->get_field_id('symbol_3'); ?>"><?php _e('Symbol for bigger font size:',$this->textdomainname) ?>
			<input class="widefat" id="<?php echo $this->get_field_id('symbol_3'); ?>" name="<?php echo $this->get_field_name('symbol_3'); ?>" type="text" value="<?php echo esc_attr($symbol_3); ?>" /></label></p>
			
			<p><label for="<?php echo $this->get_field_id('symbol_1_font_size'); ?>"><?php _e('Font size for smaller font size symbol in px (insert it without "px"):',$this->textdomainname) ?>
			<input class="widefat" id="<?php echo $this->get_field_id('symbol_1_font_size'); ?>" name="<?php echo $this->get_field_name('symbol_1_font_size'); ?>" type="text" value="<?php echo esc_attr($symbol_1_font_size); ?>" /></label></p>
			
			<p><label for="<?php echo $this->get_field_id('symbol_2_font_size'); ?>"><?php _e('Font size for default font size symbol in px (insert it without "px"):',$this->textdomainname) ?>
			<input class="widefat" id="<?php echo $this->get_field_id('symbol_2_font_size'); ?>" name="<?php echo $this->get_field_name('symbol_2_font_size'); ?>" type="text" value="<?php echo esc_attr($symbol_2_font_size); ?>" /></label></p>
			
			<p><label for="<?php echo $this->get_field_id('symbol_3_font_size'); ?>"><?php _e('Font size for bigger font size symbol in px (insert it without "px"):',$this->textdomainname) ?>
			<input class="widefat" id="<?php echo $this->get_field_id('symbol_3_font_size'); ?>" name="<?php echo $this->get_field_name('symbol_3_font_size'); ?>" type="text" value="<?php echo esc_attr($symbol_3_font_size); ?>" /></label></p>
						
	<?php
		}
	}



    function inofontResizer_place(){
		global $ino_fr_options; global $textdomainname;
        echo '<div><h3 class="fontresizetitle">'.__('Font Size',$textdomainname).'</h3>';
		echo '<div class="nofontResizer_container"><span class="inofontResizer_wrap" class="inofontResizer">';
		echo '<a class="inofontResizer_minus" title="'.__('Decrease font size',$this->textdomainname).'" '.$ino_fr_options['symbol_1_font_size'].'>'.$ino_fr_options['symbol1'].'</a> ';
		echo '<a class="inofontResizer_reset" title="'.__('Reset font size',$this->textdomainname).'" '.$ino_fr_options['symbol_2_font_size'].'>'.$ino_fr_options['symbol2'].'</a> ';
		echo '<a class="inofontResizer_add" title="'.__('Increase font size',$this->textdomainname).'" '.$ino_fr_options['symbol_3_font_size'].'>'.$ino_fr_options['symbol3'].'</a> ';
		echo '<input type="hidden" id="inofontResizer_element" value="'.$ino_fr_options['element'].'" />';
		echo '<input type="hidden" id="inofontResizer_resizeSteps" value="'.$ino_fr_options['resizeSteps'].'" />';
		echo '<input type="hidden" id="inofontResizer_resizeSteps" value="'.$ino_fr_options['instance'].'" />';
		echo '<input type="hidden" id="inofontResizer_cookieTime" value="'.$ino_fr_options['cookieTime'].'" />';
		echo '</span></div></div>';
    }
	
	# Creating the widget

    add_action('init', 'inofontResizer_sortDependencys');
	
    add_action( 'widgets_init', create_function('', 'return register_widget("inofontresizer_widget");') );
	
	# Creating credits code
	
	function ino_credits() {
		$inoplugs_backlink = apply_filters("inoplugs_backlink", '<div style="display: none;"><a href="http://inoplugs.com">WP-Backgrounds by InoPlugs Web Design</a> and <a href="http://schoenmann.at/">Juwelier Sch&ouml;nmann</a></div>');
		echo $inoplugs_backlink;
	}
	add_action('wp_footer','ino_credits');

    # Register uninstall function

    register_uninstall_hook(__FILE__, 'inofontResizer_uninstaller');
    
    # This function deletes the options when you uninstall the plugin

    function inofontResizer_uninstaller() {
    	delete_option('inoplugs_font_resizer_options');
    }

?>
