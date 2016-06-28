<?php

// Exit if accessed directly
if ( ! defined('ABSPATH') ) exit;

class arpw_widget extends WP_Widget {

	/**
	 * Widget setup
	 */
	function arpw_widget() {
	
		$widget_ops = array( 
			'classname' => 'arpw_widget random-posts', 
			'description' => __( 'Enable advanced random posts widget.', 'arpw' ) 
		);

		$control_ops = array( 
			'width' => 300, 
			'height' => 350, 
			'id_base' => 'arpw_widget' 
		);

		$this->WP_Widget( 'arpw_widget', __( 'Advanced Random Posts', 'arpw' ), $widget_ops, $control_ops );

	}

	/**
	 * Display widget
	 */
	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
 
		$title = apply_filters( 'widget_title', $instance['title'] );
		$limit = $instance['limit'];
		$excerpt = $instance['excerpt'];
		$length = (int)( $instance['length'] );
		$thumb = $instance['thumb'];
		$thumb_height = (int)( $instance['thumb_height'] );
		$thumb_width = (int)( $instance['thumb_width'] );
		$cat = $instance['cat'];
		$date = $instance['date'];
		$css = wp_filter_nohtml_kses( $instance['css'] );

		echo $before_widget;

		if( $css )
		    echo '<style>' . $css . '</style>';
 
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		
			global $post;

			$args = array( 
				'numberposts' => $limit,
				'cat' => $cat,
				'post_type' => 'post',
				'orderby' => 'rand'
			);

		    $arpwwidget = get_posts( $args );

		    if( $arpwwidget ) : ?>

				<div class="arpw-block">
					
					<ul>
						<?php $counter=1; ?>
						<?php foreach( $arpwwidget as $post ) :	setup_postdata( $post ); ?>
							<?php if( has_post_thumbnail() && $thumb == true ) : ?>
			<li class="arpw-clearfix" style="border-bottom:dashed 2px #9C0356;" >
 <div class="arpw-title" >
				<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'arpw' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><span style="font-size:15px;font-weight:bold;color:#9C0356;width:200px" ><?php the_title(); ?></span></a>
								</div>

								<?php if( has_post_thumbnail() && $thumb == true ) { ?>
									
									<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'arpw' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">
										<?php
											if ( current_theme_supports( 'get-the-image' ) )
												get_the_image( array( 'meta_key' => 'Thumbnail', 'height' => $thumb_height, 'width' => $thumb_width, 'image_class' => 'arpw-alignleft', 'link_to_post' => false ) );
											else 
												the_post_thumbnail( array( $thumb_height, $thumb_width ), array( 'class' => 'arpw-alignleft', 'alt' => esc_attr( get_the_title() ), 'title' => esc_attr( get_the_title() ) ) );
										?>
									</a>

								<?php } ?>

								

								<?php if( $date == true ) { ?>
									<span class="arpw-time"><?php echo human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . __( ' ago', 'arpw' ); ?></span>
								<?php } ?>

								<?php if( $excerpt == true ) {  ?>
									<div class="arpw-summary"><?php $arp_excerpt = arpw_excerpt( $length ); $excerptLen = strlen($arp_excerpt); echo substr($arp_excerpt,19,$excerptLen);  ?></div>
								<?php } ?>

							</li>
						<?php $counter++; if($counter==5){ break; } ?>	
                        <?php endif; ?>
						<?php endforeach; wp_reset_postdata(); ?>

					</ul>

				</div><!-- .arpw-block - http://wordpress.org/extend/plugins/advanced-random-posts-widget/ -->

			<?php endif;

		echo $after_widget;
		
	}

	/**
	 * Update widget
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['limit'] = $new_instance['limit'];
		$instance['excerpt'] = $new_instance['excerpt'];
		$instance['length'] = (int)( $new_instance['length'] );
		$instance['thumb'] = $new_instance['thumb'];
		$instance['thumb_height'] = (int)( $new_instance['thumb_height'] );
		$instance['thumb_width'] = (int)( $new_instance['thumb_width'] );
		$instance['cat'] = $new_instance['cat'];
		$instance['date'] = $new_instance['date'];
		$instance['css'] = wp_filter_nohtml_kses( $new_instance['css'] );

		return $instance;

	}

	/**
	 * Widget setting
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
        $defaults = array(
            'title' => '',
            'limit' => 5,
            'excerpt' => '',
            'length' => 10,
            'thumb' => true,
            'thumb_height' => 45,
            'thumb_width' => 45,
            'cat' => '',
            'date' => true,
            'css' => ''
        );
        
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = strip_tags( $instance['title'] );
		$limit = $instance['limit'];
		$excerpt = $instance['excerpt'];
		$length = (int)($instance['length']);
		$thumb = $instance['thumb'];
		$thumb_height = (int)( $instance['thumb_height'] );
		$thumb_width = (int)( $instance['thumb_width'] );
		$cat = $instance['cat'];
		$date = $instance['date'];
		$css = wp_filter_nohtml_kses( $instance['css'] );

	?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'arpw' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php _e( 'Limit:', 'arpw' ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'limit' ); ?>" id="<?php echo $this->get_field_id( 'limit' ); ?>">
				<?php for ( $i=1; $i<=20; $i++ ) { ?>
					<option <?php selected( $limit, $i ) ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'date' ) ); ?>"><?php _e( 'Display date?', 'arpw' ); ?></label>
	      	<input id="<?php echo $this->get_field_id( 'date' ); ?>" name="<?php echo $this->get_field_name( 'date' ); ?>" type="checkbox" value="1" <?php checked( '1', $date ); ?> />&nbsp;
        </p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'excerpt' ) ); ?>"><?php _e( 'Display excerpt?', 'arpw' ); ?></label>
	      	<input id="<?php echo $this->get_field_id( 'excerpt' ); ?>" name="<?php echo $this->get_field_name( 'excerpt' ); ?>" type="checkbox" value="1" <?php checked( '1', $excerpt ); ?> />&nbsp;
        </p>
        <p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'length' ) ); ?>"><?php _e( 'Excerpt length:', 'arpw' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'length' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'length' ) ); ?>" type="text" value="<?php echo $length; ?>" />
		</p>

		<?php if( current_theme_supports( 'post-thumbnails' ) ) { ?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'thumb' ) ); ?>"><?php _e( 'Display thumbnail?', 'arpw' ); ?></label>
		      	<input id="<?php echo $this->get_field_id( 'thumb' ); ?>" name="<?php echo $this->get_field_name( 'thumb' ); ?>" type="checkbox" value="1" <?php checked( '1', $thumb ); ?> />&nbsp;
	        </p>
	        <p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'thumb_height' ) ); ?>"><?php _e( 'Thumbnail size (height x width):', 'arpw' ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'thumb_height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumb_height' ) ); ?>" type="text" value="<?php echo $thumb_height; ?>" />
				<input id="<?php echo esc_attr( $this->get_field_id( 'thumb_width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumb_width' ) ); ?>" type="text" value="<?php echo $thumb_width; ?>" />
			</p>

		<?php } ?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'cat' ) ); ?>"><?php _e( 'Limit to category: ' , 'arpw' ); ?></label>
			<?php wp_dropdown_categories( array( 'name' => $this->get_field_name( 'cat' ), 'show_option_all' => __( 'All categories' , 'arpw' ), 'hide_empty' => 1, 'hierarchical' => 1, 'selected' => $cat ) ); ?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'css' ); ?>"><?php _e( 'Custom CSS:', 'arpw' ); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id( 'css' ); ?>" name="<?php echo $this->get_field_name( 'css' ); ?>" style="height:100px;"><?php echo $css; ?></textarea>
		</p>
		<p>
			<span style="color: #f00;"><?php printf( __( 'Advanced Random Posts Widget is a project by <a href="%s" target="_blank">Satrya</a>', 'arpw' ), esc_url( 'http://satrya.me/' ) ); ?></span>
		</p>

	<?php
	}

}

/**
 * Register widget.
 *
 * @since 1.0
 */
function arpw_register_widget() {
	register_widget( 'arpw_widget' );
}
add_action( 'widgets_init', 'arpw_register_widget' );

/**
 * Print a custom excerpt.
 * http://bavotasan.com/2009/limiting-the-number-of-words-in-your-excerpt-or-content-in-wordpress/
 *
 * @since 1.0
 */
function arpw_excerpt( $length ) {

	$excerpt = explode( ' ', get_the_excerpt(), $length );
	if ( count( $excerpt )>=$length ) {
		array_pop( $excerpt );
		$excerpt = implode( " ", $excerpt ) . '&hellip;';
	} else {
		$excerpt = implode( " ", $excerpt );
	} 
		$excerpt = preg_replace( '`\[[^\]]*\]`', '', $excerpt );

	return $excerpt;

}
?>