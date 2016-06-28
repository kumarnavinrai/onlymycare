<?php get_header(); ?>

<div id="casing">	
<div id="content">

<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>

<div class="single" id="post-<?php the_ID(); ?>">
<div class="title">
<h2><?php the_title(); ?></h2>
<div class="date"><span class="author">Posted by <?php the_author(); ?></span> <span class="clock"> On <?php the_time('F - j - Y'); ?></span>	</div>	
</div>

<div class="cover">
<div class="entry">

<?php the_content('Read the rest of this entry &raquo;'); ?>
<?php include (TEMPLATEPATH . '/ad2.php'); ?>

<div class="clear"></div>
 <?php wp_link_pages(array('before' => '<p><strong>Pages: </strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
</div>

</div>

<div class="singleinfo">
Categories: <?php the_category(', '); ?> 
</div>


</div>
<?php comments_template(); ?>
<?php endwhile; else: ?>

		<h1 class="title">Not Found</h1>
		<p>I'm Sorry,  you are looking for something that is not here. Try a different search.</p>

<?php endif; ?>

</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>