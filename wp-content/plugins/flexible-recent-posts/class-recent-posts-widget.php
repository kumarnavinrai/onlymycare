<?php
class RecentPostsWidget extends WP_Widget
{
	function __construct() {
		parent::__construct(
			'flexible-recent-posts-widget',
			__( 'Flexible Recent Posts', 'frp' ),
			array( 'description' => __( 'Displays recent posts using flexible template system', 'frp' ) ),
			array( 'width' => 400,
				'height' => 350 )
		);

		if ( is_active_widget( false, false, $this->id_base ) ) {
			add_action( 'wp_head', array( &$this, 'enqueue_styles' ) );
		}
	}

	/**
	 * Returns normalized array of widget params.
	 *
	 * @param array $instance Widget params to normalize.
	 * @return array returns normalized params array.
	 * @uses wp_parse_args()
	 */
	function parse_instance_args( $instance ) {
		$instance = wp_parse_args( (array)$instance,
			array(
				'title' => '',
				'number' => 2,
				'all_posts_link_title' => false,
				'all_posts_link_footer' => true,
				'all_posts_title' => __( 'All news', 'frp' ) . ' &gt;&gt;',
				'all_posts_link' => '',
				'template' => '',
				'theme' => 'default',
				'taxonomy' => 'category',
				// Empty terms array indicates that all taxonomies terms will be included in query.
				'terms' => array()
			)
		);

		if ( empty( $instance['template'] ) ) {
			$default_theme = frp_get_theme( 'default' );

			if ( ! empty( $default_theme ) ) {
				$instance['template'] = $default_theme['template'];
			}
		}

		return $instance;
	}

	function enqueue_styles() {
		$instances = $this->get_settings();

		foreach ( $instances as $instance ) {
			if ( isset( $instance['theme'] ) && $instance['theme'] != 'default' ) {
				$theme = frp_get_theme( $instance['theme'] );

				if ( $theme['css'] ) {
					wp_enqueue_style( 'frp-' . $instance['theme'], $theme['theme_url'] . 'frp-' . $instance['theme'] . '.css' );
				}
			}
		}

		wp_enqueue_style( 'frp-frontend', plugins_url( 'css/frp-front.css', __FILE__ ) );
	}

	function widget( $args, $instance ) {
		global $wp_query;

		extract( $args );

		$instance = $this->parse_instance_args( $instance );

		$instance['terms'] = apply_filters( 'frp_terms', $instance['terms'] );
		$instance['all_posts_title'] = htmlspecialchars( $instance['all_posts_title'] );

		if ( ! empty( $instance['taxonomy'] ) || ! empty( $instance['terms'] ) ) {
			$tmp_wp_query = $wp_query;

			if ( empty( $instance['terms'] ) ) {
				$instance['terms'] = get_terms( $instance['taxonomy'], array( 'fields' => 'ids' ) );
			}

			$query = array(
				'posts_per_page' => $instance['number'],
				'post_type' => 'any',
				'tax_query' => array(
					array(
						'taxonomy' => $instance['taxonomy'],
						'field' => 'id',
						'terms' => $instance['terms']
					)
				)
			);

			$posts = query_posts( $query );

			if ( count( $posts ) > 0 ) {
				$term_link = ( empty( $instance['all_posts_link'] ) ) ? get_term_link( intval( end( $instance['terms'] ) ), $instance['taxonomy'] ) : $instance['all_posts_link'];

				require( 'templates/recent-posts-widget.php' );
			}

			$wp_query = $tmp_wp_query;
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = $new_instance['number'];
		$instance['template'] = $new_instance['template'];
		$instance['taxonomy'] = $new_instance['taxonomy'];
		$instance['terms'] = $new_instance['terms'];
		$instance['all_posts_link_title'] = isset( $new_instance['all_posts_link_title'] );
		$instance['all_posts_link_footer'] = isset( $new_instance['all_posts_link_footer'] );
		$instance['all_posts_title'] = strip_tags( $new_instance['all_posts_title'] );
		$instance['all_posts_link'] = '';
		$instance['theme'] = $new_instance['theme'];

		if ( ! empty( $new_instance['all_posts_link'] ) ) {
			$instance['all_posts_link'] = strip_tags( $new_instance['all_posts_link'] );
		}

		return $instance;
	}

	function form( $instance ) {
		$plugin_dir = plugin_dir_url( __FILE__ );

		$instance = $this->parse_instance_args( $instance );

		$title = strip_tags( $instance['title'] );
		$template = esc_textarea( $instance['template'] );
		$taxonomies = get_taxonomies( array( 'show_ui' => 1 ), 'objects' );

		require_once( 'frp-walker-category-checklist.php' );

		$all_taxonomies_html = array();
		foreach ( $taxonomies as $name => $taxonomy ) {
			$is_all_terms = $instance['taxonomy'] == $name && empty( $instance['terms'] );
			$walker = new FRP_Walker_Category_Checklist( array(
				'field_name' => $this->get_field_name( 'terms' ),
				'terms_disabled' => $is_all_terms
			) );

			ob_start();
			wp_terms_checklist( 0, array(
				'selected_cats' => $instance['terms'],
				'taxonomy' => $name,
				'walker' => $walker
			) );
			$taxonomies_html = ob_get_contents();
			ob_end_clean();

			$all_taxonomies_html[$name] = array(
				'all_items' => $taxonomy->labels->all_items,
				'checked' => $is_all_terms,
				'html_terms' => $taxonomies_html
			);
		}

		require( 'templates/recent-posts-form.php' );
	}
}

?>