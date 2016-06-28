<?php get_header(); ?>
<?php include (TEMPLATEPATH . '/slide.php'); ?>
<div id="casing">	
<div id="content">
<?php if (have_posts()) : ?>
<?php $count = 0; ?>

<?php while (have_posts()) : the_post(); ?>
<div class="mini" id="post-<?php the_ID(); ?>">
<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>

<div class="cover">
<div class="entry">
<?php $preview = get_post_meta($post->ID, 'preview', $single = true); ?>
<img class="ethumb" src="<?php bloginfo('stylesheet_directory'); ?>/timthumb.php?src=<?php echo $preview; ?>&amp;h=120&amp;w=120&amp;zc=1" alt=""/> 

<?php the_excerpt(); ?> 

<div class="clear"></div>
</div>

</div>

<div class="singleinfo">

 <span class="more"><a href="<?php the_permalink() ?>" >READ MORE</a></span>
 <span class="comm"><?php comments_popup_link('0 Comment', '1 Comment', '% Comments'); ?></span>

</div>


</div>

<?php if(++$counter % 2 == 0) : ?>
<div class="clear"></div>
<?php endif; ?>


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

		<h1 class="title">Not Found</h1>
		<p>Sorry, but you are looking for something that isn't here.</p>

	<?php endif; ?>


</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
