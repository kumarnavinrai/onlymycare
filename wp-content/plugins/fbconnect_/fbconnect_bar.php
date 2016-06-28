<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 23/12/2008
 * @license: GPLv2
 */

?> 

<div id="fb_wpbar">
	<div id="fbheaderbar-title">
		<a class="fbheaderbar-title-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
	</div>
	<div id="fbheaderbar-main">
			<?php 
		$loginbutton = "small";
		$fb_hide_edit_profile = true;
		$fb_hide_invite_link = true;
		$fb_breaklinesprofile = "off";
		$fb_mainavatarsize = 30;
		include FBCONNECT_PLUGIN_PATH."/fbconnect_widget_login.php";
		?>
	</div>
	<div id="fbheaderbar-menu">
		    <a href="javascript:;"></a>
	</div>
</div>

<div id="facebook-sidebarcontainer">
<?php if ( is_active_sidebar( 'facebook-bar' ) ) : ?>
	<div id="facebook-sidebar" class="fbwidget-area widget-area" role="complementary">
	<ul>
		<?php dynamic_sidebar( 'facebook-bar' ); ?>
	</ul>
	</div>
<?php endif; ?>
<?php if ( is_active_sidebar( 'facebook-bar2' ) ) : ?>
	<div id="facebook-sidebar2" class="fbwidget-area widget-area" role="complementary">
	<ul>
		<?php dynamic_sidebar( 'facebook-bar2' ); ?>
	</ul>
	</div>
<?php endif; ?>
</div>

<script type="text/javascript">
	jQuery(document).ready(function(){

		jQuery("#fbheaderbar-menu a").click( function(e) {
			jQuery(this).toggleClass("fbpost-arrow-down");
			jQuery('#facebook-sidebarcontainer').toggle(500);
		});	
	 });					
</script>