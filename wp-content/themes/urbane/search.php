<?php get_header(); ?>

<div class="grid_7">
    <div class="box_nobg">
<?php if (have_posts()) : ?>
<h4 class="search">Search Results for "<?php echo $_GET['s']; ?>"</h4>

		<?php while (have_posts()) : the_post(); ?>
        
        <div class="post" id="post-<?php the_ID(); ?>">   
			        <h3><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>             
                    <div class="date">Posted on <?php the_time('M d Y'); ?></div>        
       				<div class="postcontent"><?php limits(100, '<span class="readmore">Read More &raquo;</span>'); ?></div>
        			<div class="clear"></div><br />
        </div><!--post-->
        
<?php endwhile; else: ?>
<h4 class="search">Search Results for "<?php echo $_GET['s']; ?>"</h4>
Nothing was found! Try again.
<?php endif; ?>

    </div>
</div>

<?php include (TEMPLATEPATH . '/pagebar.php'); ?>

<?php include (TEMPLATEPATH . '/bottom.php'); ?>
<?php get_footer(); ?>

