<?php
/*
Plugin Name: Cool Popular Posts
Plugin URI: http://wordpress.org/extend/plugins/cool-popular-post/ 
Description: An easy to use WordPress function to add Popular Posts to any theme.
Author: Jake Landers
Tags: future, upcoming posts, upcoming post, upcoming, draft, Post, popular, preview, plugin, post, posts
Author URI: 
Version: 1.0
*/


/*  
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    ( at your option ) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_shortcode( 'cool_popular_posts', 'cool_popular_posts' );

function cool_popular_posts( $options = '' ) {
	$ns_options = array(
		"count"    => "10",
		"comments" => "0",
		"before"   => "<li>",
		"after"    => "</li>\n",
		"order"    => "desc",
		"nofollow" => false,
		"excerpt" => false,
		"creditlink" => false,
		"featureimage" => false,
		"link" => true,
		"displaytype" => "comment",
		"show"     => true
	 );



	$options = explode( "&", $options );
	foreach ( $options as $option ) {
		$parts = explode( "=", $option );
		$ns_options[$parts[0]] = $parts[1];
	}


	if ( $ns_options['displaytype'] == 'comment' ) {

		if ( strtolower( $ns_options['order'] ) == "desc" ) {
		$sqlorder = "ORDER BY comment_count DESC";
		}
		if ( strtolower( $ns_options['order'] ) == "asc" ) {
			$sqlorder = "ORDER BY comment_count ASC";
		}
		if ( strtolower( $ns_options['order'] ) == "rand" ) {
			$sqlorder = "ORDER BY RAND( )";
		}
		global $wpdb;
		$popular_posts = $wpdb->get_results( "
			SELECT ID
			FROM " . $wpdb->posts . "
			WHERE post_type='post' AND post_status = 'publish' AND comment_count >= " . $ns_options['comments']."
			" . $sqlorder . " LIMIT 0 , " . $ns_options['count']
		 );

	} else if ( $ns_options['displaytype'] == 'total' ) { $myposts = cool_popular_posts_objectToArray( json_decode( get_option( "cool_popular_posts_total" ) ) );
	} else if ( $ns_options['displaytype'] == 'monthly' ) { $myposts = cool_popular_posts_objectToArray( json_decode( get_option( "cool_popular_posts_month_".date( 'Y_m' ) ) ) );
	} else if ( $ns_options['displaytype'] == 'weekly' ) { $myposts = cool_popular_posts_objectToArray( json_decode( get_option( "cool_popular_posts_week_".date( 'Y_W' ) ) ) );
	} else if ( $ns_options['displaytype'] == 'daily' ) { $myposts = cool_popular_posts_objectToArray( json_decode( get_option( "cool_popular_posts_day_".date( 'Y_z' ) ) ) );
	}

		if ( count( $myposts )>0 || count( $popular_posts )>0 ) {

			if ( count( $myposts )>0 ) {
				arsort( $myposts );
				if ( $myposts ) {

					foreach ( $myposts as $key=>$value ) {

						if ( count( $popular_posts ) <= $ns_options['count'] && $value > 0 )
							$popular_posts[]->ID = $key;

					}
				}
			}

		if ( count( $popular_posts )>0 ) {
				foreach ( $popular_posts as $post ) {



					if ( $ns_options['before'] == '<li>' ) {

						$post_categories = wp_get_post_categories( $the_post->ID );
						unset( $category_slugs );

						foreach( $post_categories as $category ){
							$category_details = get_category( $category );
							$category_slugs .= ' category-' . $category_details->slug;
						}

						$popular .= "\n" . '<li class="' . trim( $category_slugs ) . '">';
					}
					else
						$popular .= $ns_options['before'];

					$the_post = get_post( $post->ID );

					if ( $ns_options['link'] == 'true' ) {
						$popular .= "<a href='".get_permalink( $the_post->ID )."' ";
						if ( $ns_options['nofollow'] == 'true' ) {$popular .= 'nofollow';}
						$popular .= ">";
					}
					$popular .=  "<span class='title'>".get_the_title( $the_post->ID )."</span>";
					if ( $ns_options['link'] == 'true' ) {$popular .= "</a>";}
					if ( $ns_options['featureimage'] == 'true' ) {
						if ( has_post_thumbnail( $the_post->ID ) ) {$popular .=  "<div class='thumbnail'>".get_the_post_thumbnail( $the_post->ID,'thumbnail' )."</div>";}
					}
					if ( $ns_options['excerpt'] == 'true' ) {$popular .=  "<div class='excerpt'>".$the_post->post_excerpt."</div>";}
					$popular .=  $ns_options['after'];
				}
				if ( $ns_options['creditlink'] == 'true' && is_home( ) ) {
					$popular .=  $ns_options['before']."<a class='creditlink' href='http://wordpress.org/extend/plugins/cool-popular-post/'>Cool Popular Posts WordPress Plugin</a>".$ns_options['after'];
				}
				if ( $ns_options['show'] ) {
					echo $popular;
				} else {
					return $popular;
				}
			}
		}


}

class cool_popular_posts_widget extends WP_Widget
{


	function cool_popular_posts_widget( ){
		$widget_ops = array( 'classname' => 'widget_cool_popular_posts', 'description' => __( "A WordPress widget to add popular posts to any WordPress theme." ) );
		$control_ops = array( 'width' => 300, 'height' => 300 );
		$this->WP_Widget( 'cool_popular_posts_widget', __( 'Cool Popular Posts' ), $widget_ops, $control_ops );
	}

	function update( $new_instance, $old_instance ){
		$instance = $old_instance;
		$instance['title'] = strip_tags( stripslashes( $new_instance['title'] ) );
		$instance['count'] = strip_tags( stripslashes( $new_instance['count'] ) );
		$instance['order'] = strip_tags( stripslashes( $new_instance['order'] ) );
		$instance['link'] = strip_tags( stripslashes( $new_instance['link'] ) );
		$instance['excerpt'] = strip_tags( stripslashes( $new_instance['excerpt'] ) );
		$instance['featureimage'] = strip_tags( stripslashes( $new_instance['featureimage'] ) );
		$instance['creditlink'] = strip_tags( stripslashes( $new_instance['creditlink'] ) );
		$instance['displaytype'] = strip_tags( stripslashes( $new_instance['displaytype'] ) );

		return $instance;
	}

	function form( $instance ){
		$instance = wp_parse_args( ( array ) $instance, array( 'title'=>'Popular Posts', 'count'=>'5', 'order'=>'desc', 'link'=>'false', 'excerpt'=>'false', 'featureimage'=>'false','displaytype'=>'comment','creditlink'=>'false' ) );

		$title = htmlspecialchars( $instance['title'] );
		$count = htmlspecialchars( $instance['count'] );
		$order = htmlspecialchars( $instance['order'] );
		$link = htmlspecialchars( $instance['link'] );
		$excerpt = htmlspecialchars( $instance['excerpt'] );
		$featureimage = htmlspecialchars( $instance['featureimage'] );
		$creditlink = htmlspecialchars( $instance['creditlink'] );
		$displaytype = htmlspecialchars( $instance['displaytype'] );

		for ( $i = 5; $i <= 25; $i=$i+5 ) {
			$countoption .= "<option value='$i' ";
			if ( $count == $i ) {$countoption .= "selected";}
			$countoption .= ">$i</option>";
		}

		if ( $displaytype == "comment" ) {$displaytypeoption .= "<option value='comment' selected >Comment</option>";} else {$displaytypeoption .= "<option value='comment'>Comment</option>";}
		if ( $displaytype == "total" ) {$displaytypeoption .= "<option value='total' selected >Total</option>";} else {$displaytypeoption .= "<option value='total'>Total</option>";}
		if ( $displaytype == "monthly" ) {$displaytypeoption .= "<option value='monthly' selected >Monthly</option>";} else {$displaytypeoption .= "<option value='monthly'>Monthly</option>";}
		if ( $displaytype == "weekly" ) {$displaytypeoption .= "<option value='weekly' selected >Weekly</option>";} else {$displaytypeoption .= "<option value='weekly'>Weekly</option>";}
		if ( $displaytype == "daily" ) {$displaytypeoption .= "<option value='daily' selected >Daily</option>";} else {$displaytypeoption .= "<option value='daily'>Daily</option>";}

		if ( $order == "desc" ) {$orderoption .= "<option value='desc' selected >Descending</option>";} else {$orderoption .= "<option value='desc'>Descending</option>";}
		if ( $order == "asc" ) {$orderoption .= "<option value='asc' selected >Ascending</option>";} else {$orderoption .= "<option value='asc'>Ascending</option>";}


		if ( $link == "true" ) {$linkoption .= "<option value='true' selected >Yes</option>";} else {$linkoption .= "<option value='true'>Yes</option>";}
		if ( $link == "false" ) {$linkoption .= "<option value='false' selected >No</option>";} else {$linkoption .= "<option value='false'>No</option>";}

		if ( $excerpt == "true" ) {$excerptoption .= "<option value='true' selected >Yes</option>";} else {$excerptoption .= "<option value='true'>Yes</option>";}
		if ( $excerpt == "false" ) {$excerptoption .= "<option value='false' selected >No</option>";} else {$excerptoption .= "<option value='false'>No</option>";}

		if ( $featureimage == "true" ) {$featureimageoption .= "<option value='true' selected >Yes</option>";} else {$featureimageoption .= "<option value='true'>Yes</option>";}
		if ( $featureimage == "false" ) {$featureimageoption .= "<option value='false' selected >No</option>";} else {$featureimageoption .= "<option value='false'>No</option>";}

		if ( $creditlink == "true" ) {$creditlinkoption .= "<option value='true' selected >Yes</option>";} else {$creditlinkoption .= "<option value='true'>Yes</option>";}
		if ( $creditlink == "false" ) {$creditlinkoption .= "<option value='false' selected >No</option>";} else {$creditlinkoption .= "<option value='false'>No</option>";}

		# Output the options

		echo '	<p style="text-align:left;"><label for="' . $this->get_field_name( 'title' ) . '">' . __( 'Title:' ) . '</label><br />
				<input style="width: 300px;" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . $title . '" />
				</p>';

		echo '	<p style="text-align:left;"><label for="' . $this->get_field_name( 'count' ) . '">' . __( 'Count:' ) . '</label><br />
				<select id="' . $this->get_field_id( 'count' ) . '" name="' . $this->get_field_name( 'count' ) . '">'.$countoption.'</select>
				</p>';

		echo '	<p style="text-align:left;"><label for="' . $this->get_field_name( 'displaytype' ) . '">' . __( 'Display Type:' ) . '</label><br />
				<select id="' . $this->get_field_id( 'displaytype' ) . '" name="' . $this->get_field_name( 'displaytype' ) . '">'.$displaytypeoption.'</select>
				</p>';

		echo '	<p style="text-align:left;"><label for="' . $this->get_field_name( 'order' ) . '">' . __( 'Order:' ) . '</label><br />
				<select id="' . $this->get_field_id( 'order' ) . '" name="' . $this->get_field_name( 'order' ) . '">'.$orderoption.'</select>
				</p>';

		echo '	<p style="text-align:left;"><label for="' . $this->get_field_name( 'link' ) . '">' . __( 'Include Link?' ) . '</label><br />
				<select id="' . $this->get_field_id( 'link' ) . '" name="' . $this->get_field_name( 'link' ) . '">'.$linkoption.'</select>
				</p>';

		echo '	<p style="text-align:left;"><label for="' . $this->get_field_name( 'featureimage' ) . '">' . __( 'Include Image?' ) . '</label><br />
				<select id="' . $this->get_field_id( 'featureimage' ) . '" name="' . $this->get_field_name( 'featureimage' ) . '">'.$featureimageoption.'</select>
				</p>';

		echo '	<p style="text-align:left;"><label for="' . $this->get_field_name( 'excerpt' ) . '">' . __( 'Include Excerpt?' ) . '</label><br />
				<select id="' . $this->get_field_id( 'excerpt' ) . '" name="' . $this->get_field_name( 'excerpt' ) . '">'.$excerptoption.'</select>
				</p>';

		echo '	<p style="text-align:left;"><label for="' . $this->get_field_name( 'creditlink' ) . '">' . __( 'Include Credit Link?' ) . '</label><br />
				<select id="' . $this->get_field_id( 'creditlink' ) . '" name="' . $this->get_field_name( 'creditlink' ) . '">'.$creditlinkoption.'</select>
				</p>';

	}


	/*  Displays the Widget */
	function widget( $args, $instance ){
		extract( $args );
		$instance = wp_parse_args( ( array ) $instance, array( 'title'=>'Popular Posts', 'count'=>'5', 'order'=>'desc', 'link'=>'false', 'excerpt'=>'false', 'featureimage'=>'false','displaytype'=>'comment','creditlink'=>'false' ) );

		# Before the widget
		echo $before_widget;
		echo '<h3 class="widgettitle">'.$instance['title'].'</h3>';
		echo '<ul>'.cool_popular_posts( 'show=0&link='.$instance['link'].'&count='.$instance['count'].'&order='.$instance['order'].'&excerpt='.$instance['excerpt'].'&displaytype='.$instance['displaytype'].'&featureimage='.$instance['featureimage'].'&creditlink='.$instance['creditlink'] ).'</ul>';
		echo $after_widget;

	}

}// END class

function cool_popular_posts_widget_Init( ) {
	register_widget( 'cool_popular_posts_widget' );
}
add_action( 'widgets_init', 'cool_popular_posts_widget_Init' );

function cool_popular_posts_count( $content ) {
	if ( is_single( ) ) {

		global $post;
		$id = $post->ID;

		$total_counts = 	cool_popular_posts_objectToArray( json_decode( get_option( "cool_popular_posts_total" ) ) );
		$monthly_counts = 	cool_popular_posts_objectToArray( json_decode( get_option( "cool_popular_posts_month_".date( 'Y_m' ) ) ) );
		$weekly_counts = 	cool_popular_posts_objectToArray( json_decode( get_option( "cool_popular_posts_week_".date( 'Y_W' ) ) ) );
		$daily_counts = 	cool_popular_posts_objectToArray( json_decode( get_option( "cool_popular_posts_day_".date( 'Y_z' ) ) ) );


		$total_counts[$id]++;
		$monthly_counts[$id]++;
		$weekly_counts[$id]++;
		$daily_counts[$id]++;

		update_option( "cool_popular_posts_total",json_encode( $total_counts ) );
		update_option( "cool_popular_posts_month_".date( 'Y_m' ),json_encode( $monthly_counts ) );
		update_option( "cool_popular_posts_week_".date( 'Y_W' ),json_encode( $weekly_counts ) );
		update_option( "cool_popular_posts_day_".date( 'Y_z' ),json_encode( $daily_counts ) );

	}
	return $content;
}
add_action( 'the_content', 'cool_popular_posts_count' );

function cool_popular_posts_objectToArray( $d ) {
	if ( is_object( $d ) ) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars( $d );
	}

	if ( is_array( $d ) ) {
		/*
		* Return array converted to object
		* Using __FUNCTION__ ( Magic constant )
		* for recursive call
		*/
		return array_map( __FUNCTION__, $d );
	}
	else {
		// Return array
		return $d;
	}
}
