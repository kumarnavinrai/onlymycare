<?php get_header(); ?>
			
			<div class="column-one">

			<articlerole="article">

				<div class="blog-entry">

					<h2 class="post-title"> · <span class='colored'>404</span> <?php _e("Error", "site5framework"); ?> · </h2>

					<div id="post-content">
						<p><?php _e("The article you were looking for was not found, but maybe try looking again!", "site5framework"); ?></p>
					</div>
				</div>

			</article>

		</div><!-- end #column-one -->

		<div class="column-two">
			<?php get_sidebar(); ?>
		</div><!-- end #column-two -->
<?php get_footer(); ?>