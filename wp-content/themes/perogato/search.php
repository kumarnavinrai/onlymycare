<?php get_header(); ?>
<div id="casing">

<div id="content">
<h2 class="pagetitle">Search Results</h2>
<?php if (have_posts()) : ?>

<?php while (have_posts()) : the_post(); ?>
		
<div class="single" id="post-<?php the_ID(); ?>">
<div class="title">

<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>


</div>
<div class="date">
<span class="author">Posted by <?php the_author(); ?></span> 
<span class="clock">On <?php the_time('F - j - Y'); ?></span>		</div>	

<div class="cover">
<div class="entry">

	<?php the_excerpt(); ?> 

<div class="clear"></div>
</div>
</div>

<div class="singleinfo">
<div class="category">Categories: <?php the_category(', '); ?> </div>
</div>
</div>

<?php endwhile; ?>
<div id="navigation">
<?php if(function_exists('wp_pagenavi')) : ?>
        <?php wp_pagenavi() ?>
 	   <?php else : ?>
        <div class="alignleft"><?php next_posts_link(__('&laquo; Older Entries','arclite')) ?></div>
        <div class="alignright"><?php previous_posts_link(__('Newer Entries &raquo;','arclite')) ?></div>
        <div class="clear"></div>
<?php endif; ?>

</div>

<?php else : ?>

<h1>Not Found</h1>

<div class="cover">
<div class="entry">
	<p>Sorry, no post matched your criteria. Try a different search?</p>
</div>

</div>
<?php endif; ?>

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>