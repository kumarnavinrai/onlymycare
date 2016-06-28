<?php echo $before_widget; ?>
<div class="frp-widget-wrapper frp-widget-<?php print $instance['theme'] ?>">
	<?php
	if ( ! empty( $instance['title'] ) ) {
		echo $before_title . $instance['title'] . $after_title;

		if ( $instance['all_posts_link_title'] ):
			?>
            <a href="<?php print $term_link; ?>"
               class="frp-all-category-news frp-all-category-news-header"><?php print $instance['all_posts_title']; ?></a>
			<?php
		endif;
	}
	?>
    <div class="frp-clear"></div>
    <ul class="frp-widget">
		<?php while ( have_posts() ): the_post(); ?>
        <li class="frp-news">
			<?php echo wpautop( do_shortcode( $instance['template'] ) ); ?>
        </li>
		<?php endwhile; ?>
    </ul>
	<?php if ( $instance['all_posts_link_footer'] ): ?>
    <div class="frp-all-category-news frp-all-category-news-footer"><a
            href="<?php print $term_link; ?>"><?php print $instance['all_posts_title']; ?></a></div>
	<?php endif; ?>
</div>
<?php echo $after_widget; ?>