
<?php
	if(have_posts()) :
		$category = get_the_category();
		if ($category[0]->category_count == 1) :
			while (have_posts()) : the_post();
                        ?>
                        <script>
                        window.location.href = '<?php echo get_permalink($post->ID); ?>';
                        </script>
                         <?php
                        //wp_redirect(get_permalink($post->ID));
			endwhile;
		else :
			include(TEMPLATEPATH . '/index.php');
		endif;
	endif;
?>