<p>
	<label for="<?php print $this->get_field_id( 'title' ); ?>"><?php print __( 'Title' ); ?>:</label>
	<input class="widefat" id="<?php print $this->get_field_id( 'title' ); ?>"
		   name="<?php print $this->get_field_name( 'title' ); ?>" value="<?php print $title; ?>" type="text">
</p>
<p>
	<label
		for="<?php print $this->get_field_id( 'number' ); ?>"><?php print __( 'Number of posts to show:' ); ?></label>
	<input type="text" size="3" value="<?php print $instance['number']; ?>"
		   name="<?php print $this->get_field_name( 'number' ); ?>"
		   id="<?php print $this->get_field_id( 'number' ); ?>">
</p>
<h4 class="frp-form-group-title"><?php print __( 'All posts link' ); ?>
	<img src="<?php print $plugin_dir . 'images/help.png'; ?>"
		 title="<?php _e( 'Settings of the links to all posts page, that may be displayed near the widget title or after all posts', 'frp' ); ?>"
		 class="frp-form-help" alt="<?php _e( 'Help', 'frp' ); ?>">
</h4>
<p>
	<label for="<?php print $this->get_field_id( 'all_posts_title' ); ?>"><?php print __( 'Link title' ); ?>:</label>
	<input class="widefat" id="<?php print $this->get_field_id( 'all_posts_title' ); ?>"
		   name="<?php print $this->get_field_name( 'all_posts_title' ); ?>"
		   value="<?php print $instance['all_posts_title']; ?>" type="text">
</p>
<p>
	<label for="<?php print $this->get_field_id( 'all_posts_link' ); ?>"><?php print __( 'Static link' ); ?>:</label>
	<input class="widefat" id="<?php print $this->get_field_id( 'all_posts_link' ); ?>"
		   name="<?php print $this->get_field_name( 'all_posts_link' ); ?>"
		   value="<?php print $instance['all_posts_link']; ?>" type="text">
</p>
<p>
	<input type="checkbox" <?php print checked( $instance['all_posts_link_title'], true, false ); ?>
		   name="<?php print $this->get_field_name( 'all_posts_link_title' ); ?>"
		   id="<?php print $this->get_field_id( 'all_posts_link_title' ); ?>">&nbsp;
	<label
		for="<?php print $this->get_field_id( 'all_posts_link_title' ); ?>"><?php print __( 'Show near the widget title', 'frp' ); ?></label><br/>
	<input type="checkbox" <?php print checked( $instance['all_posts_link_footer'], true, false ); ?>
		   name="<?php print $this->get_field_name( 'all_posts_link_footer' ); ?>"
		   id="<?php print $this->get_field_id( 'all_posts_link_footer' ); ?>">&nbsp;
	<label
		for="<?php print $this->get_field_id( 'all_posts_link_footer' ); ?>"><?php print __( 'Show after all posts', 'frp' ); ?></label>
</p>
<h4 class="frp-form-group-title"><?php print __( 'Terms' ); ?><span
		title="<?php _e( 'Toggle all taxonomies list', 'frp' ); ?>"
		class="frp-form-toggle-taxonomies<?php echo ( $instance['taxonomy'] != 'category' ) ? ' visible' : ''; ?>"></span><img
		src="<?php print $plugin_dir . 'images/help.png'; ?>" title="<?php _e( 'Posts terms', 'frp' ); ?>"
		class="frp-form-help" alt="<?php _e( 'Help', 'frp' ); ?>"></h4>
<div class="categorydiv frp-taxonomies frp-form-group">
	<?php foreach ($all_taxonomies_html as $name => $taxonomy): ?>
	<label class="selectit frp-all-taxonomies">
		<input
			type="checkbox"<?php print checked( $taxonomy['checked'], true, false ); ?>
			name="<?php print $this->get_field_name( 'taxonomy' ); ?>"
			value="<?php print $name; ?>"> <?php print $taxonomy['all_items']; ?>
	</label>
	<ul class="list:category categorychecklist form-no-clear">
		<?php print $taxonomy['html_terms']; ?>
	</ul>
	<?php if ($name == 'category'): ?>
	<div class="frp-taxonomies-all<?php echo ( $instance['taxonomy'] == 'category' ) ? ' hidden' : ''; ?>">
		<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<input type="hidden" class="frp-taxonomy" name="<?php print $this->get_field_name( 'taxonomy' ); ?>"
		   value="<?php print $instance['taxonomy']; ?>">
</div>
<h4 class="frp-form-group-title"><?php print __( 'Template' ); ?><img
		src="<?php print $plugin_dir . 'images/help.png'; ?>" title="<?php _e( 'Template for each post', 'frp' ); ?>"
		class="frp-form-help" alt="<?php _e( 'Help', 'frp' ); ?>"></h4>
<div class="frp-form-group">
	<div class="frp-template-icons">
		<div class="wp_themeSkin frp-form-button-title frp-form-button" title="<?php _e( 'Title' ); ?>">
			<a href="javascript:;" class="mceButton mceButtonEnabled">
				<span class="mceIcon"></span>
			</a>
		</div>
		<div class="wp_themeSkin frp-form-button-excerpt frp-form-button" title="<?php _e( 'Excerpt' ); ?>">
			<a href="javascript:;" class="mceButton mceButtonEnabled">
				<span class="mceIcon"></span>
			</a>
		</div>
		<div class="wp_themeSkin frp-form-button-date frp-form-button" title="<?php _e( 'Date' ); ?>">
			<a href="javascript:;" class="mceButton mceButtonEnabled">
				<span class="mceIcon"></span>
			</a>
		</div>
		<div class="wp_themeSkin frp-form-button-link frp-form-button" title="<?php _e( 'Link', 'frp' ); ?>">
			<a href="javascript:;" class="mceButton mceButtonEnabled">
				<span class="mceIcon"></span>
			</a>
		</div>
		<div class="wp_themeSkin frp-form-button-thumbnail frp-form-button" title="<?php _e( 'Thumbnail' ); ?>">
			<a href="javascript:;" class="mceButton mceButtonEnabled">
				<span class="mceIcon"></span>
			</a>
		</div>
		<div class="wp_themeSkin frp-form-button-author frp-form-button" title="<?php _e( 'Author' ); ?>">
			<a href="javascript:;" class="mceButton mceButtonEnabled">
				<span class="mceIcon"></span>
			</a>
		</div>
	</div>

	<textarea name="<?php print $this->get_field_name( 'template' ); ?>"
			  id="<?php print $this->get_field_id( 'template' ); ?>" cols="20" rows="16"
			  class="widefat frp-user-modified"><?php print $template; ?></textarea>
</div>