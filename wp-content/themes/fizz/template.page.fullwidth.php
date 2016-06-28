<?php 
/*
 * Template Name: Page Fullwidth
 */
get_header(); ?>
			
			<div class="column-wide">
		<article>
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<header class="clearfix">				
					<ul class="meta">
						<li><?php echo get_the_date("M d / Y"); ?></li>
						<li><span class="comments"><?php comments_number('0','1','%'); ?> </span></li>
					</ul>
				</header>

				<?php if(has_post_thumbnail()): the_post_thumbnail('thumbnail', array('class'=>'blog-thumb')); ?><?php endif; ?>

				<div class="blog-entry">

					<span class="categories"><?php the_category(', '); ?></span>

					<h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					
				</div>
				<span class="clearfix"></span>

				<?php the_content(); ?>
			<?php 
			endwhile; 
			endif; 
			?>
			</article>


		</div>

<?php get_footer(); ?>