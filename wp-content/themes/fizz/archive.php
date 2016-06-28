<?php get_header(); ?>
			
			<div class="column-one">		
			
			
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">

				<header class="clearfix">				
					<ul class="meta">
						<li><?php echo get_the_date("M d / Y"); ?></li>
						<li><span class="comments"><?php comments_popup_link(__('0', 'site5framework'), __('1', 'site5framework'), __('%', 'site5framework')); ?> </span></li>
					</ul>
				</header>

				<?php if(has_post_thumbnail()): the_post_thumbnail('thumbnail', array('class'=>'blog-thumb')); ?><?php endif; ?>

				<div class="blog-entry">

					<span class="categories"><?php the_category(', '); ?></span>

					<h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

					<div id="post-content">
						<?php the_content(__("Continue Reading", "site5framework")); ?>
					</div>
				</div>

			</article>

			<?php endwhile; ?>

			<!-- begin #pagination -->
				<?php if (function_exists("emm_paginate")) { 
						emm_paginate();  
					 } else { ?>
				<div class="navigation">
			        <div class="alignleft"><?php next_posts_link('Older') ?></div>
			        <div class="alignright"><?php previous_posts_link('Newer') ?></div>
			    </div>
		    <?php } ?>
		    <!-- end #pagination -->


			<?php else: ?>

			<article>

				<div class="blog-entry">

					<h2 class="post-title"><?php _e("No Posts Yet", "site5framework"); ?></h2>

					<div id="post-content">
						<p><?php _e("Sorry, What you were looking for is not here.", "site5framework"); ?></p>
					</div>
				</div>

			</article>

			<?php endif; ?>


		</div><!-- end #column-one -->

		<div class="column-two">
			<?php get_sidebar('primary'); ?>
		</div><!-- end #column-two -->
<?php get_footer(); ?>