<?php get_header(); ?>

<div class="grid_7">
    <div class="box_nobg">
<?php if (have_posts()) : ?>

 	  <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
 	  <?php /* If this is a category archive */ if (is_category()) { ?>
		<h4 class="search">Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category:</h4>
 	  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		<h4 class="search">Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h4>
 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h4 class="search">Archive for <?php the_time('F jS, Y'); ?>:</h4>
 	  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h4 class="search">Archive for <?php the_time('F, Y'); ?>:</h4>
 	  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h4 class="search">Archive for <?php the_time('Y'); ?>:</h4>
	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h4 class="search">Author Archive</h4>
 	  <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h4 class="search">Blog Archives</h4>
 	  <?php } ?>

		<?php while (have_posts()) : the_post(); ?>
        
        <div class="post" id="post-<?php the_ID(); ?>">   
			        <h3><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>             
                    <div class="date">Posted on <?php the_time('M d Y'); ?></div>        
       				<div class="postcontent"><?php limits(100, '<span class="readmore">Read More &raquo;</span>'); ?></div>
        			<div class="clear"></div><br />
        </div><!--post-->
        
<?php endwhile; endif; ?>

    </div>
</div>

<?php include (TEMPLATEPATH . '/pagebar.php'); ?>

<?php include (TEMPLATEPATH . '/bottom.php'); ?>
<?php get_footer(); ?>

