<?php

class FRP_Walker_Category_Checklist extends Walker_Category_Checklist
{
	private $field_name;
	/**
	 * @var bool Indicated that all terms must be in disabled state.
	 */
	private $terms_disabled = false;

	function __construct( $params ) {
		$defaults = array(
			'field_name' => 'terms',
			'terms_disabled' => $this->terms_disabled,
		);

		$params = wp_parse_args( $params, $defaults );

		$this->field_name = $params['field_name'];
		$this->terms_disabled = $params['terms_disabled'];
	}

	function start_el( &$output, $category, $depth, $args, $id = 0 ) {
		extract( $args );
		if ( empty( $taxonomy ) )
			$taxonomy = 'category';

		if ( $taxonomy == 'category' )
			$name = 'post_category';
		else
			$name = 'tax_input[' . $taxonomy . ']';

		$class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
		$output .= "\n<li$class>" . '<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="' . $this->field_name . '[]" ' . checked( in_array( $category->term_id, $selected_cats ), true, false ) . disabled( $this->terms_disabled, true, false ) . ' /> ' . esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';
	}
}